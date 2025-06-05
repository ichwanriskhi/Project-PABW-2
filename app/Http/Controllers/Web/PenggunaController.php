<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PenggunaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    /**
     * Menampilkan daftar petugas
     */
    public function index()
    {
        // Ambil data pengguna dengan role petugas beserta jumlah lelang yang disetujui
        $petugas = PenggunaModel::withCount(['lelangDisetujui as jumlah_disetujui'])
            ->where('role', 'petugas')
            ->paginate(5);

        return view('admin.petugas', compact('petugas'));
    }

    /**
     * FUNCTION BARU: Menampilkan daftar pengguna (pembeli dan penjual)
     */
    public function dataPengguna()
    {
        // Hitung jumlah pembeli dan penjual
        $pembeliCount = PenggunaModel::where('role', 'pembeli')->count();
        $penjualCount = PenggunaModel::where('role', 'penjual')->count();
        
        // Ambil data pembeli dengan hitungan lelang
        $pembeli = PenggunaModel::withCount([
                'lelangDiikuti as jumlah_lelang_diikuti',
                'lelangDimenangkan as jumlah_lelang_dimenangkan'
            ])
            ->where('role', 'pembeli')
            ->paginate(5, ['*'], 'pembeli_page');
        
        // Ambil data penjual dengan hitungan lelang
        $penjual = PenggunaModel::withCount([
                'lelangAktif as jumlah_lelang_aktif',
                'lelangSelesai as jumlah_lelang_selesai'
            ])
            ->where('role', 'penjual')
            ->paginate(5, ['*'], 'penjual_page');
        
        return view('admin.pengguna', compact('pembeliCount', 'penjualCount', 'pembeli', 'penjual'));
    }

    /**
     * FUNCTION BARU: Mencari pengguna (pembeli dan penjual)
     */
    public function searchPengguna(Request $request)
    {
        $query = $request->input('query');
        
        // Hitung jumlah pembeli dan penjual
        $pembeliCount = PenggunaModel::where('role', 'pembeli')->count();
        $penjualCount = PenggunaModel::where('role', 'penjual')->count();
        
        // Cari pembeli
        $pembeli = PenggunaModel::withCount([
                'lelangDiikuti as jumlah_lelang_diikuti',
                'lelangDimenangkan as jumlah_lelang_dimenangkan'
            ])
            ->where('role', 'pembeli')
            ->where(function($q) use ($query) {
                $q->where('nama', 'like', '%'.$query.'%')
                  ->orWhere('email', 'like', '%'.$query.'%');
            })
            ->paginate(5, ['*'], 'pembeli_page');
            
        // Cari penjual
        $penjual = PenggunaModel::withCount([
                'lelangAktif as jumlah_lelang_aktif',
                'lelangSelesai as jumlah_lelang_selesai'
            ])
            ->where('role', 'penjual')
            ->where(function($q) use ($query) {
                $q->where('nama', 'like', '%'.$query.'%')
                  ->orWhere('email', 'like', '%'.$query.'%');
            })
            ->paginate(5, ['*'], 'penjual_page');
        
        return view('admin.pengguna', compact('pembeliCount', 'penjualCount', 'pembeli', 'penjual'));
    }

    /**
     * Menampilkan form tambah petugas
     */
    public function create()
    {
        return view('admin.addpetugas');
    }

    /**
     * Menyimpan petugas baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:pengguna,email',
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|max:15',
            'password' => 'required|string|min:8',
        ]);

        // Set password default (bisa diganti sesuai kebutuhan)
        $password = $request->password ?: 'password123';

        PenggunaModel::create([
            'email' => $request->email,
            'nama' => $request->nama,
            'telepon' => $request->telepon,
            'password' => Hash::make($password),
            'role' => 'petugas',
            // Set default values untuk field lainnya
            'alamat' => '',
            'bank' => '',
            'no_rekening' => '',
        ]);

        return redirect()->route('admin.petugas.index')->with('success', 'Petugas berhasil ditambahkan');
    }

    /**
     * Menampilkan form edit petugas
     */
    public function edit($id)
    {
        $petugas = PenggunaModel::findOrFail($id);
        return view('admin.editpetugas', compact('petugas'));
    }

    /**
     * Mengupdate data petugas
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email|unique:pengguna,email,' . $id,
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|max:15',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $petugas = PenggunaModel::findOrFail($id);
        $data = $request->only(['email', 'nama', 'telepon']);

        // Jika password diisi, update password
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $petugas->update($data);

        return redirect()->route('admin.petugas.index')->with('success', 'Data petugas berhasil diperbarui');
    }

    /**
     * Menghapus petugas
     */
    public function destroy($id)
    {
        $petugas = PenggunaModel::findOrFail($id);
        $petugas->delete();

        return redirect()->route('petugas.index')->with('success', 'Petugas berhasil dihapus');
    }
}
