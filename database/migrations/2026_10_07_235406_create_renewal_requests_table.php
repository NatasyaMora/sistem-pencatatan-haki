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
        Schema::create('renewal_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('haki_id')
                ->constrained('hakis')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('tanggal_kadaluarsa_baru');

            $table->string('nomor_sertifikat_baru')->nullable();

            $table->string('file_sertifikat_baru')->nullable();

            $table->text('catatan')->nullable();

            $table->enum('status', [
                'menunggu',
                'dikonfirmasi',
                'ditolak'
            ])->default('menunggu');

            $table->text('catatan_admin')->nullable();

            $table->timestamp('diproses_pada')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renewal_requests');
    }
};