<?php

namespace Tests\Feature;

use App\Models\Haki;
use App\Models\JenisHaki;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminJenisHakiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * 1. Guest tidak dapat membuka /admin/jenis-haki.
     */
    public function test_guest_cannot_access_admin_jenis_haki(): void
    {
        $response = $this->get('/admin/jenis-haki');

        $response->assertRedirect('/login');
    }

    /**
     * 2. User tidak dapat membuka /admin/jenis-haki.
     */
    public function test_user_cannot_access_admin_jenis_haki(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)->get('/admin/jenis-haki');

        $response->assertRedirect('/my-haki');
    }

    /**
     * 3. Admin dapat membuka halaman daftar Jenis HAKI.
     */
    public function test_admin_can_access_admin_jenis_haki_index(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/jenis-haki');

        $response->assertStatus(200);
        $response->assertSee('Kelola Master Jenis HAKI');
    }

    /**
     * 4. Admin dapat membuka form tambah.
     */
    public function test_admin_can_access_create_form(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/jenis-haki/create');

        $response->assertStatus(200);
        $response->assertSee('Tambah Jenis HAKI');
    }

    /**
     * 5. Admin dapat menambahkan Jenis HAKI.
     */
    public function test_admin_can_store_jenis_haki(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/jenis-haki', [
            'nama' => 'Hak Cipta Baru',
        ]);

        $response->assertRedirect('/admin/jenis-haki');
        $this->assertDatabaseHas('jenis_hakis', [
            'nama' => 'Hak Cipta Baru',
            'status' => 'aktif',
        ]);
    }

    /**
     * 6. Nama kosong ditolak.
     */
    public function test_empty_name_is_rejected(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/jenis-haki', [
            'nama' => '',
        ]);

        $response->assertSessionHasErrors(['nama']);
        $this->assertDatabaseCount('jenis_hakis', 0);
    }

    /**
     * 7. Nama duplikat ditolak.
     */
    public function test_duplicate_name_is_rejected(): void
    {
        $admin = User::where('role', 'admin')->first();

        JenisHaki::create([
            'nama' => 'Paten Tambahan',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->post('/admin/jenis-haki', [
            'nama' => 'Paten Tambahan',
        ]);

        $response->assertSessionHasErrors(['nama']);
        $this->assertDatabaseCount('jenis_hakis', 1);
    }

    /**
     * 8. Admin dapat membuka form edit.
     */
    public function test_admin_can_access_edit_form(): void
    {
        $admin = User::where('role', 'admin')->first();
        $jenisHaki = JenisHaki::create([
            'nama' => 'Merek Tes',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->get("/admin/jenis-haki/{$jenisHaki->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('Edit Jenis HAKI');
        $response->assertSee('Merek Tes');
    }

    /**
     * 9. Admin dapat mengubah nama.
     */
    public function test_admin_can_update_jenis_haki_name(): void
    {
        $admin = User::where('role', 'admin')->first();
        $jenisHaki = JenisHaki::create([
            'nama' => 'Nama Lama',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->put("/admin/jenis-haki/{$jenisHaki->id}", [
            'nama' => 'Nama Baru',
        ]);

        $response->assertRedirect('/admin/jenis-haki');
        $this->assertDatabaseHas('jenis_hakis', [
            'id' => $jenisHaki->id,
            'nama' => 'Nama Baru',
        ]);
    }

    /**
     * 10. Admin dapat menonaktifkan master Jenis HAKI.
     */
    public function test_admin_can_deactivate_jenis_haki(): void
    {
        $admin = User::where('role', 'admin')->first();
        $jenisHaki = JenisHaki::create([
            'nama' => 'Desain Industri Tes',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->patch("/admin/jenis-haki/{$jenisHaki->id}/toggle-status");

        $response->assertRedirect('/admin/jenis-haki');
        $this->assertDatabaseHas('jenis_hakis', [
            'id' => $jenisHaki->id,
            'status' => 'nonaktif',
        ]);
    }

    /**
     * 11. Admin dapat mengaktifkan kembali master Jenis HAKI.
     */
    public function test_admin_can_reactivate_jenis_haki(): void
    {
        $admin = User::where('role', 'admin')->first();
        $jenisHaki = JenisHaki::create([
            'nama' => 'Rahasia Dagang Tes',
            'status' => 'nonaktif',
        ]);

        $response = $this->actingAs($admin)->patch("/admin/jenis-haki/{$jenisHaki->id}/toggle-status");

        $response->assertRedirect('/admin/jenis-haki');
        $this->assertDatabaseHas('jenis_hakis', [
            'id' => $jenisHaki->id,
            'status' => 'aktif',
        ]);
    }

    /**
     * 12. Menonaktifkan master Jenis HAKI tidak menghapus HAKI yang sudah menggunakannya.
     */
    public function test_deactivating_jenis_haki_does_not_delete_haki(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        $jenisHaki = JenisHaki::create([
            'nama' => 'Karya Seni',
            'status' => 'aktif',
        ]);

        $haki = Haki::create([
            'user_id' => $user->id,
            'jenis_haki_id' => $jenisHaki->id,
            'judul_haki' => 'Lukisan Pemandangan',
            'jenis_haki' => 'Karya Seni',
            'nomor_sertifikat' => 'CERT-12345',
            'tanggal_terbit' => '2025-01-01',
            'tahun' => 2025,
            'deskripsi' => 'Deskripsi HAKI',
            'file_sertifikat' => 'sertifikat.pdf',
            'status' => 'aktif',
            'is_public' => true,
        ]);

        $response = $this->actingAs($admin)->patch("/admin/jenis-haki/{$jenisHaki->id}/toggle-status");

        $response->assertRedirect('/admin/jenis-haki');

        // Verify Jenis HAKI status is nonaktif
        $this->assertDatabaseHas('jenis_hakis', [
            'id' => $jenisHaki->id,
            'status' => 'nonaktif',
        ]);

        // Verify HAKI record is intact and untouched
        $this->assertDatabaseHas('hakis', [
            'id' => $haki->id,
            'jenis_haki_id' => $jenisHaki->id,
            'status' => 'aktif',
        ]);
    }

    /**
     * 13. Data HAKI lama tetap aman.
     */
    public function test_existing_haki_data_remains_safe(): void
    {
        $user = User::where('role', 'user')->first();

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'Sistem Antigravitasi',
            'jenis_haki' => 'Paten Modul',
            'nomor_sertifikat' => 'CERT-99999',
            'tanggal_terbit' => '2024-06-01',
            'tahun' => 2024,
            'deskripsi' => 'Modul paten lama',
            'file_sertifikat' => 'paten.pdf',
            'status' => 'aktif',
            'is_public' => true,
        ]);

        $this->assertDatabaseHas('hakis', [
            'id' => $haki->id,
            'jenis_haki' => 'Paten Modul',
            'is_public' => true,
        ]);
    }

    /**
     * 14. Tidak ada error syntax & 15. Tidak ada data Jenis HAKI default yang dibuat otomatis.
     */
    public function test_no_default_jenis_haki_created_automatically(): void
    {
        $this->assertEquals(0, JenisHaki::count());
    }
}
