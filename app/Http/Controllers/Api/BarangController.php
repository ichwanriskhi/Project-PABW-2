<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BarangCollection;
use App\Http\Resources\BarangResource;
use App\Models\BarangModel;
use App\Models\LelangModel;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    /**
     * Menampilkan daftar semua barang.
     * Admin dan petugas melihat semua barang, penjual hanya melihat barang miliknya.
     * Pembeli melihat barang yang disetujui dan lelangnya dibuka.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user('api');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda harus login terlebih dahulu'
            ], 401);
        }

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $status = $request->get('status');

        // Filter tambahan untuk pembeli
        $kategori_filter = $request->get('kategori');
        $kondisi_filter = $request->get('kondisi');
        $lokasi_filter = $request->get('lokasi');
        $harga_min = $request->get('harga_min');
        $harga_max = $request->get('harga_max');

        if (in_array($user->role, ['admin', 'petugas'])) {
            // Admin dan petugas melihat semua barang
            $query = BarangModel::with(['kategori', 'penjual']);

            // Filter berdasarkan status jika ada
            if ($status && in_array($status, ['disetujui', 'belum disetujui', 'ditolak'])) {
                $query->where('status', $status);
            }

            // Filter berdasarkan pencarian
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_barang', 'like', '%' . $search . '%')
                        ->orWhere('lokasi', 'like', '%' . $search . '%')
                        ->orWhereHas('kategori', function ($subQ) use ($search) {
                            $subQ->where('nama_kategori', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('penjual', function ($subQ) use ($search) {
                            $subQ->where('nama', 'like', '%' . $search . '%');
                        });
                });
            }
        } elseif ($user->role === 'penjual') {
            // Penjual hanya melihat barang miliknya
            $query = BarangModel::with(['kategori', 'penjual'])
                ->where('id_penjual', $user->id);

            // Filter berdasarkan status jika ada
            if ($status && in_array($status, ['disetujui', 'belum disetujui', 'ditolak'])) {
                $query->where('status', $status);
            }

            // Filter berdasarkan pencarian
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_barang', 'like', '%' . $search . '%')
                        ->orWhere('lokasi', 'like', '%' . $search . '%')
                        ->orWhereHas('kategori', function ($subQ) use ($search) {
                            $subQ->where('nama_kategori', 'like', '%' . $search . '%');
                        });
                });
            }
        } else {
            // PEMBELI - Barang yang disetujui dan lelangnya dibuka
            $query = BarangModel::with(['kategori', 'penjual', 'lelang'])
                ->where('status', 'disetujui')
                ->whereHas('lelang', function ($q) {
                    $q->where('status', 'dibuka');
                });

            // Filter berdasarkan pencarian
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_barang', 'like', '%' . $search . '%')
                        ->orWhere('lokasi', 'like', '%' . $search . '%')
                        ->orWhere('deskripsi', 'like', '%' . $search . '%')
                        ->orWhereHas('kategori', function ($subQ) use ($search) {
                            $subQ->where('nama_kategori', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('penjual', function ($subQ) use ($search) {
                            $subQ->where('nama', 'like', '%' . $search . '%');
                        });
                });
            }

            // Filter berdasarkan kategori
            if ($kategori_filter) {
                $query->where('id_kategori', $kategori_filter);
            }

            // Filter berdasarkan kondisi
            if ($kondisi_filter && in_array($kondisi_filter, ['Baru', 'Bekas'])) {
                $query->where('kondisi', $kondisi_filter);
            }

            // Filter berdasarkan lokasi
            if ($lokasi_filter) {
                $query->where('lokasi', 'like', '%' . $lokasi_filter . '%');
            }

            // Filter berdasarkan range harga
            if ($harga_min && is_numeric($harga_min)) {
                $query->where('harga_awal', '>=', $harga_min);
            }

            if ($harga_max && is_numeric($harga_max)) {
                $query->where('harga_awal', '<=', $harga_max);
            }

            // Untuk pembeli gunakan pagination yang lebih besar
            $perPage = 21;
        }

        $barang = $query->latest()->paginate($perPage);

        // Proses foto untuk pembeli (ambil foto pertama saja)
        if ($user->role === 'pembeli') {
            $barang->getCollection()->transform(function ($item) {
                if ($item->foto) {
                    $photos = explode(',', $item->foto);
                    $item->foto_utama = $photos[0]; // Ambil foto pertama
                } else {
                    $item->foto_utama = null;
                }
                return $item;
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar data barang',
            'data' => new BarangCollection($barang)
        ], 200);
    }

    /**
     * Menyimpan barang baru.
     * Hanya role penjual yang bisa membuat barang.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validasi role - hanya penjual yang bisa membuat barang
        $user = $request->user('api');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda harus login terlebih dahulu'
            ], 401);
        }

        if ($user->role !== 'penjual') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Hanya penjual yang dapat membuat barang'
            ], 403);
        }

        // Validasi input - Updated untuk multiple photos
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required|string|max:255',
            'harga_awal' => 'required|numeric|min:0',
            'lokasi' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kondisi' => 'required|in:Baru,Bekas',
            'id_kategori' => 'required|string|exists:kategori,id_kategori',
            // Validasi untuk multiple foto (maksimal 5 foto)
            'foto' => 'nullable|array|max:5',
            'foto.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle upload multiple foto
        $uploadedPhotos = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $foto) {
                $filename = time() . '_' . uniqid() . '_' . $foto->getClientOriginalName();
                $fotoPath = $foto->storeAs('barang', $filename, 'public');
                $uploadedPhotos[] = $fotoPath;
            }
        }

        // Gabungkan nama file menjadi string yang dipisahkan koma
        $photoNames = !empty($uploadedPhotos) ? implode(',', $uploadedPhotos) : null;

        // Membuat barang baru dengan id_penjual dari user yang login
        $barang = BarangModel::create([
            'nama_barang' => $request->nama_barang,
            'harga_awal' => $request->harga_awal,
            'lokasi' => $request->lokasi,
            'deskripsi' => $request->deskripsi,
            'kondisi' => $request->kondisi,
            'id_kategori' => $request->id_kategori,
            'status' => 'belum disetujui', // Default status
            'foto' => $photoNames, // Simpan sebagai string separated by comma
            'id_penjual' => $user->id, // Menggunakan ID user yang login
        ]);

        $barang->load(['kategori', 'penjual']);

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil ditambahkan',
            'data' => new BarangResource($barang)
        ], 201);
    }

    /**
     * Menampilkan detail barang tertentu.
     * Semua user yang login bisa mengakses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $user = $request->user('api');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda harus login terlebih dahulu'
            ], 401);
        }

        $barang = BarangModel::with(['kategori', 'penjual', 'lelang'])->find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }

        // Penjual hanya bisa melihat barang miliknya sendiri (kecuali admin/petugas)
        if ($user->role === 'penjual' && $barang->id_penjual != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda hanya dapat melihat barang milik Anda sendiri'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail barang',
            'data' => new BarangResource($barang)
        ], 200);
    }

    /**
     * Memperbarui barang tertentu.
     * Hanya penjual pemilik barang yang bisa edit.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validasi role - hanya penjual yang bisa edit barang
        $user = $request->user('api');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda harus login terlebih dahulu'
            ], 401);
        }

        if ($user->role !== 'penjual') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Hanya penjual yang dapat mengedit barang'
            ], 403);
        }

        // Cek apakah barang ada
        $barang = BarangModel::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }

        // Cek apakah user adalah pemilik barang
        if ($barang->id_penjual != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda hanya dapat mengedit barang milik Anda sendiri'
            ], 403);
        }

        // Validasi input - Updated untuk multiple photos
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'sometimes|required|string|max:255',
            'harga_awal' => 'sometimes|required|numeric|min:0',
            'lokasi' => 'sometimes|required|string|max:255',
            'deskripsi' => 'sometimes|required|string',
            'kondisi' => 'sometimes|required|in:Baru,Bekas',
            'id_kategori' => 'sometimes|required|string|exists:kategori,id_kategori',
            // Validasi untuk multiple foto (maksimal 5 foto)
            'foto' => 'nullable|array|max:5',
            'foto.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only([
            'nama_barang',
            'harga_awal',
            'lokasi',
            'deskripsi',
            'kondisi',
            'id_kategori'
        ]);

        // Handle upload multiple foto baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($barang->foto) {
                $oldPhotos = explode(',', $barang->foto);
                foreach ($oldPhotos as $oldPhoto) {
                    if (Storage::disk('public')->exists($oldPhoto)) {
                        Storage::disk('public')->delete($oldPhoto);
                    }
                }
            }

            // Upload foto baru
            $uploadedPhotos = [];
            foreach ($request->file('foto') as $foto) {
                $filename = time() . '_' . uniqid() . '_' . $foto->getClientOriginalName();
                $fotoPath = $foto->storeAs('barang', $filename, 'public');
                $uploadedPhotos[] = $fotoPath;
            }

            // Gabungkan nama file menjadi string yang dipisahkan koma
            $photoNames = implode(',', $uploadedPhotos);
            $updateData['foto'] = $photoNames;
        }

        // Update barang
        $barang->update($updateData);
        $barang->load(['kategori', 'penjual']);

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil diperbarui',
            'data' => new BarangResource($barang)
        ], 200);
    }

    /**
     * Menghapus barang tertentu.
     * Hanya admin dan petugas yang bisa menghapus.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // Validasi role - hanya admin dan petugas yang bisa menghapus
        $user = $request->user('api');
        if (!$user || !in_array($user->role, ['admin', 'petugas'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Hanya admin dan petugas yang dapat menghapus barang'
            ], 403);
        }

        // Cek apakah barang ada
        $barang = BarangModel::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }

        // Cek apakah barang memiliki lelang terkait
        if ($barang->lelang()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak dapat dihapus karena masih memiliki lelang terkait'
            ], 422);
        }

        // Hapus multiple foto jika ada
        if ($barang->foto) {
            $photos = explode(',', $barang->foto);
            foreach ($photos as $photo) {
                if (Storage::disk('public')->exists($photo)) {
                    Storage::disk('public')->delete($photo);
                }
            }
        }

        // Hapus barang
        $barang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil dihapus'
        ], 200);
    }

    /**
     * Menampilkan barang berdasarkan status.
     * Hanya admin dan petugas yang bisa mengakses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $status
     * @return \Illuminate\Http\Response
     */
    public function getByStatus(Request $request, $status)
    {
        // Validasi role - hanya admin dan petugas yang bisa mengakses
        $user = $request->user('api');
        if (!$user || !in_array($user->role, ['admin', 'petugas'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Hanya admin dan petugas yang dapat melihat barang berdasarkan status'
            ], 403);
        }

        $validStatus = ['belum disetujui', 'disetujui', 'ditolak'];

        if (!in_array($status, $validStatus)) {
            return response()->json([
                'success' => false,
                'message' => 'Status tidak valid'
            ], 400);
        }

        $barang = BarangModel::with(['kategori', 'penjual'])
            ->where('status', $status)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => "Daftar barang dengan status {$status}",
            'data' => new BarangCollection($barang)
        ], 200);
    }

    /**
     * Menampilkan barang berdasarkan kategori.
     * Semua user yang login bisa mengakses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id_kategori
     * @return \Illuminate\Http\Response
     */
    public function getByKategori(Request $request, $id_kategori)
    {
        $user = $request->user('api');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda harus login terlebih dahulu'
            ], 401);
        }

        $kategori = KategoriModel::find($id_kategori);
        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        if (in_array($user->role, ['admin', 'petugas'])) {
            // Admin dan petugas melihat semua barang dari kategori
            $barang = BarangModel::with(['kategori', 'penjual'])
                ->where('id_kategori', $id_kategori)
                ->latest()
                ->paginate(10);
        } elseif ($user->role === 'penjual') {
            // Penjual hanya melihat barang miliknya sendiri dari kategori
            $barang = BarangModel::with(['kategori', 'penjual'])
                ->where('id_kategori', $id_kategori)
                ->where('id_penjual', $user->id)
                ->latest()
                ->paginate(10);
        } else {
            // Pembeli hanya melihat barang yang disetujui dan lelangnya dibuka
            $barang = BarangModel::with(['kategori', 'penjual', 'lelang'])
                ->where('id_kategori', $id_kategori)
                ->where('status', 'disetujui')
                ->whereHas('lelang', function ($q) {
                    $q->where('status', 'dibuka');
                })
                ->latest()
                ->paginate(10);

            // Proses foto untuk pembeli (ambil foto pertama saja)
            $barang->getCollection()->transform(function ($item) {
                if ($item->foto) {
                    $photos = explode(',', $item->foto);
                    $item->foto_utama = $photos[0]; // Ambil foto pertama
                } else {
                    $item->foto_utama = null;
                }
                return $item;
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar barang berdasarkan kategori',
            'data' => new BarangCollection($barang)
        ], 200);
    }

    /**
     * Menampilkan barang milik penjual.
     * Penjual hanya bisa melihat barang miliknya sendiri.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getMyBarang(Request $request)
    {
        // Validasi role - hanya penjual yang bisa mengakses
        $user = $request->user('api');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda harus login terlebih dahulu'
            ], 401);
        }

        if ($user->role !== 'penjual') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Hanya penjual yang dapat mengakses endpoint ini'
            ], 403);
        }

        $barang = BarangModel::with(['kategori', 'penjual'])
            ->where('id_penjual', $user->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Daftar barang milik Anda',
            'data' => new BarangCollection($barang)
        ], 200);
    }

    /**
     * Memperbarui status barang.
     * Hanya admin dan petugas yang bisa mengakses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        // Validasi role - hanya admin dan petugas yang bisa mengakses
        $user = $request->user('api');
        if (!$user || !in_array($user->role, ['admin', 'petugas'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Hanya admin dan petugas yang dapat mengubah status barang'
            ], 403);
        }

        // Cek apakah barang ada
        $barang = BarangModel::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:belum disetujui,disetujui,ditolak'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Simpan status lama untuk pengecekan
        $statusLama = $barang->status;
        $statusBaru = $request->status;

        try {
            // Mulai database transaction
            DB::beginTransaction();

            // Update status barang
            $barang->update([
                'status' => $statusBaru
            ]);

            // Jika status diubah menjadi "disetujui", buat data lelang otomatis
            if ($statusBaru === 'disetujui' && $statusLama !== 'disetujui') {
                // Cek apakah sudah ada lelang untuk barang ini
                $existingLelang = LelangModel::where('id_barang', $barang->id_barang)->first();

                if (!$existingLelang) {
                    // Buat data lelang baru menggunakan LelangModel
                    LelangModel::create([
                        'id_barang' => $barang->id_barang,
                        'tgl_dibuka' => now(),
                        'tgl_selesai' => null,
                        'harga_akhir' => 0,
                        'id_pembeli' => null,
                        'id_user' => null,
                        'status_dana' => 'belum diserahkan',
                        'id_petugas' => $user->id, // ID petugas yang menyetujui
                        'status' => 'dibuka'
                    ]);
                }
            }

            // Commit transaction
            DB::commit();

            $barang->load(['kategori', 'penjual']);

            $message = 'Status barang berhasil diperbarui';
            if ($statusBaru === 'disetujui' && $statusLama !== 'disetujui') {
                $message .= ' dan lelang telah dibuat otomatis';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => new BarangResource($barang)
            ], 200);
        } catch (\Exception $e) {
            // Rollback transaction jika ada error
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui status barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan barang yang sudah disetujui untuk publik.
     * Endpoint untuk menampilkan barang di marketplace.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getApprovedBarang(Request $request)
    {
        $barang = BarangModel::with(['kategori', 'penjual'])
            ->where('status', 'disetujui')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Daftar barang yang sudah disetujui',
            'data' => new BarangCollection($barang)
        ], 200);
    }

    /**
     * Menampilkan detail barang untuk pembeli.
     * Khusus untuk pembeli melihat detail barang yang akan dilelang.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function showDetailForPembeli(Request $request, $id)
    {
        $user = $request->user('api');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda harus login terlebih dahulu'
            ], 401);
        }

        $barang = BarangModel::with(['kategori', 'penjual', 'lelang'])->find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }

        // Cek apakah barang disetujui dan lelang dibuka
        if ($barang->status !== 'disetujui' || !$barang->lelang || $barang->lelang->status !== 'dibuka') {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak tersedia untuk dilelang'
            ], 400);
        }

        // Ambil tawaran tertinggi
        $tawaranTertinggi = DB::table('penawaran')
            ->where('id_lelang', $barang->lelang->id_lelang)
            ->max('penawaran_harga');

        // Format tawaran tertinggi
        $tawaranTertinggiTampil = $tawaranTertinggi ? "Rp " . number_format($tawaranTertinggi, 0, ',', '.') : "Belum ada tawaran";

        // Cek apakah user sudah pernah menawar
        $sudahMenawar = DB::table('penawaran')
            ->where('id_lelang', $barang->lelang->id_lelang)
            ->where('id_pembeli', $user->id)
            ->exists();

        // Ambil semua penawaran untuk lelang ini dengan data pembeli
        $penawaran = DB::table('penawaran')
            ->join('pengguna', 'penawaran.id_pembeli', '=', 'pengguna.id')
            ->where('id_lelang', $barang->lelang->id_lelang)
            ->orderBy('penawaran_harga', 'desc')
            ->select([
                'penawaran.id_penawaran',
                'penawaran.id_pembeli',
                'pengguna.nama',
                'pengguna.foto as foto_pembeli',
                'penawaran.penawaran_harga',
                'penawaran.waktu',
                'penawaran.status_tawar'
            ])
            ->get();

        // Format lokasi untuk tampilan yang lebih user-friendly
        $lokasiTampil = match (strtolower(trim($barang->lokasi))) {
            'bandung' => 'Telkom University Bandung',
            'surabaya' => 'Telkom University Surabaya',
            'jakarta' => 'Telkom University Jakarta',
            default => $barang->lokasi,
        };

        // Process foto - convert comma-separated string to array
        $fotoArray = $barang->foto ? explode(',', $barang->foto) : [];
        $mainImage = count($fotoArray) > 0 ? trim($fotoArray[0]) : 'default-product.png';

        // Prepare response data
        $responseData = new BarangResource($barang);
        $responseArray = $responseData->toArray($request);

        // Add additional data for pembeli
        $responseArray['tawaran_tertinggi'] = $tawaranTertinggiTampil;
        $responseArray['sudah_menawar'] = $sudahMenawar;
        $responseArray['lokasi_tampil'] = $lokasiTampil;
        $responseArray['foto_array'] = array_map('trim', $fotoArray);
        $responseArray['main_image'] = $mainImage;
        $responseArray['riwayat_penawaran'] = $penawaran;

        // Add lelang information
        if ($barang->lelang) {
            $responseArray['lelang'] = [
                'id_lelang' => $barang->lelang->id_lelang,
                'tgl_dibuka' => $barang->lelang->tgl_dibuka,
                'tgl_selesai' => $barang->lelang->tgl_selesai,
                'status' => $barang->lelang->status,
                'harga_akhir' => $barang->lelang->harga_akhir,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail barang untuk pembeli',
            'data' => $responseArray
        ], 200);
    }

    /**
     * Menampilkan barang yang sudah disetujui untuk publik (pembeli).
     * Endpoint untuk menampilkan barang di marketplace untuk pembeli.
     * Hanya menampilkan barang yang disetujui dan lelangnya dibuka.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getApprovedBarangForPembeli(Request $request)
    {
        $user = $request->user('api');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda harus login terlebih dahulu'
            ], 401);
        }

        $search = $request->get('search');
        $kategori_filter = $request->get('kategori');
        $kondisi_filter = $request->get('kondisi');
        $lokasi_filter = $request->get('lokasi');
        $harga_min = $request->get('harga_min');
        $harga_max = $request->get('harga_max');

        // Query untuk pembeli - hanya barang yang disetujui dan lelangnya dibuka
        $query = BarangModel::with(['kategori', 'penjual', 'lelang'])
            ->where('status', 'disetujui')
            ->whereHas('lelang', function ($q) {
                $q->where('status', 'dibuka');
            });

        // Filter berdasarkan pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', '%' . $search . '%')
                    ->orWhere('lokasi', 'like', '%' . $search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $search . '%')
                    ->orWhereHas('kategori', function ($subQ) use ($search) {
                        $subQ->where('nama_kategori', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('penjual', function ($subQ) use ($search) {
                        $subQ->where('nama', 'like', '%' . $search . '%');
                    });
            });
        }

        // Filter berdasarkan kategori
        if ($kategori_filter) {
            $query->where('id_kategori', $kategori_filter);
        }

        // Filter berdasarkan kondisi
        if ($kondisi_filter && in_array($kondisi_filter, ['Baru', 'Bekas'])) {
            $query->where('kondisi', $kondisi_filter);
        }

        // Filter berdasarkan lokasi
        if ($lokasi_filter) {
            $query->where('lokasi', 'like', '%' . $lokasi_filter . '%');
        }

        // Filter berdasarkan range harga
        if ($harga_min && is_numeric($harga_min)) {
            $query->where('harga_awal', '>=', $harga_min);
        }

        if ($harga_max && is_numeric($harga_max)) {
            $query->where('harga_awal', '<=', $harga_max);
        }

        // Untuk pembeli gunakan pagination yang lebih besar (21 items per page)
        $barang = $query->latest()->paginate(21);

        // Process foto untuk setiap barang (ambil foto pertama saja)
        $barang->getCollection()->transform(function ($item) {
            if ($item->foto) {
                $photos = explode(',', $item->foto);
                $item->foto_utama = trim($photos[0]); // Ambil foto pertama
            } else {
                $item->foto_utama = 'default-product.png';
            }
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar barang yang tersedia untuk dilelang',
            'data' => new BarangCollection($barang),
            'filters' => [
                'search' => $search,
                'kategori' => $kategori_filter,
                'kondisi' => $kondisi_filter,
                'lokasi' => $lokasi_filter,
                'harga_min' => $harga_min,
                'harga_max' => $harga_max
            ]
        ], 200);
    }

    /**
     * Get all categories for filter dropdown
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getKategoriForFilter(Request $request)
    {
        $user = $request->user('api');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda harus login terlebih dahulu'
            ], 401);
        }

        $kategori = KategoriModel::all(['id_kategori', 'nama_kategori']);

        return response()->json([
            'success' => true,
            'message' => 'Daftar kategori',
            'data' => $kategori
        ], 200);
    }
}
