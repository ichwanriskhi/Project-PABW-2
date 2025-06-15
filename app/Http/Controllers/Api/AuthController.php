<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\PenggunaModel;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register new user as buyer (pembeli)
     */
    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama' => 'nullable|string|max:255',
                'email' => 'required|email|unique:pengguna,email',
                'password' => 'required|confirmed|min:6',
                'telepon' => 'nullable|string|max:15',
                'alamat' => 'nullable|string',
                'bank' => 'nullable|string|max:50',
                'no_rekening' => 'nullable|string|max:30',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid',
                'errors' => $e->errors(),
            ], 422);
        }

        // Handle photo upload
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = time() . '_' . uniqid() . '_' . $foto->getClientOriginalName();
            $fotoPath = $foto->storeAs('users', $filename, 'public');
        }

        $pengguna = PenggunaModel::create([
            'nama' => $validatedData['nama'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'telepon' => $validatedData['telepon'] ?? null,
            'alamat' => $validatedData['alamat'] ?? null,
            'bank' => $validatedData['bank'] ?? null,
            'no_rekening' => $validatedData['no_rekening'] ?? null,
            'role' => 'pembeli',
            'foto' => $fotoPath,
        ]);

        // Create token immediately after registration
        $token = $pengguna->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => [
                'pengguna' => new \App\Http\Resources\PenggunaResource($pengguna),
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 201);
    }

    /**
     * Register new user as seller (penjual)
     */
    public function registerSeller(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama' => 'nullable|string|max:255',
                'email' => 'required|email|unique:pengguna,email',
                'password' => 'required|confirmed|min:6',
                'telepon' => 'nullable|string|max:15',
                'alamat' => 'nullable|string',
                'bank' => 'nullable|string|max:50',
                'no_rekening' => 'nullable|string|max:30',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid',
                'errors' => $e->errors(),
            ], 422);
        }

        // Handle photo upload
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = time() . '_' . uniqid() . '_' . $foto->getClientOriginalName();
            $fotoPath = $foto->storeAs('users', $filename, 'public');
        }

        $pengguna = PenggunaModel::create([
            'nama' => $validatedData['nama'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'telepon' => $validatedData['telepon'] ?? null,
            'alamat' => $validatedData['alamat'] ?? null,
            'bank' => $validatedData['bank'] ?? null,
            'no_rekening' => $validatedData['no_rekening'] ?? null,
            'role' => 'penjual',
            'foto' => $fotoPath,
        ]);

        // Create token immediately after registration
        $token = $pengguna->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => [
                'pengguna' => new \App\Http\Resources\PenggunaResource($pengguna),
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 201);
    }

    /**
     * Login user
     */
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

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $pengguna = $request->user('api');
        
        if ($pengguna) {
            // Hapus token saat ini
            $pengguna->currentAccessToken()->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil'
            ], 200);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Tidak ada pengguna yang terautentikasi'
        ], 401);
    }
    
    /**
     * Get user profile
     */
    public function profile(Request $request)
    {
        $pengguna = $request->user('api');
        
        if (!$pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda harus login terlebih dahulu'
            ], 401);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diambil',
            'data' => new \App\Http\Resources\PenggunaResource($pengguna)
        ], 200);
    }
    
    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $pengguna = $request->user('api');
        
        if (!$pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda harus login terlebih dahulu'
            ], 401);
        }

        try {
            $validatedData = $request->validate([
                'nama' => 'sometimes|required|string|max:255',
                'telepon' => 'nullable|string|max:15',
                'alamat' => 'nullable|string',
                'bank' => 'nullable|string|max:50',
                'no_rekening' => 'nullable|string|max:30',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid',
                'errors' => $e->errors(),
            ], 422);
        }

        // Handle new photo upload
        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($pengguna->foto && Storage::disk('public')->exists($pengguna->foto)) {
                Storage::disk('public')->delete($pengguna->foto);
            }

            // Upload new photo
            $foto = $request->file('foto');
            $filename = time() . '_' . uniqid() . '_' . $foto->getClientOriginalName();
            $fotoPath = $foto->storeAs('users', $filename, 'public');
            $validatedData['foto'] = $fotoPath;
        }
        
        $pengguna->update($validatedData);
        
        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => new \App\Http\Resources\PenggunaResource($pengguna)
        ], 200);
    }
    
    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        $pengguna = $request->user('api');
        
        if (!$pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda harus login terlebih dahulu'
            ], 401);
        }

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
        
        if (!Hash::check($validatedData['current_password'], $pengguna->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini tidak sesuai',
            ], 400);
        }
        
        $pengguna->update([
            'password' => Hash::make($validatedData['password'])
        ]);

        // Optionally, revoke all tokens to force re-login
        // $pengguna->tokens()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah'
        ], 200);
    }

    /**
     * Check if token is valid
     */
    public function checkToken(Request $request)
    {
        $pengguna = $request->user('api');
        
        if (!$pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid'
            ], 401);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Token valid',
            'data' => new \App\Http\Resources\PenggunaResource($pengguna)
        ], 200);
    }

    /**
     * Refresh token (optional)
     */
    public function refreshToken(Request $request)
    {
        $pengguna = $request->user('api');
        
        if (!$pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda harus login terlebih dahulu'
            ], 401);
        }

        // Delete current token
        $pengguna->currentAccessToken()->delete();
        
        // Create new token
        $token = $pengguna->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token berhasil di-refresh',
            'data' => [
                'pengguna' => new \App\Http\Resources\PenggunaResource($pengguna),
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 200);
    }
}