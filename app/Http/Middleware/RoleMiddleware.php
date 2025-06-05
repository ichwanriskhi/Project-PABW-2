<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $role
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Periksa apakah user sudah login
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login')->withErrors(['auth' => 'Anda harus login terlebih dahulu.']);
        }

        $user = Auth::guard('web')->user();

        // Periksa apakah user memiliki role yang sesuai
        if ($user->role !== $role) {
            // Redirect ke dashboard sesuai role user
            return $this->redirectToUserDashboard($user->role);
        }

        return $next($request);
    }

    /**
     * Redirect ke dashboard sesuai role
     */
    private function redirectToUserDashboard($role)
    {
        switch ($role) {
            case 'admin':
                return redirect()->route('admin.index')->withErrors(['access' => 'Anda tidak memiliki akses ke halaman tersebut.']);
            case 'petugas':
                return redirect()->route('petugas.index')->withErrors(['access' => 'Anda tidak memiliki akses ke halaman tersebut.']);
            case 'pembeli':
                return redirect()->route('pembeli.index')->withErrors(['access' => 'Anda tidak memiliki akses ke halaman tersebut.']);
            case 'penjual':
                return redirect()->route('penjual.index')->withErrors(['access' => 'Anda tidak memiliki akses ke halaman tersebut.']);
            default:
                return redirect()->route('login')->withErrors(['role' => 'Role tidak dikenali.']);
        }
    }
}