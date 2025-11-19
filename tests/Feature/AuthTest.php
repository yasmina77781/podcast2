<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test : Un utilisateur peut s'inscrire
     */
    public function test_user_can_register()
    {
        // 1. Envoyer une requête POST à /api/register
        $response = $this->postJson('/api/register', [
            'first_name' => 'Ahmed',
            'last_name' => 'Test',
            'email' => 'ahmed@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        // 2. Vérifier que la réponse a le bon code (200 ou 201)
        $response->assertStatus(200);

        // 3. Vérifier qu'on a reçu un token
        $response->assertJsonStructure([
            'token',
            'user' => [
                'id',
                'first_name',
                'last_name',
                'email'
                
            ]
        ]);

        // 4. Vérifier que le user est bien créé dans la base
        $this->assertDatabaseHas('users', [
            'email' => 'ahmed@test.com',
            'first_name' => 'Ahmed',
            'last_name' => 'Test'
        ]);
    }
}