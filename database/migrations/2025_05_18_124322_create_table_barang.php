<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id('id_barang');
            $table->string('nama_barang', 50);
            $table->integer('harga_awal');
            $table->string('lokasi', 20);
            $table->text('deskripsi');
            $table->enum('kondisi', ['bekas', 'baru']);
            $table->string('id_kategori', 15);
            $table->enum('status', ['belum disetujui', 'disetujui', 'ditolak']);
            $table->string('foto', 255);
            $table->unsignedBigInteger('id_penjual');
            $table->timestamps(); 
            
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_penjual')->references('id')->on('pengguna')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('barang');
    }
};
