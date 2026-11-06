<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RegistrationApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_default_leaves_new_users_pending(): void
    {
        DB::table('users')->insert([
            'name' => 'Nuevo usuario',
            'email' => 'nuevo@example.com',
            'password' => 'hashed-password',
            'rol' => 'organizador',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'nuevo@example.com',
            'aprobado' => false,
        ]);
    }

    public function test_public_registration_creates_a_pending_user_who_cannot_log_in(): void
    {
        $this->post('/register', [
            'name' => 'Nuevo usuario',
            'email' => 'nuevo@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'email' => 'nuevo@example.com',
            'aprobado' => false,
        ]);

        $this->post('/login', [
            'email' => 'nuevo@example.com',
            'password' => 'Password123!',
        ])->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
