<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BarangResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Process foto
        $foto = $this->foto ? explode(',', $this->foto) : [];
        $foto_utama = count($foto) > 0 ? trim($foto[0]) : 'default-product.png';

        return [
            'id_barang' => $this->id_barang,
            'nama_barang' => $this->nama_barang,
            'harga_awal' => $this->harga_awal,
            'lokasi' => $this->lokasi,
            'deskripsi' => $this->deskripsi,
            'kondisi' => $this->kondisi,
            'status' => $this->status,
            'foto_utama' => $foto_utama,
            'foto' => $foto,
            
            // Relationships
            'kategori' => $this->whenLoaded('kategori', fn() => [
                'id_kategori' => $this->kategori->id_kategori,
                'nama_kategori' => $this->kategori->nama_kategori,
            ]),
            
            'penjual' => $this->whenLoaded('penjual', fn() => [
                'id' => $this->penjual->id,
                'nama' => $this->penjual->nama,
            ]),
            
            'lelang' => $this->whenLoaded('lelang', function() {
                if (!$this->lelang) return null;
                return [
                    'id_lelang' => $this->lelang->id_lelang,
                    'status' => $this->lelang->status,
                    // include other lelang fields as needed
                ];
            }),
        ];
    }
}

?>