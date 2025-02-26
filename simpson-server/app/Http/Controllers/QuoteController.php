<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\JsonResponse;

class QuoteController extends Controller
{
    /**
     * @return JsonResponse with the last five quotes sorted by their creation date.
     */
    public function index(): JsonResponse
    {
        //TODO: Move to a service layer?
        //TODO: Test
        $quotes = Quote::query()->orderBy('created_at', 'desc')->limit(5)->get();
        return response()->json($quotes);
    }
}
