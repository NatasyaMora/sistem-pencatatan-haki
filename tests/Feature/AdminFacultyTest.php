<?php

namespace Tests\Feature;

use App\Models\Faculty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFacultyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * 1. Admin dapat melihat halaman Fakultas.
     */
    public function test_admin_can_access_fakultas_index(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/fakultas');

        $response->assertStatus(200);
        $response->assertSee('Kelola Master Fakultas');
    }

    /**
     * 2. Admin dapat menambah Fakultas.
     */
    public function test_admin_can_store_fakultas(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/fakultas', [
            'nama' => 'Fakultas Ilmu Komputer',
        ]);

        $response->assertRedirect('/admin/fakultas');
        $this->assertDatabaseHas('faculties', [
            'nama' => 'Fakultas Ilmu Komputer',
            'status' => 'aktif',
        ]);
    }

    /**
     * 3. Nama Fakultas wajib diisi.
     */
    public function test_empty_fakultas_name_is_rejected(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/fakultas', [
            'nama' => '',
        ]);

        $response->assertSessionHasErrors(['nama']);
        $this->assertDatabaseCount('faculties', 0);
    }

    /**
     * 4. Nama Fakultas harus unique.
     */
    public function test_duplicate_fakultas_name_is_rejected(): void
    {
        $admin = User::where('role', 'admin')->first();

        Faculty::create([
            'nama' => 'Fakultas Teknik',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->post('/admin/fakultas', [
            'nama' => 'Fakultas Teknik',
        ]);

        $response->assertSessionHasErrors(['nama']);
        $this->assertDatabaseCount('faculties', 1);
    }

    /**
     * 5. Admin dapat mengedit Fakultas.
     */
    public function test_admin_can_edit_fakultas(): void
    {
        $admin = User::where('role', 'admin')->first();
        $faculty = Faculty::create([
            'nama' => 'Fakultas Sains',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->get("/admin/fakultas/{$faculty->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Edit Fakultas');

        $updateResponse = $this->actingAs($admin)->put("/admin/fakultas/{$faculty->id}", [
            'nama' => 'Fakultas Sains Terapan',
        ]);

        $updateResponse->assertRedirect('/admin/fakultas');
        $this->assertDatabaseHas('faculties', [
            'id' => $faculty->id,
            'nama' => 'Fakultas Sains Terapan',
        ]);
    }

    /**
     * 6. Admin dapat toggle status Fakultas.
     */
    public function test_admin_can_toggle_fakultas_status(): void
    {
        $admin = User::where('role', 'admin')->first();
        $faculty = Faculty::create([
            'nama' => 'Fakultas Ekonomi',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->patch("/admin/fakultas/{$faculty->id}/toggle-status");

        $response->assertRedirect('/admin/fakultas');
        $this->assertDatabaseHas('faculties', [
            'id' => $faculty->id,
            'status' => 'nonaktif',
        ]);

        // Toggle back to active
        $response2 = $this->actingAs($admin)->patch("/admin/fakultas/{$faculty->id}/toggle-status");
        $response2->assertRedirect('/admin/fakultas');
        $this->assertDatabaseHas('faculties', [
            'id' => $faculty->id,
            'status' => 'aktif',
        ]);
    }

    /**
     * 7. User biasa tidak dapat mengakses halaman Fakultas.
     */
    public function test_user_cannot_access_fakultas(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)->get('/admin/fakultas');

        $response->assertRedirect('/my-haki');
    }

    /**
     * 8. Guest diarahkan ke login.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/fakultas');

        $response->assertRedirect('/login');
    }
}
