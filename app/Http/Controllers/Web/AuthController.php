<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\PenggunaModel;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Menampilkan form login admin/petugas
     */
    public function showLoginAdmin()
    {
        // Jika sudah login, redirect sesuai role
        if (Auth::guard('web')->check()) {
            return $this->redirectBasedOnRole(Auth::guard('web')->user());
        }
        
        return view('auth.login_admin');
    }

    /**
     * Menampilkan form login penjual
     */
    public function showLoginPenjual()
    {
        // Jika sudah login, redirect sesuai role
        if (Auth::guard('web')->check()) {
            return $this->redirectBasedOnRole(Auth::guard('web')->user());
        }
        
        return view('auth.login_penjual');
    }

    /**
     * Menampilkan form login pembeli
     */
    public function showLoginPembeli()
    {
        // Jika sudah login, redirect sesuai role
        if (Auth::guard('web')->check()) {
            return $this->redirectBasedOnRole(Auth::guard('web')->user());
        }
        
        return view('auth.login_pembeli');
    }

     public function showRegisterPembeli()
    {
        // Jika sudah login, redirect sesuai role
        if (Auth::guard('web')->check()) {
            return $this->redirectBasedOnRole(Auth::guard('web')->user());
        }
        
        return view('auth.register_pembeli');
    }

    /**
     * Menampilkan form register penjual
     */
    public function showRegisterPenjual()
    {
        // Jika sudah login, redirect sesuai role
        if (Auth::guard('web')->check()) {
            return $this->redirectBasedOnRole(Auth::guard('web')->user());
        }
        
        return view('auth.register_penjual');
    }

     /**
     * Proses register pembeli
     */
    public function registerPembeli(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email|unique:pengguna',
                'password' => 'required|confirmed|min:6',
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $pengguna = PenggunaModel::create([
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => 'pembeli',
        ]);

        // Auto login setelah register
        Auth::guard('web')->login($pengguna);
        $request->session()->regenerate();

         // TAMBAHKAN INI: Simpan data user ke session
        $this->storeUserDataInSession($pengguna);

        return redirect()->route('pembeli.index')->with('success', 'Registrasi berhasil! Selamat datang!');
    }

    /**
     * Proses register penjual
     */
    public function registerPenjual(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email|unique:pengguna',
                'password' => 'required|confirmed|min:6',
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $pengguna = PenggunaModel::create([
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => 'penjual',
        ]);

        // Auto login setelah register
        Auth::guard('web')->login($pengguna);
        $request->session()->regenerate();

         // TAMBAHKAN INI: Simpan data user ke session
        $this->storeUserDataInSession($pengguna);

        return redirect()->route('penjual.index')->with('success', 'Registrasi berhasil! Selamat datang!');
    }

    /**
     * Proses login admin/petugas
     */
    public function loginAdmin(Request $request)
    {
        return $this->processLogin($request, ['admin', 'petugas'], 'login.admin');
    }

    /**
     * Proses login penjual
     */
    public function loginPenjual(Request $request)
    {
        return $this->processLogin($request, ['penjual'], 'login.penjual');
    }

    /**
     * Proses login pembeli
     */
    public function loginPembeli(Request $request)
    {
        return $this->processLogin($request, ['pembeli'], 'login.pembeli');
    }

    /**
     * Fungsi helper untuk memproses login
     */
    private function processLogin(Request $request, array $allowedRoles, string $redirectRoute)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        // Cari pengguna berdasarkan email
        $pengguna = PenggunaModel::where('email', $credentials['email'])->first();
        
        // Periksa apakah pengguna ditemukan dan password cocok
        if ($pengguna && Hash::check($credentials['password'], $pengguna->password)) {
            // Periksa apakah role sesuai dengan halaman login
            if (!in_array($pengguna->role, $allowedRoles)) {
                return back()->withErrors([
                    'email' => 'Anda tidak memiliki akses untuk login di halaman ini.',
                ])->withInput($request->only('email'));
            }
            
            // Login menggunakan guard web
            Auth::guard('web')->login($pengguna, $request->boolean('remember'));
            
            // Regenerate session untuk keamanan
            $request->session()->regenerate();

            // TAMBAHKAN INI: Simpan data user ke session
            $this->storeUserDataInSession($pengguna);
            
            // Redirect berdasarkan role
            return $this->redirectBasedOnRole($pengguna);
        }

        // Jika login gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->only('email'));
    }

    /**
     * TAMBAHKAN FUNGSI INI: Simpan data user ke session
     */
    private function storeUserDataInSession($user)
    {
        session([
            'user_id' => $user->id,
            'nama' => $user->nama ?? $user->email, // gunakan nama atau email jika nama kosong
            'email' => $user->email,
            'role' => $user->role,
            'telepon' => $user->telepon,
            'alamat' => $user->alamat,
            'foto' => $user->foto
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $user = Auth::guard('web')->user();
        Auth::guard('web')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Redirect ke halaman login sesuai role terakhir
        $redirectRoute = $this->getLoginRouteByRole($user ? $user->role : null);
        
        return redirect()->route($redirectRoute)->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Get login route berdasarkan role
     */
    private function getLoginRouteByRole($role)
    {
        switch ($role) {
            case 'admin':
            case 'petugas':
                return 'login.admin';
            case 'penjual':
                return 'login.penjual';
            case 'pembeli':
                return 'login.pembeli';
            default:
                return 'login.admin'; // default ke admin
        }
    }

    /**
     * Redirect berdasarkan role
     */
    private function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.index')->with('success', 'Selamat datang, Admin!');
            case 'petugas':
                return redirect()->route('petugas.index')->with('success', 'Selamat datang, Petugas!');
            case 'pembeli':
                return redirect()->route('pembeli.index')->with('success', 'Selamat datang, Pembeli!');
            case 'penjual':
                return redirect()->route('penjual.index')->with('success', 'Selamat datang, Penjual!');
            default:
                // Jika role tidak dikenali, logout dan redirect ke login
                Auth::guard('web')->logout();
                return redirect()->route('login.admin')->withErrors(['role' => 'Role tidak dikenali.']);
        }
    }

    /**
     * Menampilkan dashboard admin
     */
    public function adminIndex()
    {
        // Pastikan hanya admin yang bisa akses
        if (!Auth::guard('web')->check() || Auth::guard('web')->user()->role !== 'admin') {
            return redirect()->route('login.admin')->withErrors(['access' => 'Akses ditolak.']);
        }
        
        return view('admin.index');
    }

    /**
     * Menampilkan dashboard petugas
     */
    public function petugasIndex()
    {
        // Pastikan hanya petugas yang bisa akses
        if (!Auth::guard('web')->check() || Auth::guard('web')->user()->role !== 'petugas') {
            return redirect()->route('login.admin')->withErrors(['access' => 'Akses ditolak.']);
        }
        
        return view('petugas.index');
    }

    /**
     * Menampilkan dashboard pembeli
     */
    public function pembeliIndex()
    {
        // Pastikan hanya pembeli yang bisa akses
        if (!Auth::guard('web')->check() || Auth::guard('web')->user()->role !== 'pembeli') {
            return redirect()->route('login.pembeli')->withErrors(['access' => 'Akses ditolak.']);
        }
        
        // Redirect ke BarangController yang sudah memiliki logic untuk menampilkan barang
        return redirect()->route('pembeli.barang.index');
    }

    /**
     * Menampilkan dashboard penjual
     */
    public function penjualIndex()
    {
        // Pastikan hanya penjual yang bisa akses
        if (!Auth::guard('web')->check() || Auth::guard('web')->user()->role !== 'penjual') {
            return redirect()->route('login.penjual')->withErrors(['access' => 'Akses ditolak.']);
        }
        
        return view('penjual.index');
    }

    public function bantuan()
    {
        // Redirect ke halaman bantuan
        return view('bantuan');
    }
}