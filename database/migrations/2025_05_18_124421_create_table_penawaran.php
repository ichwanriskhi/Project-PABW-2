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
        Schema::create('penawaran', function (Blueprint $table) {
            $table->id('id_penawaran');
            $table->unsignedBigInteger('id_lelang');
            $table->unsignedBigInteger('id_pembeli');
            $table->integer('penawaran_harga');
            $table->integer('uang_muka');
            $table->timestamp('waktu')->useCurrent();
            $table->enum('status_tawar', ['banned', 'win', 'lose'])->nullable();
            $table->integer('bayar_sisa')->nullable();
            $table->timestamp('waktu_bs')->nullable();
            $table->enum('status_bs', ['dikonfirmasi'])->nullable();
            
            $table->foreign('id_lelang')->references('id_lelang')->on('lelang')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_pembeli')->references('id')->on('pengguna')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('penawaran');
    }
};
