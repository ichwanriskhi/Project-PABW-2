<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PenawaranResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Safely parse dates
        $waktu = $this->waktu ? Carbon::parse($this->waktu) : null;
        $waktuBs = $this->waktu_bs ? Carbon::parse($this->waktu_bs) : null;
        $tglDibuka = $this->whenLoaded('lelang') && $this->lelang->tgl_dibuka
            ? Carbon::parse($this->lelang->tgl_dibuka)
            : null;
        $tglSelesai = $this->whenLoaded('lelang') && $this->lelang->tgl_selesai
            ? Carbon::parse($this->lelang->tgl_selesai)
            : null;

        return [
            'id_penawaran' => $this->id_penawaran,
            'penawaran_harga' => $this->penawaran_harga,
            'uang_muka' => $this->uang_muka,
            'waktu' => $waktu ? $waktu->format('Y-m-d H:i:s') : null,
            'status_tawar' => $this->status_tawar,
            'bayar_sisa' => $this->bayar_sisa,
            'waktu_bs' => $waktuBs ? $waktuBs->format('Y-m-d H:i:s') : null,
            'status_bs' => $this->status_bs,

            // Relationships
            'lelang' => $this->whenLoaded('lelang', function () use ($tglDibuka, $tglSelesai) {
                return [
                    'id_lelang' => $this->lelang->id_lelang,
                    'status' => $this->lelang->status,
                    'tgl_dibuka' => $tglDibuka ? $tglDibuka->format('Y-m-d H:i:s') : null,
                    'tgl_selesai' => $tglSelesai ? $tglSelesai->format('Y-m-d H:i:s') : null,
                    'harga_akhir' => $this->lelang->harga_akhir,
                    'barang' => $this->lelang->relationLoaded('barang') ? [
                        'id_barang' => $this->lelang->barang->id_barang,
                        'nama_barang' => $this->lelang->barang->nama_barang,
                        'harga_awal' => $this->lelang->barang->harga_awal,
                        'foto' => $this->lelang->barang->foto ? explode(',', $this->lelang->barang->foto) : [],
                        'kategori' => $this->lelang->barang->relationLoaded('kategori') ? [
                            'id_kategori' => $this->lelang->barang->kategori->id_kategori,
                            'nama_kategori' => $this->lelang->barang->kategori->nama_kategori,
                        ] : null,
                    ] : null,
                    'penjual' => $this->lelang->relationLoaded('penjual') ? [
                        'id' => $this->lelang->penjual->id,
                        'nama' => $this->lelang->penjual->nama,
                    ] : null,
                ];
            }),
        ];
    }
}
