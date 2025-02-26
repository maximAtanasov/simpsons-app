<?php

namespace App\Http\Controllers;

use App\Enums\CharacterDirection;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class QuoteController extends Controller
{
    /**
     * @return JsonResponse with the last five quotes sorted by their creation date.
     */
    public function index(): JsonResponse
    {
        //TODO: Move to a service layer?
        //TODO: Test
        $this->fetchAndStoreQuote();
        $quotes = Quote::query()->orderBy('created_at', 'desc')->limit(5)->get();
        return response()->json($quotes);
    }

    /**
     * Fetches and stores quotes from the thesimpsonsquoteapi. If there are more than 5 quotes
     * persisted, then the oldest one is deleted.
     * @return void
     */
    private function fetchAndStoreQuote(): void
    {
        $response = Http::get('https://thesimpsonsquoteapi.glitch.me/quotes');

        if ($response->successful()) {
            $simpsonQuotes = $response->json();

            if (isset($simpsonQuotes[0])) {
                $quoteData = $simpsonQuotes[0];

                if (Quote::count() >= 5) {
                    Quote::oldest()->first()?->delete();
                }

                Quote::create([
                    'text' => $quoteData['quote'],
                    'character' => $quoteData['character'],
                    'image_url' => $quoteData['image'],
                    'character_direction' => CharacterDirection::from($quoteData['characterDirection'])
                ]);
            }
        } else {
            //TODO: Log error
        }
    }
}
