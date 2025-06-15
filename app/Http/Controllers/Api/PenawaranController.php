<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PenawaranResource;
use App\Http\Resources\PenawaranCollection;
use App\Models\PenawaranModel;
use App\Models\LelangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PenawaranController extends Controller
{
    /**
     * Store a newly created bid in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user('api');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda harus login terlebih dahulu'
            ], 401);
        }

        if ($user->role !== 'pembeli') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Hanya pembeli yang dapat melakukan penawaran'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'id_lelang' => 'required|exists:lelang,id_lelang',
            'penawaran_harga' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    $lelang = LelangModel::with('barang')->find($request->id_lelang);
                    if (!$lelang) {
                        $fail('Lelang tidak ditemukan');
                        return;
                    }

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
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if auction is still open
        $lelang = LelangModel::find($request->id_lelang);
        if ($lelang->status !== 'dibuka') {
            return response()->json([
                'success' => false,
                'message' => 'Lelang sudah ditutup, tidak bisa melakukan penawaran'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create new bid
            $penawaran = PenawaranModel::create([
                'id_lelang' => $request->id_lelang,
                'id_pembeli' => $user->id,
                'penawaran_harga' => $request->penawaran_harga,
                'uang_muka' => $request->uang_muka,
                'waktu' => now(),
                'status_tawar' => null,
                'bayar_sisa' => null,
                'waktu_bs' => null,
                'status_bs' => null
            ]);

            // Update lelang's highest bid
            $lelang->harga_akhir = $request->penawaran_harga;
            $lelang->id_pembeli = $user->id;
            $lelang->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penawaran berhasil dikirim',
                'data' => new PenawaranResource($penawaran)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan penawaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display bid history for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function history(Request $request)
    {
        $user = $request->user('api');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda harus login terlebih dahulu'
            ], 401);
        }

        $perPage = $request->get('per_page', 5);
        
        $query = PenawaranModel::with(['lelang.barang.kategori', 'lelang.penjual', 'pembeli'])
            ->where('id_pembeli', $user->id)
            ->orderBy('waktu', 'desc');

        // Filter berdasarkan status pembayaran
        if ($request->has('filter')) {
            if ($request->filter == 'paid') {
                $query->where('status_bs', 'dikonfirmasi');
            } elseif ($request->filter == 'unpaid') {
                $query->where('status_tawar', 'win')
                    ->whereNull('status_bs');
            } elseif ($request->filter == 'banned') {
                $query->where('status_tawar', 'banned');
            }
        }

        $penawaran = $query->paginate($perPage);

        // Hitung statistik
        $stats = [
            'won' => PenawaranModel::where('id_pembeli', $user->id)
                ->where('status_tawar', 'win')
                ->count(),
            'completed' => PenawaranModel::where('id_pembeli', $user->id)
                ->where('status_bs', 'dikonfirmasi')
                ->count(),
            'banned' => PenawaranModel::where('id_pembeli', $user->id)
                ->where('status_tawar', 'banned')
                ->count(),
            'refund' => 0 // Anda bisa menyesuaikan ini sesuai logika refund
        ];

        return response()->json([
            'success' => true,
            'message' => 'Riwayat penawaran',
            'data' => new PenawaranCollection($penawaran),
            'stats' => $stats
        ], 200);
    }

    /**
     * Display the specified bid.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $user = $request->user('api');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda harus login terlebih dahulu'
            ], 401);
        }

        $penawaran = PenawaranModel::with(['lelang.barang', 'lelang.barang.kategori'])
            ->where('id_penawaran', $id)
            ->where('id_pembeli', $user->id)
            ->first();

        if (!$penawaran) {
            return response()->json([
                'success' => false,
                'message' => 'Penawaran tidak ditemukan'
            ], 404);
        }

        // Hitung nilai-nilai pembayaran
        $paymentDetails = [
            'subtotal' => $penawaran->penawaran_harga,
            'uang_muka' => $penawaran->uang_muka,
            'biaya_layanan' => 15000, // Biaya tetap
            'total' => ($penawaran->penawaran_harga - $penawaran->uang_muka) + 15000,
            'tanggal_lelang' => $penawaran->waktu->format('d F Y H:i:s')
        ];

        // Clean description
        $deskripsi = $penawaran->lelang->barang->deskripsi;
        $clean_desc = trim(strip_tags($deskripsi));
        $paragraf = preg_split('/\n\s*\n/', $clean_desc);
        $deskripsi_pendek = implode("\n\n", array_slice($paragraf, 0, 3));

        $responseData = new PenawaranResource($penawaran);
        $responseArray = $responseData->toArray($request);
        
        $responseArray['payment_details'] = $paymentDetails;
        $responseArray['deskripsi_pendek'] = $deskripsi_pendek;
        $responseArray['deskripsi_lengkap'] = $clean_desc;
        $responseArray['show_more_button'] = (count($paragraf) > 3);

        return response()->json([
            'success' => true,
            'message' => 'Detail penawaran',
            'data' => $responseArray
        ], 200);
    }

    /**
     * Update payment for the specified bid.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePayment(Request $request, $id)
    {
        $user = $request->user('api');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Anda harus login terlebih dahulu'
            ], 401);
        }

        $penawaran = PenawaranModel::where('id_penawaran', $id)
            ->where('id_pembeli', $user->id)
            ->first();

        if (!$penawaran) {
            return response()->json([
                'success' => false,
                'message' => 'Penawaran tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'bayar_sisa' => 'required|numeric|min:' . ($penawaran->penawaran_harga - $penawaran->uang_muka)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $penawaran->update([
                'bayar_sisa' => $request->bayar_sisa,
                'waktu_bs' => now(),
                'status_bs' => 'dikonfirmasi'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran sisa berhasil dikonfirmasi',
                'data' => new PenawaranResource($penawaran)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}