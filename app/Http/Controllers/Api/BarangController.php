<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BarangCollection;
use App\Http\Resources\BarangResource;
use App\Models\BarangModel;
use App\Models\LelangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    /**
     * Menampilkan daftar semua barang.
     * Hanya admin dan petugas yang bisa mengakses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Validasi role - hanya admin dan petugas yang bisa mengakses
        $user = $request->user('api');
        if (!$user || !in_array($user->role, ['admin', 'petugas'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Akses ditolak. Hanya admin dan petugas yang dapat melihat semua barang'
            ], 403);
        }

        $barang = BarangModel::with(['kategori', 'penjual'])->latest()->paginate(10);

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

        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required|string|max:255',
            'harga_awal' => 'required|numeric|min:0',
            'lokasi' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kondisi' => 'required|in:Baru,Bekas',
            'id_kategori' => 'required|string|exists:kategori,id_kategori',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle upload foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = time() . '_' . $foto->getClientOriginalName();
            $fotoPath = $foto->storeAs('barang', $filename, 'public');
        }

        // Membuat barang baru dengan id_penjual dari user yang login
        $barang = BarangModel::create([
            'nama_barang' => $request->nama_barang,
            'harga_awal' => $request->harga_awal,
            'lokasi' => $request->lokasi,
            'deskripsi' => $request->deskripsi,
            'kondisi' => $request->kondisi,
            'id_kategori' => $request->id_kategori,
            'status' => 'belum disetujui', // Default status
            'foto' => $fotoPath ?? null,
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

        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'sometimes|required|string|max:255',
            'harga_awal' => 'sometimes|required|numeric|min:0',
            'lokasi' => 'sometimes|required|string|max:255',
            'deskripsi' => 'sometimes|required|string',
            'kondisi' => 'sometimes|required|in:Baru,Bekas - Sangat Baik,Bekas - Baik,Bekas - Cukup',
            'id_kategori' => 'sometimes|required|integer|exists:kategori,id_kategori',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
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

        // Handle upload foto baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($barang->foto && Storage::disk('public')->exists($barang->foto)) {
                Storage::disk('public')->delete($barang->foto);
            }

            $foto = $request->file('foto');
            $filename = time() . '_' . $foto->getClientOriginalName();
            $fotoPath = $foto->storeAs('barang', $filename, 'public');
            $updateData['foto'] = $fotoPath;
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

        // Hapus foto jika ada
        if ($barang->foto && Storage::disk('public')->exists($barang->foto)) {
            Storage::disk('public')->delete($barang->foto);
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

        $barang = BarangModel::with(['kategori', 'penjual'])
            ->where('id_kategori', $id_kategori)
            ->where('status', 'disetujui') // Hanya tampilkan barang yang sudah disetujui
            ->latest()
            ->paginate(10);

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

    // /**
    //  * Memperbarui status barang.
    //  * Hanya admin dan petugas yang bisa mengakses.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  string  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function updateStatus(Request $request, $id)
    // {
    //     // Validasi role - hanya admin dan petugas yang bisa mengakses
    //     $user = $request->user('api');
    //     if (!$user || !in_array($user->role, ['admin', 'petugas'])) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Unauthorized - Hanya admin dan petugas yang dapat mengubah status barang'
    //         ], 403);
    //     }

    //     // Cek apakah barang ada
    //     $barang = BarangModel::find($id);

    //     if (!$barang) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Barang tidak ditemukan'
    //         ], 404);
    //     }

    //     // Validasi input
    //     $validator = Validator::make($request->all(), [
    //         'status' => 'required|in:belum disetujui,disetujui,ditolak'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validasi gagal',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     // Update status barang
    //     $barang->update([
    //         'status' => $request->status
    //     ]);

    //     $barang->load(['kategori', 'penjual']);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Status barang berhasil diperbarui',
    //         'data' => new BarangResource($barang)
    //     ], 200);
    // }

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
}
