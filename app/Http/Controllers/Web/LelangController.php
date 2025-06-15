<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LelangModel;
use App\Models\BarangModel;
use App\Models\PenawaranModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LelangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LelangModel::with(['barang.kategori', 'pembeli', 'petugas'])
            ->orderBy('tgl_dibuka', 'desc');

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Search berdasarkan nama barang
        if ($request->has('search') && $request->search != '') {
            $query->whereHas('barang', function ($q) use ($request) {
                $q->where('nama_barang', 'like', '%' . $request->search . '%');
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $lelang = $query->paginate($perPage);

        // Append query parameters to pagination links
        $lelang->appends($request->all());

        return view('lelang.index', compact('lelang'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $lelang = LelangModel::with(['barang.kategori', 'barang.penjual', 'pembeli', 'petugas'])
            ->findOrFail($id);

        $penawaran = PenawaranModel::with(['pembeli'])
            ->where('id_lelang', $id)
            ->orderBy('penawaran_harga', 'desc')
            ->paginate(10);

        return view('lelang.detail', compact('lelang', 'penawaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Hanya admin dan petugas yang bisa akses
        if (!in_array(Auth::user()->role, ['admin', 'petugas'])) {
            return redirect()->route('lelang.index')->with('error', 'Anda tidak memiliki akses untuk mengedit lelang.');
        }

        $lelang = LelangModel::findOrFail($id);

        // Hanya bisa edit jika lelang masih dibuka
        if ($lelang->status !== 'dibuka') {
            return redirect()->route('lelang.index')->with('error', 'Lelang yang sudah ditutup tidak dapat diedit.');
        }

        $barang = BarangModel::where('status', 'disetujui')->get();

        return view('lelang.edit', compact('lelang', 'barang'));
    }

    /**
     * Close the auction
     */
    public function close($id)
{
    if (!in_array(Auth::user()->role, ['admin', 'petugas'])) {
        return redirect()->route('lelang.index')->with('error', 'Anda tidak memiliki akses untuk menutup lelang.');
    }

    try {
        DB::beginTransaction();

        $lelang = LelangModel::findOrFail($id);

        if ($lelang->status !== 'dibuka') {
            return redirect()->route('lelang.index')->with('error', 'Lelang sudah ditutup sebelumnya.');
        }

        // Cari penawar tertinggi
        $penawarTertinggi = PenawaranModel::where('id_lelang', $id)
            ->orderBy('penawaran_harga', 'desc')
            ->first();

        $updateData = [
            'status' => 'ditutup',
            'tgl_selesai' => now(),
        ];

        if ($penawarTertinggi) {
            $updateData['id_pembeli'] = $penawarTertinggi->id_pembeli;
            $updateData['harga_akhir'] = $penawarTertinggi->penawaran_harga;

            // Update status penawar tertinggi menjadi 'win'
            $penawarTertinggi->update([
                'status_tawar' => 'win'
            ]);

            // Update status penawar lainnya menjadi 'lose'
            PenawaranModel::where('id_lelang', $id)
                ->where('id_penawaran', '!=', $penawarTertinggi->id_penawaran)
                ->update([
                    'status_tawar' => 'lose'
                ]);
        }

        $lelang->update($updateData);

        DB::commit();
        
        $routeName = match (Auth::user()->role) {
            'admin' => 'admin.lelang.index',
            'petugas' => 'petugas.lelang.index',
            default => 'lelang.index',
        };
        return redirect()->route($routeName)->with('success', 'Lelang berhasil ditutup.');
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Gagal menutup lelang: ' . $e->getMessage());
    }
}

    /**
     * Show auction result
     */
    public function result($id)
    {
        // Hanya admin dan petugas yang bisa akses
        if (!in_array(Auth::user()->role, ['admin', 'petugas'])) {
            return redirect()->route('lelang.index')->with('error', 'Anda tidak memiliki akses untuk melihat hasil lelang.');
        }

        $lelang = LelangModel::with(['barang.kategori', 'barang.penjual', 'pembeli', 'petugas'])
            ->findOrFail($id);

        if ($lelang->status === 'dibuka') {
            return redirect()->route('lelang.index')->with('error', 'Lelang masih berlangsung.');
        }

        // Ambil semua history lelang
        $historyLelang = PenawaranModel::with('user')
            ->where('id_lelang', $id)
            ->orderBy('penawaran_harga', 'desc')
            ->get();

        return view('lelang.result', compact('lelang', 'historyLelang'));
    }

    /**
     * Complete transaction
     */
    public function complete($id)
    {
        // Hanya admin dan petugas yang bisa akses
        if (!in_array(Auth::user()->role, ['admin', 'petugas'])) {
            return redirect()->route('lelang.index')->with('error', 'Anda tidak memiliki akses.');
        }

        try {
            $lelang = LelangModel::findOrFail($id);

            if ($lelang->status !== 'ditutup') {
                return redirect()->route('lelang.index')->with('error', 'Lelang belum ditutup atau sudah selesai.');
            }

            $lelang->update([
                'status' => 'selesai',
                'status_dana' => 'diserahkan'
            ]);

            return redirect()->route('lelang.result', $id)->with('success', 'Transaksi lelang berhasil diselesaikan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyelesaikan transaksi: ' . $e->getMessage());
        }
    }
}
