<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('krs', function (Blueprint $table): void {
            $table->id();
            $table->string('nim', 20);
            $table->string('kode_mata_kuliah', 30);
            $table->string('nama_mata_kuliah', 120);
            $table->unsignedTinyInteger('sks');
            $table->string('tahun_ajaran', 9);
            $table->enum('semester', ['ganjil', 'genap', 'pendek']);
            $table->enum('status_persetujuan', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['nim', 'tahun_ajaran', 'semester']);
            $table->unique(
                ['nim', 'kode_mata_kuliah', 'tahun_ajaran', 'semester'],
                'krs_unique_taken_course'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('krs');
    }
};
