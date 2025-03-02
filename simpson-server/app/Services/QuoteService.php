<?php

namespace App\Services;

use App\Enums\CharacterDirection;
use App\Exceptions\ExternalApiUrlUndefinedException;
use App\Exceptions\UnableToFetchQuotesException;
use App\Models\Quote;
use App\Repositories\QuoteRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

readonly class QuoteService
{
    private QuoteRepository $quoteRepository;
    private string $apiUrl;

    /**
     * @param QuoteRepository $quoteRepository
     * @throws ExternalApiUrlUndefinedException when the SIMPSONS_QUOTE_API_URL is not defined.
     */
    public function __construct(QuoteRepository $quoteRepository)
    {
        $this->apiUrl = Config::get('services.simpsons_api.url');

        if (!$this->apiUrl) {
            throw new ExternalApiUrlUndefinedException('SIMPSONS_QUOTE_API_URL is not defined.');
        }
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Fetches a new quote and persists it. If more than 5 quotes are present, the oldest one is deleted.
     * @return Collection<Quote> with the last five quotes sorted by their creation date.
     * @throws UnableToFetchQuotesException when the Quotes API is unreachable or returns an unexpected response.
     */
    public function fetchLatestQuotes(): Collection
    {
        $this->fetchAndStoreQuote();
        return $this->quoteRepository->findAllOrderByCreatedAtDesc();
    }

    /**
     * @throws UnableToFetchQuotesException when the Quotes API is unreachable or returns an unexpected response.
     */
    private function fetchAndStoreQuote(): void
    {
        try {
            $response = Http::get($this->apiUrl . '/quotes');

            if ($response->successful() && isset($response->json()[0])) {
                $quoteData = $response->json()[0];

                Log::info('Fetched quote from API:', ['data' => $quoteData]);

                if ($this->quoteRepository->count() >= 5) {
                    $this->quoteRepository->deleteOldest();
                }

                $this->quoteRepository->createQuote(
                    $quoteData['quote'],
                    $quoteData['character'],
                    $quoteData['image'],
                    CharacterDirection::from($quoteData['characterDirection']));
            } else {
                throw new UnableToFetchQuotesException('Failed to fetch quotes from the API');
            }
        } catch (Exception $e) {
            throw new UnableToFetchQuotesException('Error fetching quote: ', 0, $e);
        }
    }
}
