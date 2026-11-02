<?php

namespace Tests\Feature;

use App\Models\Haki;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminHakiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');
    }

    public function test_guest_cannot_access_admin_haki(): void
    {
        $response = $this->get('/admin/haki');

        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_admin_haki(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)->get('/admin/haki');

        $response->assertRedirect('/my-haki');
    }

    public function test_admin_can_access_admin_haki(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/haki');

        $response->assertStatus(200);
        $response->assertSee('Kelola Data HAKI');
    }

    public function test_admin_can_search_haki_data(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'Sistem AI Terpusat',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-999',
            'tanggal_terbit' => '2025-01-01',
            'tahun' => 2025,
            'deskripsi' => 'Deskripsi AI',
            'file_sertifikat' => 'certificates/cert-999.pdf',
            'is_public' => true,
        ]);

        Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'Aplikasi Mobile Pembelajaran',
            'jenis_haki' => 'Paten',
            'nomor_sertifikat' => 'CERT-100',
            'tanggal_terbit' => '2024-05-05',
            'tahun' => 2024,
            'deskripsi' => 'Deskripsi Mobile',
            'file_sertifikat' => 'certificates/cert-100.pdf',
            'is_public' => false,
        ]);

        $response = $this->actingAs($admin)->get('/admin/haki?search=Terpusat');

        $response->assertStatus(200);
        $response->assertSee('Sistem AI Terpusat');
        $response->assertDontSee('Aplikasi Mobile Pembelajaran');

        $responseCert = $this->actingAs($admin)->get('/admin/haki?search=CERT-100');

        $responseCert->assertStatus(200);
        $responseCert->assertSee('Aplikasi Mobile Pembelajaran');
        $responseCert->assertDontSee('Sistem AI Terpusat');

        $responseUser = $this->actingAs($admin)->get(
            '/admin/haki?search=' . urlencode($user->name)
        );

        $responseUser->assertStatus(200);
        $responseUser->assertSee('Sistem AI Terpusat');
        $responseUser->assertSee('Aplikasi Mobile Pembelajaran');
    }

    public function test_admin_can_filter_haki_by_jenis_and_tahun(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'Inovasi Cipta 2023',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-2023',
            'tanggal_terbit' => '2023-01-01',
            'file_sertifikat' => 'certificates/cert-2023.pdf',
            'tahun' => 2023,
            'is_public' => true,
        ]);

        Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'Inovasi Paten 2024',
            'jenis_haki' => 'Paten',
            'nomor_sertifikat' => 'CERT-2024',
            'tanggal_terbit' => '2024-01-01',
            'file_sertifikat' => 'certificates/cert-2024.pdf',
            'tahun' => 2024,
            'is_public' => true,
        ]);

        $resJenis = $this->actingAs($admin)->get('/admin/haki?jenis=Hak+Cipta');

        $resJenis->assertSee('Inovasi Cipta 2023');
        $resJenis->assertDontSee('Inovasi Paten 2024');

        $resTahun = $this->actingAs($admin)->get('/admin/haki?tahun=2024');

        $resTahun->assertSee('Inovasi Paten 2024');
        $resTahun->assertDontSee('Inovasi Cipta 2023');
    }

    public function test_admin_pagination_retains_query_string(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        for ($i = 1; $i <= 15; $i++) {
            Haki::create([
                'user_id' => $user->id,
                'judul_haki' => "HAKI Item $i",
                'jenis_haki' => 'Hak Cipta',
                'nomor_sertifikat' => "CERT-PAG-$i",
                'tanggal_terbit' => '2025-01-01',
                'file_sertifikat' => "certificates/cert-pag-$i.pdf",
                'tahun' => 2025,
                'is_public' => true,
            ]);
        }

        $response = $this->actingAs($admin)->get(
            '/admin/haki?jenis=Hak+Cipta&page=2'
        );

        $response->assertStatus(200);
        $response->assertSee('jenis=Hak%20Cipta');
    }

    public function test_guest_cannot_access_admin_haki_detail(): void
    {
        $user = User::where('role', 'user')->first();

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'Detail Test HAKI',
            'jenis_haki' => 'Paten',
            'nomor_sertifikat' => 'CERT-DET-1',
            'tanggal_terbit' => '2025-01-01',
            'file_sertifikat' => 'certificates/cert-det-1.pdf',
            'tahun' => 2025,
            'is_public' => true,
        ]);

        $response = $this->get('/admin/haki/' . $haki->id);

        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_admin_haki_detail(): void
    {
        $user = User::where('role', 'user')->first();

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'Detail Test HAKI',
            'jenis_haki' => 'Paten',
            'nomor_sertifikat' => 'CERT-DET-1',
            'tanggal_terbit' => '2025-01-01',
            'file_sertifikat' => 'certificates/cert-det-1.pdf',
            'tahun' => 2025,
            'is_public' => true,
        ]);

        $response = $this->actingAs($user)->get('/admin/haki/' . $haki->id);

        $response->assertRedirect('/my-haki');
    }

    public function test_admin_can_access_admin_haki_detail(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'Detail Test HAKI Spesifik',
            'jenis_haki' => 'Desain Industri',
            'nomor_sertifikat' => 'CERT-DET-99',
            'tanggal_terbit' => '2025-06-15',
            'deskripsi' => 'Deskripsi detail tes HAKI',
            'file_sertifikat' => 'certificates/cert-det-99.pdf',
            'tahun' => 2025,
            'is_public' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/haki/' . $haki->id);

        $response->assertStatus(200);
        $response->assertSee('Detail HAKI');
        $response->assertSee('Detail Test HAKI Spesifik');
        $response->assertSee('Desain Industri');
        $response->assertSee('CERT-DET-99');
        $response->assertSee('certificates/cert-det-99.pdf');
        $response->assertSee($user->name);
        $response->assertSee('Kembali ke Kelola HAKI');
        $response->assertSee('Edit');
    }

    public function test_admin_gets_404_for_non_existent_haki_detail(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/haki/999999');

        $response->assertStatus(404);
    }

    public function test_guest_cannot_access_edit_or_update(): void
    {
        $user = User::where('role', 'user')->first();

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'HAKI Edit Guest',
            'jenis_haki' => 'Merek',
            'nomor_sertifikat' => 'CERT-EDIT-GUEST',
            'tanggal_terbit' => '2025-01-01',
            'file_sertifikat' => 'certificates/cert-guest.pdf',
            'tahun' => 2025,
            'is_public' => true,
        ]);

        $responseEdit = $this->get("/admin/haki/{$haki->id}/edit");

        $responseEdit->assertRedirect('/login');

        $responseUpdate = $this->put("/admin/haki/{$haki->id}", [
            'judul_haki' => 'Updated Judul',
        ]);

        $responseUpdate->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_edit_or_update(): void
    {
        $user = User::where('role', 'user')->first();

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'HAKI Edit User',
            'jenis_haki' => 'Merek',
            'nomor_sertifikat' => 'CERT-EDIT-USER',
            'tanggal_terbit' => '2025-01-01',
            'file_sertifikat' => 'certificates/cert-user.pdf',
            'tahun' => 2025,
            'is_public' => true,
        ]);

        $responseEdit = $this->actingAs($user)->get("/admin/haki/{$haki->id}/edit");

        $responseEdit->assertRedirect('/my-haki');

        $responseUpdate = $this->actingAs($user)->put("/admin/haki/{$haki->id}", [
            'judul_haki' => 'Updated Judul',
        ]);

        $responseUpdate->assertRedirect('/my-haki');
    }

    public function test_admin_can_open_edit_form(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'HAKI Edit Form Test',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-EDIT-FORM',
            'tanggal_terbit' => '2025-02-02',
            'file_sertifikat' => 'certificates/cert-form.pdf',
            'tahun' => 2025,
            'is_public' => true,
        ]);

        $response = $this->actingAs($admin)->get("/admin/haki/{$haki->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('Edit Data HAKI');
        $response->assertSee('HAKI Edit Form Test');
        $response->assertSee($user->name);
    }

    public function test_admin_can_update_haki_data_and_user_id_remains_unchanged(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'Judul Lama',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-LAMA',
            'tanggal_terbit' => '2023-01-01',
            'file_sertifikat' => 'certificates/cert-lama.pdf',
            'tahun' => 2023,
            'deskripsi' => 'Deskripsi Lama',
            'is_public' => true,
        ]);

        $otherUser = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->put("/admin/haki/{$haki->id}", [
            'user_id' => $otherUser->id,
            'judul_haki' => 'Judul Baru yang Diperbarui',
            'jenis_haki' => 'Paten',
            'nomor_sertifikat' => 'CERT-BARU-888',
            'tanggal_terbit' => '2025-05-05',
            'tahun' => 2025,
            'deskripsi' => 'Deskripsi Baru',
        ]);

        $response->assertRedirect(route('admin.haki.show', $haki));
        $response->assertSessionHas('success', 'Data HAKI berhasil diperbarui.');

        $haki->refresh();

        $this->assertEquals('Judul Baru yang Diperbarui', $haki->judul_haki);
        $this->assertEquals('Paten', $haki->jenis_haki);
        $this->assertEquals('CERT-BARU-888', $haki->nomor_sertifikat);
        $this->assertEquals('2025-05-05', $haki->tanggal_terbit->format('Y-m-d'));
        $this->assertEquals(2025, $haki->tahun);
        $this->assertEquals('Deskripsi Baru', $haki->deskripsi);
        $this->assertEquals($user->id, $haki->user_id);
    }

    public function test_admin_can_update_without_new_file(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'HAKI Tetap File',
            'jenis_haki' => 'Merek',
            'nomor_sertifikat' => 'CERT-KEEP-FILE',
            'tanggal_terbit' => '2024-10-10',
            'file_sertifikat' => 'certificates/original-cert.pdf',
            'tahun' => 2024,
            'is_public' => true,
        ]);

        $response = $this->actingAs($admin)->put("/admin/haki/{$haki->id}", [
            'judul_haki' => 'HAKI Tetap File Updated',
            'jenis_haki' => 'Merek',
            'nomor_sertifikat' => 'CERT-KEEP-FILE',
            'tanggal_terbit' => '2024-10-10',
            'tahun' => 2024,
        ]);

        $response->assertRedirect(route('admin.haki.show', $haki));

        $haki->refresh();

        $this->assertEquals(
            'certificates/original-cert.pdf',
            $haki->file_sertifikat
        );
    }

    public function test_admin_can_update_with_new_file_deletes_old_file(): void
    {
        // File baru harus disimpan di disk privat (local).
        // File lama mungkin masih berada di disk public.
        Storage::fake('local');
        Storage::fake('public');

        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        // Simulasikan file lama yang masih tersimpan di disk public.
        $oldFilePath = Storage::disk('public')->putFile(
            'certificates',
            UploadedFile::fake()->create('old_cert.pdf', 100)
        );

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'HAKI Ganti File',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-SWAP-FILE',
            'tanggal_terbit' => '2025-01-01',
            'file_sertifikat' => $oldFilePath,
            'tahun' => 2025,
            'is_public' => true,
        ]);

        Storage::disk('public')->assertExists($oldFilePath);

        $newFile = UploadedFile::fake()->create(
            'new_sertifikat.pdf',
            500,
            'application/pdf'
        );

        $response = $this->actingAs($admin)->put("/admin/haki/{$haki->id}", [
            'judul_haki' => 'HAKI Ganti File Updated',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-SWAP-FILE',
            'tanggal_terbit' => '2025-01-01',
            'tahun' => 2025,
            'file_sertifikat' => $newFile,
        ]);

        $response->assertRedirect(route('admin.haki.show', $haki));

        $haki->refresh();

        // Pastikan file lama di disk public sudah dihapus.
        Storage::disk('public')->assertMissing($oldFilePath);

        // Pastikan file sertifikat baru tersimpan pada disk privat.
        Storage::disk('local')->assertExists($haki->file_sertifikat);

        // Pastikan file baru tidak tersimpan pada disk public.
        Storage::disk('public')->assertMissing($haki->file_sertifikat);

        // Pastikan lokasi file sertifikat telah berubah.
        $this->assertNotEquals($oldFilePath, $haki->file_sertifikat);
    }

    public function test_update_file_size_exceeding_3mb_is_rejected(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'HAKI Large File Test',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-LARGE-FILE',
            'tanggal_terbit' => '2025-01-01',
            'file_sertifikat' => 'certificates/small.pdf',
            'tahun' => 2025,
            'is_public' => true,
        ]);

        // 3073 KB melebihi batas 3072 KB.
        $largeFile = UploadedFile::fake()->create('large.pdf', 3073);

        $response = $this->actingAs($admin)->put("/admin/haki/{$haki->id}", [
            'judul_haki' => 'HAKI Large File Test',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-LARGE-FILE',
            'tanggal_terbit' => '2025-01-01',
            'tahun' => 2025,
            'file_sertifikat' => $largeFile,
        ]);

        $response->assertSessionHasErrors(['file_sertifikat']);
    }

    public function test_update_invalid_file_format_is_rejected(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'HAKI Invalid Format Test',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-INVALID-FORMAT',
            'tanggal_terbit' => '2025-01-01',
            'file_sertifikat' => 'certificates/valid.pdf',
            'tahun' => 2025,
            'is_public' => true,
        ]);

        $invalidFile = UploadedFile::fake()->create('script.exe', 100);

        $response = $this->actingAs($admin)->put("/admin/haki/{$haki->id}", [
            'judul_haki' => 'HAKI Invalid Format Test',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-INVALID-FORMAT',
            'tanggal_terbit' => '2025-01-01',
            'tahun' => 2025,
            'file_sertifikat' => $invalidFile,
        ]);

        $response->assertSessionHasErrors(['file_sertifikat']);
    }

    public function test_update_validation_required_fields(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        $haki = Haki::create([
            'user_id' => $user->id,
            'judul_haki' => 'HAKI Validation Test',
            'jenis_haki' => 'Hak Cipta',
            'nomor_sertifikat' => 'CERT-VAL-1',
            'tanggal_terbit' => '2025-01-01',
            'file_sertifikat' => 'certificates/valid.pdf',
            'tahun' => 2025,
            'is_public' => true,
        ]);

        $response = $this->actingAs($admin)->put("/admin/haki/{$haki->id}", [
            'judul_haki' => '',
            'jenis_haki' => '',
            'nomor_sertifikat' => '',
            'tanggal_terbit' => '',
            'tahun' => '',
        ]);

        $response->assertSessionHasErrors([
            'judul_haki',
            'jenis_haki',
            'nomor_sertifikat',
            'tanggal_terbit',
            'tahun',
        ]);
    }
}
