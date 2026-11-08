<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legalisasi', function (Blueprint $table) {
            $table->id('id_legalisasi');
            $table->string('no_urut', 20);
            $table->date('tanggal');
            $table->string('sifat_surat', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legalisasi');
    }
};