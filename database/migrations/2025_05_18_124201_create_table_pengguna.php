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
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id(); 
            $table->string('email', 30)->unique();
            $table->string('password');
            $table->string('nama', 50)->nullable();
            $table->string('telepon', 15)->nullable();
            $table->text('alamat')->nullable();
            $table->string('bank', 20)->nullable();
            $table->string('no_rekening', 20)->nullable();
            $table->enum('role', ['pembeli', 'penjual', 'petugas', 'admin']);
            $table->string('foto', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('pengguna');
    }
};
