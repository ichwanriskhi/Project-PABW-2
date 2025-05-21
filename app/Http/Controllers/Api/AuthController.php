<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\PenggunaModel;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama' => 'nullable|string|max:255',
                'email' => 'required|email|unique:pengguna',
                'password' => 'required|confirmed|min:6',
                'telepon' => 'nullable|string|max:15',
                'alamat' => 'nullable|string',
                'bank' => 'nullable|string|max:50',
                'no_rekening' => 'nullable|string|max:30',
                'foto' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid',
                'errors' => $e->errors(),
            ], 422);
        }

        $pengguna = PenggunaModel::create([
            'nama' => $validatedData['nama'] ?? null,
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'telepon' => $validatedData['telepon'] ?? null,
            'alamat' => $validatedData['alamat'] ?? null,
            'bank' => $validatedData['bank'] ?? null,
            'no_rekening' => $validatedData['no_rekening'] ?? null,
            'role' => 'pembeli',
            'foto' => $validatedData['foto'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => new \App\Http\Resources\PenggunaResource($pengguna), 
        ], 201);
    }

    public function registerSeller(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama' => 'nullable|string|max:255',
                'email' => 'required|email|unique:pengguna',
                'password' => 'required|confirmed|min:6',
                'telepon' => 'nullable|string|max:15',
                'alamat' => 'nullable|string',
                'bank' => 'nullable|string|max:50',
                'no_rekening' => 'nullable|string|max:30',
                'foto' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid',
                'errors' => $e->errors(),
            ], 422);
        }

        $pengguna = PenggunaModel::create([
            'nama' => $validatedData['nama'] ?? null,
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'telepon' => $validatedData['telepon'] ?? null,
            'alamat' => $validatedData['alamat'] ?? null,
            'bank' => $validatedData['bank'] ?? null,
            'no_rekening' => $validatedData['no_rekening'] ?? null,
            'role' => 'penjual',
            'foto' => $validatedData['foto'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => new \App\Http\Resources\PenggunaResource($pengguna), 
        ], 201);
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kesalahan validasi',
                'errors' => $e->errors(),
            ], 422);
        }
        
        // Cari pengguna berdasarkan email
        $pengguna = PenggunaModel::where('email', $credentials['email'])->first();
        
        // Periksa apakah pengguna ditemukan dan password cocok
        if ($pengguna && Hash::check($credentials['password'], $pengguna->password)) {
            // Hapus token lama jika ada (opsional)
            $pengguna->tokens()->delete();
            
            // Buat token menggunakan Sanctum
            $token = $pengguna->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'pengguna' => new \App\Http\Resources\PenggunaResource($pengguna),
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah',
        ], 401);
    }

    public function logout(Request $request)
    {
        $pengguna = $request->user();
        
        if ($pengguna) {
            // Hapus token saat ini
            $pengguna->currentAccessToken()->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil. Token dihapus.'
            ], 200);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Tidak ada pengguna yang terautentikasi'
        ], 401);
    }
    
    public function profile(Request $request)
    {
        $pengguna = $request->user();
        
        if (!$pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada pengguna yang terautentikasi'
            ], 401);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Data profil berhasil diambil',
            'data' => new \App\Http\Resources\PenggunaResource($pengguna)
        ], 200);
    }
    
    public function updateProfile(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama' => 'nullable|string|max:255',
                'telepon' => 'nullable|string|max:15',
                'alamat' => 'nullable|string',
                'bank' => 'nullable|string|max:50',
                'no_rekening' => 'nullable|string|max:30',
                'foto' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid',
                'errors' => $e->errors(),
            ], 422);
        }
        
        $pengguna = $request->user();
        
        if (!$pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada pengguna yang terautentikasi'
            ], 401);
        }
        
        $pengguna->update($validatedData);
        
        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => new \App\Http\Resources\PenggunaResource($pengguna)
        ], 200);
    }
    
    public function changePassword(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'current_password' => 'required',
                'password' => 'required|confirmed|min:6',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid',
                'errors' => $e->errors(),
            ], 422);
        }
        
        $pengguna = $request->user();
        
        if (!$pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada pengguna yang terautentikasi'
            ], 401);
        }
        
        if (!Hash::check($validatedData['current_password'], $pengguna->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini tidak sesuai',
            ], 401);
        }
        
        $pengguna->update([
            'password' => Hash::make($validatedData['password'])
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah'
        ], 200);
    }
}