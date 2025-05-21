<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KategoriCollection;
use App\Http\Resources\KategoriResource;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class KategoriController extends Controller
{
    /**
     * Menampilkan daftar semua kategori.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Validasi role - hanya admin dan petugas yang bisa mengakses
        $user = $request->user('api');
        if (!$user || !in_array($user->role, ['admin', 'petugas'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Akses ditolak'
            ], 403);
        }
        
        $kategori = KategoriModel::latest()->paginate(10);
        
        return response()->json([
            'success' => true,
            'message' => 'Daftar data kategori',
            'data' => new KategoriCollection($kategori)
        ], 200);
    }

    /**
     * Menyimpan kategori baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validasi role - hanya admin dan petugas yang bisa mengakses
        $user = $request->user('api');
        if (!$user || !in_array($user->role, ['admin', 'petugas'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Akses ditolak'
            ], 403);
        }
        
        // Validasi input
        $validator = Validator::make($request->all(), [
            'id_kategori' => 'required|string|unique:kategori,id_kategori',
            'nama_kategori' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Membuat kategori baru
        $kategori = KategoriModel::create([
            'id_kategori' => $request->id_kategori,
            'nama_kategori' => $request->nama_kategori,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan',
            'data' => new KategoriResource($kategori)
        ], 201);
    }

    /**
     * Menampilkan detail kategori tertentu.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // Validasi role - hanya admin dan petugas yang bisa mengakses
        $user = $request->user('api');
        if (!$user || !in_array($user->role, ['admin', 'petugas'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Akses ditolak'
            ], 403);
        }
        
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail kategori',
            'data' => new KategoriResource($kategori)
        ], 200);
    }

    /**
     * Memperbarui kategori tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validasi role - hanya admin dan petugas yang bisa mengakses
        $user = $request->user('api');
        if (!$user || !in_array($user->role, ['admin', 'petugas'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Akses ditolak'
            ], 403);
        }
        
        // Cek apakah kategori ada
        $kategori = KategoriModel::find($id);
        
        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update kategori
        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui',
            'data' => new KategoriResource($kategori)
        ], 200);
    }

    /**
     * Menghapus kategori tertentu.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // Validasi role - hanya admin dan petugas yang bisa mengakses
        $user = $request->user('api');
        if (!$user || !in_array($user->role, ['admin', 'petugas'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Akses ditolak'
            ], 403);
        }
        
        // Cek apakah kategori ada
        $kategori = KategoriModel::find($id);
        
        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        // Cek apakah kategori memiliki barang terkait
        if ($kategori->barang()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak dapat dihapus karena masih memiliki barang terkait'
            ], 422);
        }

        // Hapus kategori
        $kategori->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus'
        ]);
    }
}