<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Cryptocurrency;
use App\Models\Price;

class CryptocurrencyController extends Controller
{
    /**
     * Fetch latest cryptocurrency prices from CoinGecko
     * and store them in the database
     */
    public function fetchPrices()
    {
        // Example: Fetch Bitcoin and Ethereum
        $cryptoList =Cryptocurrency::pluck('api_id',)->toArray();

        // Make API request to CoinGecko
        $response = Http::get('https://api.coingecko.com/api/v3/simple/price', [
            'ids' => implode(',', $cryptoList), // e.g., bitcoin,ethereum,tether
            'vs_currencies' => 'usd'
        ]);

        if ($response->successful()) {
            $data = $response->json();

            foreach ($data as $coinId => $info) {
                // Check if crypto already exists
                $crypto = Cryptocurrency::where('coingecko_id', $coinId)->first();

            if ($crypto) {
                // Insert new price record
                Price::create([
                    'cryptocurrency_id' => $crypto->id,
                    'price_usd' => $info['usd'],
                ]);
            }
            }

            return response()->json(['message' => 'Prices fetched and stored successfully!']);
        }

        return response()->json(['error' => 'Failed to fetch data'], 500);
    }

    /**
     * Show all cryptos with latest price
     */
   public function index()
{
    $cryptos = Cryptocurrency::with(['prices' => function($q) {
        $q->latest()->take(1);
    }])->get();
    // dd($cryptos);
    // Bitcoin historical data for chart
    $btc = Cryptocurrency::where('symbol', 'btc')->first();
    $btcPrices = [];
    $btcLabels = [];

    if ($btc) {
        $history = $btc->prices()->orderBy('fetched_at', 'asc')->take(20)->get();
        foreach ($history as $record) {
            $btcPrices[] = $record->price_usd;
            $btcLabels[] = $record->fetched_at->format('H:i');
        }
    }

    return view('crypto', compact('cryptos', 'btcPrices', 'btcLabels'));
}

}