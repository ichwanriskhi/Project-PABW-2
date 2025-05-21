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
        Schema::create('pencairandana', function (Blueprint $table) {
            $table->id('id_pencairan');
            $table->integer('nominal');
            $table->unsignedBigInteger('id_lelang');
            $table->unsignedBigInteger('id_penjual');
            $table->timestamp('waktu')->useCurrent();
            
            $table->foreign('id_lelang')->references('id_lelang')->on('lelang')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_penjual')->references('id')->on('pengguna')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('pencairandana');
    }
};
