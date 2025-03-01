<?php

namespace App\Services;

use App\Enums\CharacterDirection;
use App\Exceptions\ExternalApiUrlUndefinedException;
use App\Models\Quote;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QuoteService
{
    private string $apiUrl;

    /**
     * @throws ExternalApiUrlUndefinedException when the SIMPSONS_QUOTE_API_URL is not defined.
     */
    public function __construct()
    {
        $this->apiUrl = Config::get('services.simpsons_api.url');

        if (!$this->apiUrl) {
            throw new ExternalApiUrlUndefinedException('SIMPSONS_QUOTE_API_URL is not defined.');
        }
    }

    /**
     * Fetches a new quote and persists it. If more than 5 quotes are present, the oldest one is deleted.
     * @return Collection with the last five quotes sorted by their creation date.
     */
    public function fetchLatestQuotes(): Collection
    {
        //TODO: Test
        $this->fetchAndStoreQuote();
        return Quote::query()->orderBy(Model::CREATED_AT, 'desc')->limit(5)->get();
    }

    private function fetchAndStoreQuote(): void
    {
        try {
            $response = Http::get($this->apiUrl . '/quotes');

            if ($response->successful()) {
                $simpsonQuotes = $response->json();

                if (isset($simpsonQuotes[0])) {
                    $quoteData = $simpsonQuotes[0];

                    Log::info('Fetched quote from API:', ['data' => $quoteData]);

                    if (Quote::count() >= 5) {
                        Quote::oldest()->first()?->delete();
                    }

                    Quote::create([
                        'text' => $quoteData['quote'],
                        'character' => $quoteData['character'],
                        'image_url' => $quoteData['image'],
                        'character_direction' => CharacterDirection::from($quoteData['characterDirection'])
                    ]);
                } else {
                    Log::warning('No quotes returned in the API response.');
                }
            } else {
                Log::warning('Failed to fetch quotes from the API', ['status_code' => $response->status()]);
            }
        } catch (Exception $e) {
            Log::error('Error fetching quote: ' . $e->getMessage());
        }
    }
}
