<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\LelangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BarangController extends Controller
{
    /**
     * Menampilkan daftar semua barang.
     * Admin dan petugas melihat semua barang, penjual hanya melihat barang miliknya.
     * Pembeli melihat barang yang disetujui dan lelangnya dibuka.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
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

            $barang = $query->latest()->paginate($perPage);

            // Append query parameters to pagination links
            $barang->appends($request->query());

            return view('barang.index', compact('barang'));
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

            $barang = $query->latest()->paginate($perPage);

            // Append query parameters to pagination links
            $barang->appends($request->query());

            return view('barang.index', compact('barang'));
        } else {
            // PEMBELI - Logic baru untuk menampilkan barang yang disetujui dan lelangnya dibuka
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

            // Append query parameters to pagination links
            $barang->appends($request->query());

            // Proses foto untuk setiap barang (ambil foto pertama saja)
            $barang->getCollection()->transform(function ($item) {
                if ($item->foto) {
                    $photos = explode(',', $item->foto);
                    $item->foto_utama = $photos[0]; // Ambil foto pertama
                } else {
                    $item->foto_utama = null;
                }
                return $item;
            });

            // Ambil semua kategori untuk filter
            $kategori = KategoriModel::all();

            // Return view khusus untuk pembeli
            return view('pembeli.index', compact('barang', 'kategori'));
        }
    }

    /**
     * Menampilkan form untuk membuat barang baru.
     * Hanya penjual yang bisa membuat barang.
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->role !== 'penjual') {
            return redirect()->route('barang.index')->withErrors(['error' => 'Hanya penjual yang dapat membuat barang.']);
        }

        $kategori = KategoriModel::all();

        return view('barang.create', compact('kategori'));
    }

    /**
     * Menyimpan barang baru ke database.
     * Hanya penjual yang bisa membuat barang.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'penjual') {
            return redirect()->route('barang.index')->withErrors(['error' => 'Hanya penjual yang dapat membuat barang.']);
        }

        try {
            $validatedData = $request->validate([
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
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
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

        // Gabungkan nama file menjadi string yang dipisahkan koma (seperti PHP native Anda)
        $photoNames = !empty($uploadedPhotos) ? implode(',', $uploadedPhotos) : null;

        BarangModel::create([
            'nama_barang' => $validatedData['nama_barang'],
            'harga_awal' => $validatedData['harga_awal'],
            'lokasi' => $validatedData['lokasi'],
            'deskripsi' => $validatedData['deskripsi'],
            'kondisi' => $validatedData['kondisi'],
            'id_kategori' => $validatedData['id_kategori'],
            'status' => 'belum disetujui',
            'foto' => $photoNames, // Simpan sebagai string separated by comma
            'id_penjual' => $user->id,
        ]);

        return redirect()->route('penjual.barang.index')->with('success', 'Barang berhasil ditambahkan dan menunggu persetujuan!');
    }

    /**
     * Menampilkan detail barang tertentu.
     */
    public function show($id)
    {
        $barang = BarangModel::with(['kategori', 'penjual', 'lelang'])->find($id);

        if (!$barang) {
            return redirect()->route('barang.index')->withErrors(['barang' => 'Barang tidak ditemukan.']);
        }

        $user = Auth::user();

        // Penjual hanya bisa melihat barang miliknya sendiri (kecuali admin/petugas)
        if ($user->role === 'penjual' && $barang->id_penjual != $user->id) {
            return redirect()->route('barang.index')->withErrors(['error' => 'Anda hanya dapat melihat barang milik Anda sendiri.']);
        }

        return view('barang.detail', compact('barang'));
    }

    /**
     * Menampilkan form untuk mengedit barang.
     * Hanya penjual pemilik barang yang bisa edit.
     */
    public function edit($id)
    {
        $barang = BarangModel::with(['kategori'])->find($id);

        if (!$barang) {
            return redirect()->route('barang.index')->withErrors(['barang' => 'Barang tidak ditemukan.']);
        }

        $user = Auth::user();

        if ($user->role !== 'penjual') {
            return redirect()->route('barang.index')->withErrors(['error' => 'Hanya penjual yang dapat mengedit barang.']);
        }

        if ($barang->id_penjual != $user->id) {
            return redirect()->route('barang.index')->withErrors(['error' => 'Anda hanya dapat mengedit barang milik Anda sendiri.']);
        }

        $kategori = KategoriModel::all();

        return view('barang.edit', compact('barang', 'kategori'));
    }

    /**
     * Memperbarui barang tertentu di database.
     * Hanya penjual pemilik barang yang bisa edit.
     */
    public function update(Request $request, $id)
    {
        $barang = BarangModel::find($id);

        if (!$barang) {
            return redirect()->route('barang.index')->withErrors(['barang' => 'Barang tidak ditemukan.']);
        }

        $user = Auth::user();

        if ($user->role !== 'penjual') {
            return redirect()->route('barang.index')->withErrors(['error' => 'Hanya penjual yang dapat mengedit barang.']);
        }

        if ($barang->id_penjual != $user->id) {
            return redirect()->route('barang.index')->withErrors(['error' => 'Anda hanya dapat mengedit barang milik Anda sendiri.']);
        }

        try {
            $validatedData = $request->validate([
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
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $updateData = [
            'nama_barang' => $validatedData['nama_barang'],
            'harga_awal' => $validatedData['harga_awal'],
            'lokasi' => $validatedData['lokasi'],
            'deskripsi' => $validatedData['deskripsi'],
            'kondisi' => $validatedData['kondisi'],
            'id_kategori' => $validatedData['id_kategori'],
        ];

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

        $barang->update($updateData);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui!');
    }

    /**
     * Menghapus barang tertentu dari database.
     * Hanya admin dan petugas yang bisa menghapus.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'petugas'])) {
            return redirect()->route('barang.index')->withErrors(['error' => 'Hanya admin dan petugas yang dapat menghapus barang.']);
        }

        $barang = BarangModel::find($id);

        if (!$barang) {
            return redirect()->route('barang.index')->withErrors(['barang' => 'Barang tidak ditemukan.']);
        }

        // Cek apakah barang memiliki lelang terkait
        if ($barang->lelang()->count() > 0) {
            return redirect()->route('barang.index')->withErrors(['barang' => 'Barang tidak dapat dihapus karena masih memiliki lelang terkait.']);
        }

        // Hapus foto jika ada
        if ($barang->foto && Storage::disk('public')->exists($barang->foto)) {
            Storage::disk('public')->delete($barang->foto);
        }

        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus!');
    }

    /**
     * Menampilkan barang berdasarkan status.
     * Hanya admin dan petugas yang bisa mengakses.
     */
    public function getByStatus($status)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'petugas'])) {
            return redirect()->route('barang.index')->withErrors(['error' => 'Hanya admin dan petugas yang dapat melihat barang berdasarkan status.']);
        }

        $validStatus = ['belum disetujui', 'disetujui', 'ditolak'];

        if (!in_array($status, $validStatus)) {
            return redirect()->route('barang.index')->withErrors(['error' => 'Status tidak valid.']);
        }

        $barang = BarangModel::with(['kategori', 'penjual'])
            ->where('status', $status)
            ->latest()
            ->paginate(10);

        return view('barang.status', compact('barang', 'status'));
    }

    /**
     * Menampilkan barang berdasarkan kategori.
     */
    public function getByKategori($id_kategori)
    {
        $kategori = KategoriModel::find($id_kategori);

        if (!$kategori) {
            return redirect()->route('barang.index')->withErrors(['kategori' => 'Kategori tidak ditemukan.']);
        }

        $user = Auth::user();

        if (in_array($user->role, ['admin', 'petugas'])) {
            // Admin dan petugas melihat semua barang dari kategori
            $barang = BarangModel::with(['kategori', 'penjual'])
                ->where('id_kategori', $id_kategori)
                ->latest()
                ->paginate(10);
        } else {
            // User lain hanya melihat barang yang sudah disetujui
            $barang = BarangModel::with(['kategori', 'penjual'])
                ->where('id_kategori', $id_kategori)
                ->where('status', 'disetujui')
                ->latest()
                ->paginate(10);
        }

        return view('barang.kategori', compact('barang', 'kategori'));
    }

    /**
     * Memperbarui status barang.
     * Hanya admin dan petugas yang bisa mengakses.
     */
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'petugas'])) {
            return redirect()->route('barang.index')->withErrors(['error' => 'Hanya admin dan petugas yang dapat mengubah status barang.']);
        }

        $barang = BarangModel::find($id);

        if (!$barang) {
            return redirect()->route('barang.index')->withErrors(['barang' => 'Barang tidak ditemukan.']);
        }

        try {
            $validatedData = $request->validate([
                'status' => 'required|in:belum disetujui,disetujui,ditolak'
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        // Simpan status lama untuk pengecekan
        $statusLama = $barang->status;
        $statusBaru = $validatedData['status'];

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
                    // Buat data lelang baru
                    LelangModel::create([
                        'id_barang' => $barang->id_barang,
                        'tgl_dibuka' => now(),
                        'tgl_selesai' => null,
                        'harga_akhir' => 0,
                        'id_pembeli' => null,
                        'id_user' => null,
                        'status_dana' => 'belum diserahkan',
                        'id_petugas' => $user->id,
                        'status' => 'dibuka'
                    ]);
                }
            }

            // Commit transaction
            DB::commit();

            $message = 'Status barang berhasil diperbarui';
            if ($statusBaru === 'disetujui' && $statusLama !== 'disetujui') {
                $message .= ' dan lelang telah dibuat otomatis';
            }

            return redirect()->route('barang.index')->with('success', $message);
        } catch (\Exception $e) {
            // Rollback transaction jika ada error
            DB::rollback();

            return redirect()->route('barang.index')->withErrors(['error' => 'Terjadi kesalahan saat memperbarui status barang.']);
        }
    }

    /**
     * Menampilkan form untuk mengubah status barang.
     * Hanya admin dan petugas yang bisa mengakses.
     */
    public function editStatus($id)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'petugas'])) {
            return redirect()->route('barang.index')->withErrors(['error' => 'Hanya admin dan petugas yang dapat mengubah status barang.']);
        }

        $barang = BarangModel::with(['kategori', 'penjual'])->find($id);

        if (!$barang) {
            return redirect()->route('barang.index')->withErrors(['barang' => 'Barang tidak ditemukan.']);
        }

        return view('barang.index', compact('barang'));
    }

    public function showDetailForPembeli($id)
{
    $user = Auth::user();
    $barang = BarangModel::with(['kategori', 'penjual', 'lelang'])->find($id);

    if (!$barang) {
        return redirect()->route('pembeli.index')->withErrors(['error' => 'Barang tidak ditemukan.']);
    }

    // Cek apakah barang disetujui dan lelang dibuka
    if ($barang->status !== 'disetujui' || !$barang->lelang || $barang->lelang->status !== 'dibuka') {
        return redirect()->route('pembeli.index')->withErrors(['error' => 'Barang tidak tersedia untuk dilelang.']);
    }

    // Ambil tawaran tertinggi
    $tawaranTertinggi = DB::table('penawaran')
        ->where('id_barang', $id)
        ->max('penawaran_harga');

    // Cek apakah user sudah pernah menawar
    $sudahMenawar = DB::table('penawaran')
        ->where('id_barang', $id)
        ->where('id_pembeli', $user->id)
        ->exists();

    // Format lokasi
    $lokasiTampil = match (strtolower(trim($barang->lokasi))) {
        'bandung' => 'Telkom University Bandung',
        'surabaya' => 'Telkom University Surabaya',
        'jakarta' => 'Telkom University Jakarta',
        default => $barang->lokasi,
    };

    // Ambil foto barang
    $fotoArray = $barang->foto ? explode(',', $barang->foto) : [];
    $mainImage = count($fotoArray) > 0 ? trim($fotoArray[0]) : 'default-product.png';

    return view('pembeli.detailbarang', compact(
        'barang',
        'tawaranTertinggi',
        'sudahMenawar',
        'lokasiTampil',
        'fotoArray',
        'mainImage'
    ));
}
}
