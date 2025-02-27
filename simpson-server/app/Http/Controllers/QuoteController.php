<?php

namespace App\Http\Controllers;

use App\Services\QuoteService;
use Illuminate\Http\JsonResponse;

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
        //TODO: Test
        $quotes = $this->quoteService->fetchLatestQuotes();
        return response()->json($quotes);
    }
}
