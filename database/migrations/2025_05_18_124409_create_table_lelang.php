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
        Schema::create('lelang', function (Blueprint $table) {
            $table->id('id_lelang');
            $table->unsignedBigInteger('id_barang');
            $table->timestamp('tgl_dibuka')->useCurrent();
            $table->timestamp('tgl_selesai')->nullable();
            $table->integer('harga_akhir');
            $table->unsignedBigInteger('id_pembeli')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->enum('status_dana', ['belum diserahkan', 'diserahkan']);
            $table->unsignedBigInteger('id_petugas');
            $table->enum('status', ['dibuka', 'ditutup', 'return', 'selesai']);
            
            $table->foreign('id_barang')->references('id_barang')->on('barang')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_pembeli')->references('id')->on('pengguna')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_user')->references('id')->on('pengguna')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_petugas')->references('id')->on('pengguna')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('lelang');
    }
};
