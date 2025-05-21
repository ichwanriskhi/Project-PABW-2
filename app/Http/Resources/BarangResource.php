<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BarangResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_barang' => $this->id_barang,
            'nama_barang' => $this->nama_barang,
            'harga_awal' => $this->harga_awal,
            'lokasi' => $this->lokasi,
            'deskripsi' => $this->deskripsi,
            'kondisi' => $this->kondisi,
            'status' => $this->status,
            'foto' => $this->foto ? asset('storage/' . $this->foto) : null,
            'foto_path' => $this->foto,
            'id_kategori' => $this->id_kategori,
            'id_penjual' => $this->id_penjual,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Relasi
            'kategori' => $this->whenLoaded('kategori', function () {
                return [
                    'id_kategori' => $this->kategori->id_kategori,
                    'nama_kategori' => $this->kategori->nama_kategori,
                ];
            }),
            
            'penjual' => $this->whenLoaded('penjual', function () {
                return [
                    'id' => $this->penjual->id,
                    'nama_lengkap' => $this->penjual->nama_lengkap,
                    'username' => $this->penjual->username,
                    'email' => $this->penjual->email,
                    'telp' => $this->penjual->telp,
                ];
            }),
            
            'lelang' => $this->whenLoaded('lelang', function () {
                return $this->lelang->map(function ($lelang) {
                    return [
                        'id_lelang' => $lelang->id_lelang,
                        'tgl_lelang' => $lelang->tgl_lelang,
                        'harga_akhir' => $lelang->harga_akhir,
                        'status' => $lelang->status,
                        'created_at' => $lelang->created_at?->format('Y-m-d H:i:s'),
                    ];
                });
            }),
        ];
    }
}