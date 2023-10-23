<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Http;
use App\Models\Pokemon;
use Illuminate\Support\Facades\Schema;

class PokemonController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getPokemonList(Request $request): JsonResponse
    {

        if (Schema::hasTable('pokemons')) {
            $paged = $_GET['paged'] ?? 1;
            $perPage = 20;
            $currentPage = $request->input('page', $paged);
            $offset = ($currentPage - 1) * $perPage;
            $pokemons = Pokemon::skip($offset)->take($perPage)->get();
            $total = Pokemon::count();
            $response = [
                'pokemons' => $pokemons,
                'current_page' => $currentPage,
                'total' => $total,
            ];
        } else {
            $response = [
                'pokemons' => [],
                'current_page' => 1,
                'total' => 0,
            ];
        }

        return response()->json($response);
    }
}
