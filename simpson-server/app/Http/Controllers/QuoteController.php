<?php

namespace App\Http\Controllers;

use App\Services\QuoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class QuoteController extends Controller
{

    private $quoteService;

    public function __construct(QuoteService $quoteService)
    {
        $this->quoteService = $quoteService;
    }

    /**
     * @return JsonResponse with the last five quotes sorted by their creation date.
     */
    public function index(): JsonResponse
    {
        $quotes = $this->quoteService->fetchLatestQuotes();

        $formattedQuotes = collect($quotes)->map(function ($quote) {
            return collect($quote)->keyBy(fn ($value, $key) => Str::camel($key))->all();
        });

        return response()->json($formattedQuotes);
    }
}
