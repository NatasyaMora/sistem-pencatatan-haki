<?php

namespace Tests\Feature;

use App\Models\Faculty;
use App\Models\Haki;
use App\Models\JenisHaki;
use App\Models\RenewalRequest;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HakiStatusAndPublicationTest extends TestCase
{
    use RefreshDatabase;

    protected Faculty $faculty;
    protected StudyProgram $studyProgram;
    protected JenisHaki $jenisHaki;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');

        $this->faculty = Faculty::firstOrCreate(
            ['nama' => 'Fakultas Sains dan Teknologi'],
            ['status' => 'aktif']
        );

        $this->studyProgram = StudyProgram::firstOrCreate(
            ['nama' => 'Teknik Informatika', 'faculty_id' => $this->faculty->id],
            ['status' => 'aktif']
        );

        $this->jenisHaki = JenisHaki::firstOrCreate(
            ['nama' => 'Hak Cipta'],
            ['status' => 'aktif']
        );
    }

    /**
     * 1. Pencatatan HAKI tanpa tanggal kedaluwarsa ditolak.
     */
    public function test_1_store_haki_without_tanggal_kadaluarsa_is_rejected(): void
    {
        Storage::fake('local');

        $user = User::where('role', 'user')->first();
        $file = UploadedFile::fake()->create('sertifikat.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post(route('user.haki.store'), [
            'judul_haki' => 'Sistem Pemantauan HAKI Kampus',
            'jenis_haki_id' => $this->jenisHaki->id,
            'nomor_sertifikat' => 'CERT-TEST-001',
            'tanggal_terbit' => '2026-01-01',
            'tanggal_kadaluarsa' => '',
            'faculty_id' => $this->faculty->id,
            'study_program_id' => $this->studyProgram->id,
            'deskripsi' => 'Deskripsi HAKI uji coba',
            'file_sertifikat' => $file,
        ]);

        $response->assertSessionHasErrors(['tanggal_kadaluarsa']);
        $this->assertDatabaseMissing('hakis', [
            'nomor_sertifikat' => 'CERT-TEST-001',
        ]);
    }

    /**
     * 2. Tanggal kedaluwarsa sebelum tanggal terbit ditolak.
     */
    public function test_2_store_haki_with_tanggal_kadaluarsa_before_tanggal_terbit_is_rejected(): void
    {
        Storage::fake('local');

        $user = User::where('role', 'user')->first();
        $file = UploadedFile::fake()->create('sertifikat.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post(route('user.haki.store'), [
            'judul_haki' => 'Sistem Pemantauan HAKI Kampus',
            'jenis_haki_id' => $this->jenisHaki->id,
            'nomor_sertifikat' => 'CERT-TEST-002',
            'tanggal_terbit' => '2026-05-10',
            'tanggal_kadaluarsa' => '2026-05-09',
            'faculty_id' => $this->faculty->id,
            'study_program_id' => $this->studyProgram->id,
            'deskripsi' => 'Deskripsi HAKI uji coba',
            'file_sertifikat' => $file,
        ]);

        $response->assertSessionHasErrors(['tanggal_kadaluarsa']);
        $this->assertDatabaseMissing('hakis', [
            'nomor_sertifikat' => 'CERT-TEST-002',
        ]);
    }

    /**
     * 3. HAKI dengan tanggal kedaluwarsa besok berstatus aktif dan dipublikasikan.
     */
    public function test_3_store_haki_with_tomorrow_expiry_date_is_active_and_public(): void
    {
        Storage::fake('local');

        $user = User::where('role', 'user')->first();
        $tomorrow = now('Asia/Jakarta')->addDay()->toDateString();
        $file = UploadedFile::fake()->create('sertifikat.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post(route('user.haki.store'), [
            'judul_haki' => 'Sistem IoT Pertanian Cerdas',
            'jenis_haki_id' => $this->jenisHaki->id,
            'nomor_sertifikat' => 'CERT-TEST-003',
            'tanggal_terbit' => '2026-01-01',
            'tanggal_kadaluarsa' => $tomorrow,
            'faculty_id' => $this->faculty->id,
            'study_program_id' => $this->studyProgram->id,
            'deskripsi' => 'Deskripsi sistem IoT cerdas',
            'file_sertifikat' => $file,
        ]);

        $response->assertRedirect(route('user.haki.index'));
        $this->assertDatabaseHas('hakis', [
            'nomor_sertifikat' => 'CERT-TEST-003',
            'status' => 'aktif',
            'is_public' => true,
        ]);
    }

    /**
     * 4. HAKI dengan tanggal kedaluwarsa hari ini masih aktif.
     */
    public function test_4_store_haki_with_today_expiry_date_is_active(): void
    {
        Storage::fake('local');

        $user = User::where('role', 'user')->first();
        $today = now('Asia/Jakarta')->toDateString();
        $file = UploadedFile::fake()->create('sertifikat.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post(route('user.haki.store'), [
            'judul_haki' => 'Aplikasi Pembelajaran Mandiri',
            'jenis_haki_id' => $this->jenisHaki->id,
            'nomor_sertifikat' => 'CERT-TEST-004',
            'tanggal_terbit' => $today,
            'tanggal_kadaluarsa' => $today,
            'faculty_id' => $this->faculty->id,
            'study_program_id' => $this->studyProgram->id,
            'deskripsi' => 'Deskripsi aplikasi pembelajaran',
            'file_sertifikat' => $file,
        ]);

        $response->assertRedirect(route('user.haki.index'));
        $this->assertDatabaseHas('hakis', [
            'nomor_sertifikat' => 'CERT-TEST-004',
            'status' => 'aktif',
            'is_public' => true,
        ]);
    }

    /**
     * 5. HAKI dengan tanggal kedaluwarsa kemarin menjadi nonaktif dan tidak dipublikasikan.
     */
    public function test_5_haki_with_yesterday_expiry_date_becomes_inactive_and_not_public(): void
    {
        $user = User::where('role', 'user')->first();
        $yesterday = now('Asia/Jakarta')->subDay()->toDateString();

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'Karya Ilmiah Lama',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-TEST-005',
            'tanggal_terbit' => '2024-01-01',
            'tanggal_kadaluarsa' => $yesterday,
            'tahun' => 2024,
            'file_sertifikat' => 'certificates/cert-005.pdf',
            'status' => 'aktif',
            'is_public' => true,
        ]);

        // Jalankan perintah pengecekan kedaluwarsa
        $this->artisan('haki:check-expiry')->assertExitCode(0);

        $haki->refresh();
        $this->assertEquals('nonaktif', $haki->status);
        $this->assertFalse((bool) $haki->is_public);
    }

    /**
     * 6. HAKI kedaluwarsa tidak muncul di dashboard publik.
     */
    public function test_6_expired_haki_does_not_appear_on_public_dashboard(): void
    {
        $user = User::where('role', 'user')->first();
        $tomorrow = now('Asia/Jakarta')->addDay()->toDateString();
        $yesterday = now('Asia/Jakarta')->subDay()->toDateString();

        // 1 HAKI Aktif masih berlaku
        Haki::create([
            'user_id' => $user->id,
            'faculty_id' => $this->faculty->id,
            'study_program_id' => $this->studyProgram->id,
            'judul_haki' => 'HAKI Aktif Masih Berlaku',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-PUB-VALID',
            'tanggal_terbit' => '2025-01-01',
            'tanggal_kadaluarsa' => $tomorrow,
            'tahun' => 2025,
            'file_sertifikat' => 'certificates/valid.pdf',
            'status' => 'aktif',
            'is_public' => true,
        ]);

        // 1 HAKI Kedaluwarsa
        Haki::create([
            'user_id' => $user->id,
            'faculty_id' => $this->faculty->id,
            'study_program_id' => $this->studyProgram->id,
            'judul_haki' => 'HAKI Sudah Kedaluwarsa',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-PUB-EXPIRED',
            'tanggal_terbit' => '2024-01-01',
            'tanggal_kadaluarsa' => $yesterday,
            'tahun' => 2024,
            'file_sertifikat' => 'certificates/expired.pdf',
            'status' => 'nonaktif',
            'is_public' => false,
        ]);

        $response = $this->get(route('public.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalHakiAktif', 1);
    }

    /**
     * 7. Pengguna dapat melihat detail HAKI sendiri.
     */
    public function test_7_user_can_view_own_haki_detail(): void
    {
        $user = User::where('role', 'user')->first();

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'Inovasi Teknologi Energi Bersih',
            'jenis_haki' => 'Paten',
            'nomor_sertifikat' => 'CERT-OWN-123',
            'tanggal_terbit' => '2025-01-01',
            'tanggal_kadaluarsa' => '2027-01-01',
            'tahun' => 2025,
            'deskripsi' => 'Pengembangan energi hijau terbarukan',
            'file_sertifikat' => 'certificates/secret.pdf',
            'status' => 'aktif',
            'is_public' => true,
        ]);

        $response = $this->actingAs($user)->get(route('user.haki.show', $haki->id));

        $response->assertStatus(200);
        $response->assertSee('Inovasi Teknologi Energi Bersih');
        $response->assertSee('CERT-OWN-123');
        $response->assertSee('Paten');
        // Jangan menampilkan atau menyediakan unduhan file sertifikat
        $response->assertDontSee('certificates/secret.pdf');
    }

    /**
     * 8. Pengguna tidak dapat melihat detail HAKI milik orang lain (404).
     */
    public function test_8_user_cannot_view_other_user_haki_detail_returns_404(): void
    {
        $userA = User::where('role', 'user')->first();

        $userB = User::create([
            'name' => 'Pengguna Lain',
            'email' => 'otheruser@example.com',
            'password' => bcrypt('password'),
            'identity_number' => 'NIM999999',
            'user_type' => 'mahasiswa',
            'role' => 'user',
        ]);

        $hakiUserB = Haki::create([
            'user_id' => $userB->id,
            'judul_haki' => 'HAKI Rahasia Milik User B',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-SECRET-B',
            'tanggal_terbit' => '2025-01-01',
            'tanggal_kadaluarsa' => '2027-01-01',
            'tahun' => 2025,
            'file_sertifikat' => 'certificates/user_b.pdf',
            'status' => 'aktif',
            'is_public' => true,
        ]);

        // User A mencoba mengakses HAKI milik User B
        $response = $this->actingAs($userA)->get(route('user.haki.show', $hakiUserB->id));

        $response->assertStatus(404);
    }

    /**
     * 9. Admin dapat menonaktifkan HAKI tanpa menghapus data.
     */
    public function test_9_admin_can_deactivate_haki_without_deleting_data(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'HAKI Aktif Akan Dinonaktifkan',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-DEACTIVATE-1',
            'tanggal_terbit' => '2025-01-01',
            'tanggal_kadaluarsa' => '2027-01-01',
            'tahun' => 2025,
            'file_sertifikat' => 'certificates/deact.pdf',
            'status' => 'aktif',
            'is_public' => true,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.haki.toggle-status', $haki));

        $response->assertSessionHas('success');
        $haki->refresh();

        $this->assertEquals('nonaktif', $haki->status);
        $this->assertFalse((bool) $haki->is_public);
        $this->assertDatabaseHas('hakis', [
            'id' => $haki->id,
            'nomor_sertifikat' => 'CERT-DEACTIVATE-1',
        ]);
    }

    /**
     * 10. Admin dapat mengaktifkan kembali HAKI yang belum kedaluwarsa.
     */
    public function test_10_admin_can_reactivate_haki_that_is_not_expired(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'HAKI Nonaktif Masa Berlaku Valid',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-REACTIVATE-1',
            'tanggal_terbit' => '2025-01-01',
            'tanggal_kadaluarsa' => '2028-01-01',
            'tahun' => 2025,
            'file_sertifikat' => 'certificates/react.pdf',
            'status' => 'nonaktif',
            'is_public' => false,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.haki.toggle-status', $haki));

        $response->assertSessionHas('success');
        $haki->refresh();

        $this->assertEquals('aktif', $haki->status);
        $this->assertTrue((bool) $haki->is_public);
    }

    /**
     * 11. Admin tidak dapat mengaktifkan HAKI yang sudah kedaluwarsa.
     */
    public function test_11_admin_cannot_activate_expired_haki(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();
        $yesterday = now('Asia/Jakarta')->subDay()->toDateString();

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'HAKI Nonaktif Sudah Kedaluwarsa',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-CANNOT-ACTIVATE',
            'tanggal_terbit' => '2024-01-01',
            'tanggal_kadaluarsa' => $yesterday,
            'tahun' => 2024,
            'file_sertifikat' => 'certificates/expired.pdf',
            'status' => 'nonaktif',
            'is_public' => false,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.haki.toggle-status', $haki));

        $response->assertSessionHas('error');
        $haki->refresh();

        $this->assertEquals('nonaktif', $haki->status);
        $this->assertFalse((bool) $haki->is_public);
    }

    /**
     * 12. Pengajuan pembaruan yang dikonfirmasi memperbarui status dan publikasi.
     */
    public function test_12_confirmed_renewal_request_updates_status_and_publication(): void
    {
        Storage::fake('local');

        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();
        $yesterday = now('Asia/Jakarta')->subDay()->toDateString();
        $nextYear = now('Asia/Jakarta')->addYear()->toDateString();

        $hakiFile = 'certificates/original.pdf';
        Storage::disk('local')->put($hakiFile, 'original-content');

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'HAKI Perlu Pembaruan',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-OLD-NUM',
            'tanggal_terbit' => '2024-01-01',
            'tanggal_kadaluarsa' => $yesterday,
            'tahun' => 2024,
            'file_sertifikat' => $hakiFile,
            'status' => 'nonaktif',
            'is_public' => false,
        ]);

        $renewalFile = 'renewal-certificates/renewal-file.pdf';
        Storage::disk('local')->put($renewalFile, 'new-content');

        $renewal = RenewalRequest::create([
            'haki_id' => $haki->id,
            'user_id' => $user->id,
            'tanggal_kadaluarsa_baru' => $nextYear,
            'nomor_sertifikat_baru' => 'CERT-NEW-NUM',
            'file_sertifikat_baru' => $renewalFile,
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($admin)->post(
            route('admin.renewal-requests.confirm', $renewal),
            ['catatan_admin' => 'Disetujui dan diperpanjang']
        );

        $response->assertRedirect(route('admin.renewal-requests.index'));
        $haki->refresh();
        $renewal->refresh();

        $this->assertEquals($nextYear, $haki->tanggal_kadaluarsa->format('Y-m-d'));
        $this->assertEquals('CERT-NEW-NUM', $haki->nomor_sertifikat);
        $this->assertEquals('aktif', $haki->status);
        $this->assertTrue((bool) $haki->is_public);

        $this->assertEquals('dikonfirmasi', $renewal->status);
        $this->assertEquals('Disetujui dan diperpanjang', $renewal->catatan_admin);
        $this->assertNotNull($renewal->diproses_pada);
    }

    /**
     * 13. Pengajuan yang ditolak tidak mengubah masa berlaku HAKI.
     */
    public function test_13_rejected_renewal_request_does_not_change_haki_expiry(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();
        $originalExpiry = '2025-06-30';

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'HAKI Pengajuan Ditolak',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-REJECT-TEST',
            'tanggal_terbit' => '2024-01-01',
            'tanggal_kadaluarsa' => $originalExpiry,
            'tahun' => 2024,
            'file_sertifikat' => 'certificates/cert.pdf',
            'status' => 'nonaktif',
            'is_public' => false,
        ]);

        $renewal = RenewalRequest::create([
            'haki_id' => $haki->id,
            'user_id' => $user->id,
            'tanggal_kadaluarsa_baru' => '2028-06-30',
            'nomor_sertifikat_baru' => 'CERT-SHOULD-NOT-APPLY',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($admin)->post(
            route('admin.renewal-requests.reject', $renewal),
            ['catatan_admin' => 'Dokumen tidak lengkap. Ditolak.']
        );

        $response->assertRedirect(route('admin.renewal-requests.index'));
        $haki->refresh();
        $renewal->refresh();

        // Masa berlaku dan nomor sertifikat HAKI tidak berubah
        $this->assertEquals($originalExpiry, $haki->tanggal_kadaluarsa->format('Y-m-d'));
        $this->assertEquals('CERT-REJECT-TEST', $haki->nomor_sertifikat);
        $this->assertEquals('nonaktif', $haki->status);
        $this->assertFalse((bool) $haki->is_public);

        // Status renewal request menjadi ditolak
        $this->assertEquals('ditolak', $renewal->status);
        $this->assertEquals('Dokumen tidak lengkap. Ditolak.', $renewal->catatan_admin);
        $this->assertNotNull($renewal->diproses_pada);
    }
}
