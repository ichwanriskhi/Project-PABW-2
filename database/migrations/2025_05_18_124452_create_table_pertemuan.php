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
        Schema::create('pertemuan', function (Blueprint $table) {
            $table->id('id_pertemuan');
            $table->unsignedBigInteger('id_penawaran');
            $table->string('lokasi', 50);
            $table->date('waktu');
            $table->enum('status_terima_barang', ['diterima'])->nullable();
            $table->timestamps();
            
            $table->foreign('id_penawaran')->references('id_penawaran')->on('penawaran')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('pertemuan');
    }
};
