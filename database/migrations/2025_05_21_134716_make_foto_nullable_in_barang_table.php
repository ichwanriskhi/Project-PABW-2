<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeFotoNullableInBarangTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->string('foto', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->string('foto', 255)->nullable(false)->change();
        });
    }
}
