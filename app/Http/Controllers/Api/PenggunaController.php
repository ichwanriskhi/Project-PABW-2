<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PenggunaCollection;
use App\Http\Resources\PenggunaResource;
use App\Models\PenggunaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PenggunaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Hanya admin yang bisa melihat semua pengguna
        $user = $request->user('api');
        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Akses ditolak'
            ], 403);
        }

        $pengguna = PenggunaModel::latest()->paginate(10);
        
        return response()->json([
            'success' => true,
            'message' => 'Daftar pengguna berhasil diambil',
            'data' => new PenggunaCollection($pengguna)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Hanya admin yang bisa menambah pengguna baru
        $user = $request->user('api');
        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Akses ditolak'
            ], 403);
        }

        try {
            $validatedData = $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'required|email|unique:pengguna',
                'password' => 'required|min:6',
                'telepon' => 'nullable|string|max:15',
                'alamat' => 'nullable|string',
                'bank' => 'nullable|string|max:50',
                'no_rekening' => 'nullable|string|max:30',
                'role' => 'required|in:petugas',
                'foto' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid',
                'errors' => $e->errors(),
            ], 422);
        }

        $validatedData['password'] = bcrypt($validatedData['password']);
        $pengguna = PenggunaModel::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil ditambahkan',
            'data' => new PenggunaResource($pengguna)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pengguna = PenggunaModel::find($id);
        
        if (!$pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail pengguna berhasil diambil',
            'data' => new PenggunaResource($pengguna)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Hanya admin yang bisa update pengguna lain
        $currentUser = $request->user('api');
        if (!$currentUser || ($currentUser->role !== 'admin' && $currentUser->id != $id)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Akses ditolak'
            ], 403);
        }

        $pengguna = PenggunaModel::find($id);
        
        if (!$pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }

        try {
            $validatedData = $request->validate([
                'nama' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:pengguna,email,'.$id.',id',
                'telepon' => 'nullable|string|max:15',
                'alamat' => 'nullable|string',
                'bank' => 'nullable|string|max:50',
                'no_rekening' => 'nullable|string|max:30',
                'role' => 'nullable|in:petugas',
                'foto' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid',
                'errors' => $e->errors(),
            ], 422);
        }

        // Hanya admin yang bisa mengubah role
        if (isset($validatedData['role']) && $currentUser->role !== 'admin') {
            unset($validatedData['role']);
        }

        $pengguna->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil diperbarui',
            'data' => new PenggunaResource($pengguna)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // Hanya admin yang bisa menghapus pengguna
        $currentUser = $request->user('api');
        if (!$currentUser || $currentUser->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Akses ditolak'
            ], 403);
        }

        $pengguna = PenggunaModel::find($id);
        
        if (!$pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }

        // Mencegah admin menghapus dirinya sendiri
        if ($currentUser->id === $pengguna->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri'
            ], 400);
        }

        $pengguna->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dihapus'
        ], 200);
    }
}