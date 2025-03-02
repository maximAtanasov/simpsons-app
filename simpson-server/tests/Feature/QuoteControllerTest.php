<?php

namespace Tests\Feature;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class QuoteControllerTest extends TestCase
{

    use RefreshDatabase;

    public function testFetchQuotesSuccessfully()
    {
        // given
        $quoteData = [
            ['quote' => 'Test',
                'character' => 'Homer',
                'characterDirection' => 'Left',
                'image' => 'testUrl']
        ];

        Http::fake([
            "https://thesimpsonsquoteapi.glitch.me/quotes" => Http::response($quoteData),
        ]);

        Quote::factory()->count(5)->create();

        User::create([
            'username' => 'test',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'test',
            'password' => 'secret123',
        ]);

        //when
        $response = $this->getJson('/api/quotes', ['Authorization' => 'Bearer ' . $response->json('token')]);

        //then
        $this->assertEquals(Response::HTTP_OK, $response->status());
        $response->assertJsonIsArray();
        $response->assertJsonCount(5);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'text',
                'characterDirection',
                'imageUrl',
                'createdAt',
            ]
        ]);
    }

    public function testReturnsUnauthorizedWhenNoTokenProvided()
    {
        //when
        $response = $this->getJson('/api/quotes');

        //then
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->status());
    }

    public function testReturnsServiceUnavailableWhenResponseEmpty()
    {
        // given
        $quoteData = [[]];

        Http::fake([
            "https://thesimpsonsquoteapi.glitch.me/quotes" => Http::response($quoteData),
        ]);

        Quote::factory()->count(5)->create();

        User::create([
            'username' => 'test',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'test',
            'password' => 'secret123',
        ]);

        //when
        $response = $this->getJson('/api/quotes', ['Authorization' => 'Bearer ' . $response->json('token')]);

        //then
        $this->assertEquals(Response::HTTP_SERVICE_UNAVAILABLE, $response->status());
    }

    public function testReturnsServiceUnavailableWhenQuoteApiUnavailable()
    {
        // given
        Http::fake([
            "https://thesimpsonsquoteapi.glitch.me/quotes" => Http::response([], 500),
        ]);

        Quote::factory()->count(5)->create();

        User::create([
            'username' => 'test',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'test',
            'password' => 'secret123',
        ]);

        //when
        $response = $this->getJson('/api/quotes', ['Authorization' => 'Bearer ' . $response->json('token')]);

        //then
        $this->assertEquals(Response::HTTP_SERVICE_UNAVAILABLE, $response->status());
    }
}
