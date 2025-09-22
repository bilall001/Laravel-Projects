<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cryptocurrency;
use App\Models\Price;
use App\Services\CoinGeckoService;

class UpdateCryptoPrices extends Command
{
    protected $signature = 'crypto:update-prices';
    protected $description = 'Fetch latest crypto prices from CoinGecko';

    public function handle(CoinGeckoService $service)
    {
        $this->info('Fetching latest crypto prices...');

        $cryptos = Cryptocurrency::all();

        foreach ($cryptos as $crypto) {
            $data = $service->getCoinPrice($crypto->api_id);

            if (isset($data[$crypto->api_id])) {
                $priceData = $data[$crypto->api_id];

                Price::create([
                    'cryptocurrency_id' => $crypto->id,
                    'price_usd' => $priceData['usd'],
                    'market_cap' => $priceData['usd_market_cap'] ?? null,
                    'change_24h' => $priceData['usd_24h_change'] ?? null,
                    'fetched_at' => now(),
                ]);

                $this->info("Updated price for {$crypto->name}: \${$priceData['usd']}");
            }
        }

        $this->info('Crypto prices updated successfully!');
    }
}
