<?php

namespace App\Http\Controllers;

use App\Exceptions\UnableToFetchQuotesException;
use App\Services\QuoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * @OA\SecurityScheme(
 *     securityScheme="BearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     name="Authorization"
 * )
 */
readonly class QuoteController
{

    private QuoteService $quoteService;

    public function __construct(QuoteService $quoteService)
    {
        $this->quoteService = $quoteService;
    }

    /**
     * @OA\Get(
     *      path="/api/quotes",
     *      summary="Fetch the last five quotes sorted by their creation date",
     *      tags={"Quotes"},
     *      security={{"BearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="The quotes were successfuly returned",
     *          @OA\JsonContent(ref="#/components/schemas/Quote")
     *       ),
     *     @OA\Response(
     *           response=401,
     *           description="No bearer token was provided"
     *        )
     * )
     *
     * @OA\Schema(
     *      schema="Quote",
     *      type="object",
     *      @OA\Property(property="id", type="integer", example=1),
     *      @OA\Property(property="text", type="string", example="Example quote"),
     *      @OA\Property(property="character", type="string", example="Homer"),
     *      @OA\Property(property="characterDirection", type="string", example="Left"),
     *      @OA\Property(property="imageUrl", type="string", example="https://url/img.png"),
     *      @OA\Property(property="createdAt", type="date", example="2025-03-01T22:03:55.000000Z"),
     *  )
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
