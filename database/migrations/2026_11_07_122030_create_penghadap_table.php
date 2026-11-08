
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penghadap', function (Blueprint $table) {
            $table->id('id_penghadap');

            $table->foreignId('id_skmht')
                ->nullable()
                ->unique()
                ->constrained('skmht', 'id_skmht')
                ->cascadeOnDelete();

            $table->foreignId('id_legalisasi')
                ->nullable()
                ->unique()
                ->constrained('legalisasi', 'id_legalisasi')
                ->cascadeOnDelete();

            $table->string('nama_penghadap', 150);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penghadap');
    }
};
