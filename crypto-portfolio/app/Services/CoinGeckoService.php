<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
class CoinGeckoService
{
    /**
     * Create a new class instance.
     */
  private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://api.coingecko.com/api/v3/';
    }
   
    public function getCoinsList()
    {
        $response = Http::get("{$this->baseUrl}/coins/markets", [
            'vs_currency' => 'usd',
            'order' => 'market_cap_desc',
            'per_page' => 50, // fetch top 50
            'page' => 1,
            'sparkline' => false,
        ]);
        if ($response->failed()) {
            throw new \Exception('Failed to fetch data from CoinGecko API');
        }
        if($response->successful()){
            return $response->json();
        }
    }
     public function getCoinPrice($apiId)
    {
        $response = Http::get("{$this->baseUrl}/simple/price", [
            'ids' => $apiId,
            'vs_currencies' => 'usd',
            'include_market_cap' => 'true',
            'include_24hr_change' => 'true',
        ]);

        return $response->json();
    }
}
