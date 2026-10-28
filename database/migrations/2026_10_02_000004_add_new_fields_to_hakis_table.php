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
        Schema::table('hakis', function (Blueprint $table) {
            $table->foreignId('jenis_haki_id')->nullable()->after('user_id')->constrained('jenis_hakis')->nullOnDelete();
            $table->foreignId('faculty_id')->nullable()->after('jenis_haki_id')->constrained('faculties')->nullOnDelete();
            $table->foreignId('study_program_id')->nullable()->after('faculty_id')->constrained('study_programs')->nullOnDelete();
            $table->date('tanggal_kadaluarsa')->nullable()->after('tanggal_terbit');
            $table->string('status')->default('aktif')->after('is_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hakis', function (Blueprint $table) {
            $table->dropForeign(['jenis_haki_id']);
            $table->dropForeign(['faculty_id']);
            $table->dropForeign(['study_program_id']);
            $table->dropColumn([
                'jenis_haki_id',
                'faculty_id',
                'study_program_id',
                'tanggal_kadaluarsa',
                'status',
            ]);
        });
    }
};
