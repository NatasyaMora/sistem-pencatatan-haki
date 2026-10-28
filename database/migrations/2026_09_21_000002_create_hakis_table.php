<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hakis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('judul_haki');
            $table->string('jenis_haki');
            $table->string('nomor_sertifikat');
            $table->date('tanggal_terbit');
            $table->integer('tahun');
            $table->text('deskripsi')->nullable();
            $table->string('file_sertifikat');
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hakis');
    }
};
