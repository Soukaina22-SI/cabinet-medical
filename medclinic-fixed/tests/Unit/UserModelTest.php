<?php
// tests/Unit/UserModelTest.php
namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_helpers(): void
    {
        $admin = new User(['role' => 'admin']);
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isDoctor());

        $doctor = new User(['role' => 'medecin']);
        $this->assertTrue($doctor->isDoctor());
        $this->assertFalse($doctor->isAdmin());

        $secretary = new User(['role' => 'secretaire']);
        $this->assertTrue($secretary->isSecretary());

        $patient = new User(['role' => 'patient']);
        $this->assertTrue($patient->isPatient());
    }

    public function test_doctors_scope(): void
    {
        User::factory()->count(3)->create(['role' => 'medecin']);
        User::factory()->count(2)->create(['role' => 'patient']);
        User::factory()->create(['role' => 'admin']);

        $this->assertEquals(3, User::doctors()->count());
    }

    public function test_active_scope(): void
    {
        User::factory()->count(3)->create(['is_active' => true]);
        User::factory()->count(2)->create(['is_active' => false]);

        $this->assertEquals(3, User::active()->count());
    }

    public function test_avatar_url_returns_gravatar_when_no_avatar(): void
    {
        $user = new User(['name' => 'Test User', 'avatar' => null]);

        $this->assertStringContainsString('ui-avatars.com', $user->avatar_url);
        $this->assertStringContainsString('Test+User', $user->avatar_url);
    }

    public function test_avatar_url_returns_storage_path_when_avatar_set(): void
    {
        $user = new User(['name' => 'Test', 'avatar' => 'avatars/test.jpg']);

        $this->assertStringContainsString('avatars/test.jpg', $user->avatar_url);
    }
}
