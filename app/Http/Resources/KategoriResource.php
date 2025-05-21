<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KategoriResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id_kategori' => $this->id_kategori,
            'nama_kategori' => $this->nama_kategori,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}