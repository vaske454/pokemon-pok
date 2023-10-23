<?php

namespace App\Console\Commands;

use App\Models\Pokemon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportPokemonData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-pokemon-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = Http::get('https://pokeapi.co/api/v2/pokemon?limit=151');
        $data = $response->json();

        foreach ($data['results'] as $result) {
            $pokemonResponse = Http::get($result['url']);

            if ($pokemonResponse->ok()) {
                $pokemonData = $pokemonResponse->json(); // Use $pokemonResponse here, not $response

                // Extract relevant data
                $id = $pokemonData['id'];
                $name = $pokemonData['name'];
                $height = $pokemonData['height'];
                $weight = $pokemonData['weight'];
                $image = $pokemonData['sprites']['front_default'];
                $types = json_encode($pokemonData['types']);

                // Check if the Pokémon with this ID already exists
                $existingPokemon = Pokemon::where('id', $id)->first();

                if (!$existingPokemon) {
                    // If it doesn't exist, create a new Pokémon entry
                    Pokemon::create([
                        'id' => $id,
                        'name' => $name,
                        'height' => $height,
                        'weight' => $weight,
                        'image' => $image,
                        'types' => $types,
                    ]);
                } else {
                    $this->info("Pokémon with ID $id already exists. Skipping.");
                }
            }
        }

        $this->info('Data imported successfully.');
    }
}
