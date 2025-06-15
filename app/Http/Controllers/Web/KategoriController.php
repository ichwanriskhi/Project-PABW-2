<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class KategoriController extends Controller
{
    /**
     * Menampilkan daftar semua kategori.
     */
    public function index()
    {
        // Ambil semua kategori dengan jumlah barang (untuk pagination tetap sama)
        $kategori = KategoriModel::withCount('barang')->latest()->paginate(10);

        // Cari kategori terpopuler (yang memiliki barang terbanyak)
        $kategoriTerpopuler = KategoriModel::withCount('barang')
            ->having('barang_count', '>', 0) // hanya kategori yang memiliki barang
            ->orderBy('barang_count', 'desc')
            ->first();

        // Cari kategori kurang diminati (yang memiliki barang paling sedikit, tapi tetap ada barangnya)
        $kategoriKurangDiminati = KategoriModel::withCount('barang')
            ->having('barang_count', '>', 0) // hanya kategori yang memiliki barang
            ->orderBy('barang_count', 'asc')
            ->first();

        // Jika kategori terpopuler dan kurang diminati sama (hanya ada 1 kategori dengan barang)
        if (
            $kategoriTerpopuler && $kategoriKurangDiminati &&
            $kategoriTerpopuler->id_kategori == $kategoriKurangDiminati->id_kategori
        ) {
            $kategoriKurangDiminati = null; // Set null jika hanya ada 1 kategori
        }

        return view('kategori.index', compact('kategori', 'kategoriTerpopuler', 'kategoriKurangDiminati'));
    }

    /**
     * Menampilkan form untuk membuat kategori baru.
     */
    public function create()
    {
        return view('kategori.create');
    }

    /**
     * Menyimpan kategori baru ke database.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id_kategori' => 'required|string|unique:kategori,id_kategori',
                'nama_kategori' => 'required|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        KategoriModel::create([
            'id_kategori' => $validatedData['id_kategori'],
            'nama_kategori' => $validatedData['nama_kategori'],
        ]);

        $redirectRoute = match (Auth::user()->role) {
            'admin' => 'admin.kategori.index',
            'petugas' => 'petugas.kategori.index',
            default => 'kategori.index'
        };

        return redirect()->route($redirectRoute)->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail kategori tertentu.
     */
    public function show($id)
    {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return redirect()->route('kategori.index')->withErrors(['kategori' => 'Kategori tidak ditemukan.']);
        }

        return view('kategori.show', compact('kategori'));
    }

    /**
     * Menampilkan form untuk mengedit kategori.
     */
    public function edit($id)
    {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return redirect()->route('kategori.index')->withErrors(['kategori' => 'Kategori tidak ditemukan.']);
        }

        return view('kategori.edit', compact('kategori'));
    }

    /**
     * Memperbarui kategori tertentu di database.
     */
    public function update(Request $request, $id)
    {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return redirect()->route('kategori.index')->withErrors(['kategori' => 'Kategori tidak ditemukan.']);
        }

        try {
            $validatedData = $request->validate([
                'nama_kategori' => 'required|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $kategori->update([
            'nama_kategori' => $validatedData['nama_kategori'],
        ]);

        $redirectRoute = match (Auth::user()->role) {
            'admin' => 'admin.kategori.index',
            'petugas' => 'petugas.kategori.index',
            default => 'kategori.index'
        };

        return redirect()->route($redirectRoute)->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Menghapus kategori tertentu dari database.
     */
    public function destroy($id)
    {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return redirect()->route('kategori.index')->withErrors(['kategori' => 'Kategori tidak ditemukan.']);
        }

        // Cek apakah kategori memiliki barang terkait
        if ($kategori->barang()->count() > 0) {
            return redirect()->route('kategori.index')->withErrors(['kategori' => 'Kategori tidak dapat dihapus karena masih memiliki barang terkait.']);
        }

        $kategori->delete();

        $redirectRoute = match (Auth::user()->role) {
            'admin' => 'admin.kategori.index',
            'petugas' => 'petugas.kategori.index',
            default => 'kategori.index'
        };

        return redirect()->route($redirectRoute)->with('success', 'Kategori berhasil dihapus!');
    }
}
