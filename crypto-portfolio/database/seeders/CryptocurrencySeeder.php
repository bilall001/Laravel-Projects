<?php

namespace Database\Seeders;

use App\Models\Cryptocurrency;
use App\Services\CoinGeckoService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CryptocurrencySeeder extends Seeder
{

    public function run(): void
    {
       
        $service = new CoinGeckoService();
        $coins = $service->getCoinsList();

        foreach ($coins as $coin) {
            Cryptocurrency::updateOrCreate(
                ['api_id' => $coin['id']], // unique
                [
                    'name' => $coin['name'],
                    'symbol' => strtoupper($coin['symbol']),
                    'api_id' => $coin['id'],
                    'image_url' => $coin['image'],
                ]
            );
        }
    }
    }
