<?php

namespace Tests\Feature;

use App\Models\Faculty;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStudyProgramTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * 1. Admin dapat melihat halaman Program Studi.
     */
    public function test_admin_can_access_program_studi_index(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/program-studi');

        $response->assertStatus(200);
        $response->assertSee('Kelola Master Program Studi');
    }

    /**
     * 2 & 4. Admin dapat menambah Program Studi dengan Fakultas yang valid.
     */
    public function test_admin_can_store_program_studi(): void
    {
        $admin = User::where('role', 'admin')->first();
        $faculty = Faculty::create([
            'nama' => 'Fakultas Teknik',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->post('/admin/program-studi', [
            'faculty_id' => $faculty->id,
            'nama' => 'Informatika',
        ]);

        $response->assertRedirect('/admin/program-studi');
        $this->assertDatabaseHas('study_programs', [
            'faculty_id' => $faculty->id,
            'nama' => 'Informatika',
            'status' => 'aktif',
        ]);
    }

    /**
     * 3. Program Studi wajib memiliki Fakultas.
     */
    public function test_program_studi_requires_valid_faculty(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Testing missing faculty_id
        $response = $this->actingAs($admin)->post('/admin/program-studi', [
            'faculty_id' => '',
            'nama' => 'Sistem Informasi',
        ]);
        $response->assertSessionHasErrors(['faculty_id']);

        // Testing invalid faculty_id
        $response2 = $this->actingAs($admin)->post('/admin/program-studi', [
            'faculty_id' => 9999,
            'nama' => 'Sistem Informasi',
        ]);
        $response2->assertSessionHasErrors(['faculty_id']);

        $this->assertDatabaseCount('study_programs', 0);
    }

    /**
     * 5. Nama Program Studi unique dalam Fakultas yang sama.
     */
    public function test_program_studi_name_is_unique_within_same_faculty(): void
    {
        $admin = User::where('role', 'admin')->first();
        $faculty1 = Faculty::create(['nama' => 'Fakultas Teknik', 'status' => 'aktif']);
        $faculty2 = Faculty::create(['nama' => 'Fakultas Ilmu Komputer', 'status' => 'aktif']);

        StudyProgram::create([
            'faculty_id' => $faculty1->id,
            'nama' => 'Informatika',
            'status' => 'aktif',
        ]);

        // Duplicate name within same faculty should fail
        $response = $this->actingAs($admin)->post('/admin/program-studi', [
            'faculty_id' => $faculty1->id,
            'nama' => 'Informatika',
        ]);
        $response->assertSessionHasErrors(['nama']);

        // Same name under a different faculty should succeed
        $response2 = $this->actingAs($admin)->post('/admin/program-studi', [
            'faculty_id' => $faculty2->id,
            'nama' => 'Informatika',
        ]);
        $response2->assertRedirect('/admin/program-studi');
        $this->assertDatabaseHas('study_programs', [
            'faculty_id' => $faculty2->id,
            'nama' => 'Informatika',
        ]);
    }

    /**
     * 6. Admin dapat mengedit Program Studi.
     */
    public function test_admin_can_edit_program_studi(): void
    {
        $admin = User::where('role', 'admin')->first();
        $faculty = Faculty::create(['nama' => 'Fakultas Teknik', 'status' => 'aktif']);
        $studyProgram = StudyProgram::create([
            'faculty_id' => $faculty->id,
            'nama' => 'Teknik Elektro',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->get("/admin/program-studi/{$studyProgram->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Edit Program Studi');

        $updateResponse = $this->actingAs($admin)->put("/admin/program-studi/{$studyProgram->id}", [
            'faculty_id' => $faculty->id,
            'nama' => 'Teknik Elektro dan Komputer',
        ]);

        $updateResponse->assertRedirect('/admin/program-studi');
        $this->assertDatabaseHas('study_programs', [
            'id' => $studyProgram->id,
            'nama' => 'Teknik Elektro dan Komputer',
        ]);
    }

    /**
     * 7. Admin dapat toggle status Program Studi.
     */
    public function test_admin_can_toggle_program_studi_status(): void
    {
        $admin = User::where('role', 'admin')->first();
        $faculty = Faculty::create(['nama' => 'Fakultas Ekonomi', 'status' => 'aktif']);
        $studyProgram = StudyProgram::create([
            'faculty_id' => $faculty->id,
            'nama' => 'Manajemen',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->patch("/admin/program-studi/{$studyProgram->id}/toggle-status");

        $response->assertRedirect('/admin/program-studi');
        $this->assertDatabaseHas('study_programs', [
            'id' => $studyProgram->id,
            'status' => 'nonaktif',
        ]);

        // Toggle back to active
        $response2 = $this->actingAs($admin)->patch("/admin/program-studi/{$studyProgram->id}/toggle-status");
        $response2->assertRedirect('/admin/program-studi');
        $this->assertDatabaseHas('study_programs', [
            'id' => $studyProgram->id,
            'status' => 'aktif',
        ]);
    }

    /**
     * 8. User biasa tidak dapat mengakses halaman Program Studi.
     */
    public function test_user_cannot_access_program_studi(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)->get('/admin/program-studi');

        $response->assertRedirect('/my-haki');
    }

    /**
     * 9. Guest diarahkan ke login.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/program-studi');

        $response->assertRedirect('/login');
    }
}
