<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PenawaranModel;
use App\Models\LelangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PenawaranController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'id_lelang' => 'required|exists:lelang,id_lelang',
            'penawaran_harga' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    $lelang = LelangModel::find($request->id_lelang);
                    $tawaranTertinggi = PenawaranModel::where('id_lelang', $request->id_lelang)
                        ->max('penawaran_harga') ?? $lelang->barang->harga_awal;

                    $minBid = $tawaranTertinggi + 10000;

                    if ($value < $minBid) {
                        $fail("Penawaran harus lebih tinggi dari tawaran tertinggi saat ini (Rp " . number_format($minBid, 0, ',', '.') . ")");
                    }
                }
            ],
            'uang_muka' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if auction is still open
        $lelang = LelangModel::find($request->id_lelang);
        if ($lelang->status !== 'dibuka') {
            return redirect()->back()
                ->with('error', 'Lelang sudah ditutup, tidak bisa melakukan penawaran.');
        }

        // Get current user ID
        $id_pembeli = Auth::id();

        // Create new bid
        PenawaranModel::create([
            'id_lelang' => $request->id_lelang,
            'id_pembeli' => $id_pembeli,
            'penawaran_harga' => $request->penawaran_harga,
            'uang_muka' => $request->uang_muka,
            'waktu' => now(),
            'status_tawar' => null, // Initially null, will be updated when auction closes
            'bayar_sisa' => null,
            'waktu_bs' => null,
            'status_bs' => null
        ]);

        return redirect()->back()
            ->with('success', 'Penawaran berhasil dikirim!')
            ->with('scroll', true); // For scrolling to form after submission
    }

    // Additional methods for bid management
    public function history(Request $request)
    {
        $user = Auth::user();

        // Query dasar
        $query = PenawaranModel::with(['lelang.barang', 'lelang.penjual', 'pembeli'])
            ->where('id_pembeli', $user->id)
            ->orderBy('waktu', 'desc');

        // Filter berdasarkan status pembayaran
        if ($request->has('filter')) {
            if ($request->filter == 'paid') {
                $query->where('status_bs', 'dikonfirmasi');
            } elseif ($request->filter == 'unpaid') {
                $query->where('status_tawar', 'win')
                    ->whereNull('status_bs');
            }
        }

        // Hitung statistik
        $wonCount = PenawaranModel::where('id_pembeli', $user->id)
            ->where('status_tawar', 'win')
            ->count();

        $completedCount = PenawaranModel::where('id_pembeli', $user->id)
            ->where('status_bs', 'dikonfirmasi')
            ->count();

        $bannedCount = PenawaranModel::where('id_pembeli', $user->id)
            ->where('status_tawar', 'banned')
            ->count();

        $refundCount = 0; // Anda bisa menyesuaikan ini sesuai logika refund

        // Paginasi
        $penawaran = $query->paginate(5);

        return view('pembeli.aktivitas', compact(
            'penawaran',
            'wonCount',
            'completedCount',
            'bannedCount',
            'refundCount'
        ));
    }

    public function show($id)
    {
        // Ambil data penawaran berdasarkan ID
        $penawaran = PenawaranModel::with(['lelang.barang', 'lelang.barang.kategori'])
            ->where('id_penawaran', $id)
            ->firstOrFail();

        // Hitung nilai-nilai pembayaran
        $subtotal = $penawaran->penawaran_harga;
        $uangMuka = $penawaran->uang_muka;
        $biayaLayanan = 15000; // Biaya tetap
        $total = $subtotal - $uangMuka + $biayaLayanan;

        // Format tanggal
        $tanggalLelang = date('d F Y H:i:s', strtotime($penawaran->waktu));

        // Potong deskripsi dengan mempertahankan formatting
        $deskripsi = $penawaran->lelang->barang->deskripsi;
        $clean_desc = trim(strip_tags($deskripsi));

        // Pisahkan paragraf
        $paragraf = preg_split('/\n\s*\n/', $clean_desc); // Pisah berdasarkan empty lines
        $deskripsi_pendek = implode("\n\n", array_slice($paragraf, 0, 3));

        // Jika hanya ada 1 paragraf, jangan tampilkan tombol
        $show_more_button = (count($paragraf) > 3);

        return view('pembeli.detailtransaksi', [
            'penawaran' => $penawaran,
            'subtotal' => $subtotal,
            'uangMuka' => $uangMuka,
            'biayaLayanan' => $biayaLayanan,
            'total' => $total,
            'tanggalLelang' => $tanggalLelang,
            'deskripsi_pendek' => $deskripsi_pendek,
            'deskripsi_lengkap' => $clean_desc,
            'show_more_button' => $show_more_button
        ]);
    }

    public function updatePayment(Request $request, $id)
    {
        $penawaran = PenawaranModel::where('id_penawaran', $id)
            ->where('id_pembeli', Auth::id())
            ->firstOrFail();

        // Validate payment
        $request->validate([
            'bayar_sisa' => 'required|numeric|min:' . $penawaran->penawaran_harga - $penawaran->uang_muka
        ]);

        // Update payment
        $penawaran->update([
            'bayar_sisa' => $request->bayar_sisa,
            'waktu_bs' => now(),
            'status_bs' => 'dikonfirmasi'
        ]);

        return redirect()->back()
            ->with('success', 'Pembayaran sisa berhasil dikonfirmasi');
    }
}
