<?php

namespace App\Http\Controllers;

use App\Exceptions\UnableToFetchQuotesException;
use App\Services\QuoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

readonly class QuoteController
{

    private QuoteService $quoteService;

    public function __construct(QuoteService $quoteService)
    {
        $this->quoteService = $quoteService;
    }

    /**
     * @return JsonResponse with the last five quotes sorted by their creation date.
     */
    public function fetchQuotes(): JsonResponse
    {
        try {
            $quotes = $this->quoteService->fetchLatestQuotes();
        } catch (UnableToFetchQuotesException) {
            return response()->json(['error' => 'Unable to fetch quotes'], 503);
        }

        $formattedQuotes = collect($quotes)->map(function ($quote) {
            return collect($quote)->keyBy(fn($value, $key) => Str::camel($key))->all();
        });

        return response()->json($formattedQuotes);
    }
}
