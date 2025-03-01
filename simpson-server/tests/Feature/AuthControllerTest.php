<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginWithValidCredentials()
    {
        //given
        User::create([
            'username' => 'test',
            'password' => Hash::make('secret123'),
        ]);

        //when
        $response = $this->postJson('/api/login', [
            'username' => 'test',
            'password' => 'secret123',
        ]);

        //then
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['token']);
    }

    public function testLoginFailsWithInvalidCredentials()
    {
        //when
        $response = $this->postJson('/api/login', [
            'username' => 'test',
            'password' => 'wrong_password',
        ]);

        //then
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(['error' => 'Invalid credentials']);
    }

    public function testLoginFailsWhenPasswordMissing()
    {
        //when
        $response = $this->postJson('/api/login', [
            'username' => 'test',
        ]);

        //then
        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson(['error' => 'Invalid input']);
    }

    public function testLoginFailsWhenUsernameMissing()
    {
        //when
        $response = $this->postJson('/api/login', [
            'password' => 'secret123',
        ]);

        //then
        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson(['error' => 'Invalid input']);
    }
}
