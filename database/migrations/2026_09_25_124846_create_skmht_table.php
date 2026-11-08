<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skmht', function (Blueprint $table) {
            $table->id('id_skmht');
            $table->string('nomor_bulanan', 20);
            $table->date('tanggal_akta');
            $table->string('sifat_jenis_akta', 100);
            $table->text('alamat')->nullable();
            $table->time('jam')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skmht');
    }
};