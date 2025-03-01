<?php

namespace App\Repositories;

use App\Enums\CharacterDirection;
use App\Models\Quote;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class QuoteRepository
{
    public function createQuote(string $text, string $character, string $imageUrl, CharacterDirection $characterDirection): void
    {
        Quote::create([
            'text' => $text,
            'character' => $character,
            'image_url' => $imageUrl,
            'character_direction' => $characterDirection
        ]);
    }

    public function findAllOrderByCreatedAtDesc(): Collection
    {
        return Quote::query()->orderBy(Model::CREATED_AT, 'desc')->get();
    }

    public function count(): int
    {
        return Quote::count();
    }

    public function deleteOldest(): void
    {
        Quote::oldest()->first()?->delete();
    }
}
