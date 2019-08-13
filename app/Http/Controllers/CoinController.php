<?php

namespace App\Http\Controllers;

use App\Coin;
use Illuminate\Http\Request;

class CoinController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only('take', 'validateTake');
    }

    /**
     * Processes the adding of coins (validation, store in session)
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function total(Request $request)
    {
        session()->flash('coin', true);

        $attributes = $this->validateAdd();     

        if (session('total')) {
            $total = session('total');
            $coins = session('coins');
        } else {
            $total = 0;
            $coins = [];
            foreach (Coin::all() as $coinDB) {
                $coins[$coinDB->coin] = 0;
            }
        }

        $coin = Coin::findOrFail($attributes['id'])->coin;
        
        $total += $coin;
        $total = number_format($total, 2);
        session(['total' => $total]);

        $coins[$coin] += 1;
        session(['coins' => $coins]);
    }

    /**
     * Validates the drinks that are added
     *
     * @return \Illuminate\Http\Request  validated $request
     */
    public function validateAdd()
    {
        $rules = [
            'id' => ['required', 'exists:coins,id']
            // 'id' => ['required', Rule::in(['0.10', '0.20', '0,50', '1'])]
        ];

        $messages = [
            'id.*' => 'Er gaat iets mis, probeer later opnieuw.'
        ];

        return $this->validate(request(), $rules, $messages);
    }

    /**
     * Calculates the amount of each coin that has to be returned
     *
     * @param total amount of change    $total
     * @return array with amount of each coins   $changeCoins
     */
    public function changeCoins($total)
    {
        $coins = [];
        $userCoins = session('coins');
        dump($userCoins);
        $coinsDB = Coin::all()->sortByDesc('coin');
        $smallestCoin = Coin::all()->min('coin');
        foreach ($coinsDB as $coinDB) {
            $coin = $coinDB->coin; 
            $stock = $coinDB->stock;
            $userCoin =  $userCoins[$coin];
            $totalStock = $stock + $userCoin;  
            $coins[$coin] = 0;
            if ($total > 0) {
                $amount = floor( ($total + PHP_FLOAT_EPSILON) / $coin );
                if ($totalStock >= $amount){
                    $coins[$coin] = $amount;
                    $total -= $amount * $coin;
                } else {
                    $coins[$coin] = $totalStock;
                    $total -= $totalStock * $coin;
                }
            }
        }
        if (($total + PHP_FLOAT_EPSILON) < $smallestCoin) {
            return $coins;
        } else {
            session()->flash('error', 'Aanvraag kon niet verwerkt worden. Onvoldoende wisselgeld beschikbaar. U kan uw munten terugnemen via de knop.');
            return false;
        }
    }

    /**
     * Updates the coins in the database, after the exchange of coins (user input, change returnd)
     *
     * @param  array with amount of each coins that are returned   $changeCoins
     */
    public function exchangeCoins($changeCoins)
    {
        $userCoins = session('coins');
        $coinsDB = Coin::all();

        foreach ($coinsDB as $coin) {
            $newStock = $coin->stock + $userCoins[$coin->coin] - $changeCoins[$coin->coin];
            $coin->update(['stock' => $newStock]);
        }
    }

    /**
     * Processes the emptying of coins (validation, store)
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function take(Request $request)
    {
        session()->flash('coin', true);

        $attributes = $this->validateTake();

        $taken = $attributes['stock'];
        $coin = Coin::findOrFail($attributes['id']);
        $stock = $coin->stock;

        if (($stock - $taken) < 0) {
            session()->flash('error', 'Onvoldoende muntstukken beschikbaar.');
            return;
        }
        
        $attributes['stock'] = $stock - $taken;

        $coin->update($attributes);
    }

    /**
     * Validates the coin that are emptied
     *
     * @return \Illuminate\Http\Request  validated $request
     */
    public function validateTake()
    {
        $rules = [
            'id' => ['required', 'numeric', 'exists:coins,id'],
            'stock' => ['required', 'numeric', 'min:1']
        ];

        $messages = [
            'stock.required' => 'Aantal muntstukken is een verplicht veld',
            'stock.*' => 'Aantal muntstukken moet groter dan 0 zijn',
            'id.*' => 'Er gaat iets mis, probeer later opnieuw.'
        ];

        return $this->validate(request(), $rules, $messages);
    }
}
