<?php

namespace GlennRaya\Xendivel;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class Xendivel
{
    /**
     * Some comment.
     */
    public static function getRandom(): JsonResponse
    {
        $game = Http::get('https://jsonfakery.com/games/random');
        return response()->json([
            'data' => $game->json(),
        ]);
    }
}
