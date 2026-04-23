<?php
// tests/Feature/AuthTest.php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('MedClinic');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        $response = $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->create([
            'email'     => 'inactive@example.com',
            'password'  => bcrypt('password'),
            'is_active' => false,
        ]);

        $this->post('/login', [
            'email'    => 'inactive@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_doctor_redirects_to_doctor_dashboard(): void
    {
        $doctor = User::factory()->create(['role' => 'medecin']);

        $response = $this->post('/login', [
            'email'    => $doctor->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('doctor.dashboard'));
    }
}
