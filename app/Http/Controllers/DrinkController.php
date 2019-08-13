<?php

namespace App\Http\Controllers;

use App\Drink;
use App\Http\Controllers\CoinController;
use Illuminate\Http\Request;

class DrinkController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only('add', 'validateAdd');
    }

    /**
     * Processes the ordering of drinks (validation, storage drinks, calculation return, storage coins)
     * Session ('drink', 'error', 'change', total', 'order')
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function buy(Request $request)
    {
        session()->flash('drink', true);

        $attributes = $this->validateOrder();
        $drink = Drink::find($attributes['id']);

        if (!$drink->stock) {
            $error = $drink->name . " is niet in voorraad.";
            session()->flash('error', $error);
            return;
        }

        $price = $drink->price;
        $total = session('total');
        $change = $total - $price;

        if ($change < 0) {
            session()->flash('error', 'Onvoldoende geld ingeworpen.');
            return;
        }

        $coinCtr = new CoinController();
        $changeCoins = $coinCtr->changeCoins($change);
        
        if ($changeCoins) {
            $coinCtr->exchangeCoins($changeCoins);
            $this->takeOne($drink);

            session()->flash('changeCoins', $changeCoins);
            session()->flash('total', $total);
            session()->flash('order', $drink->name);
        }
    }

    /**
     * Validates the drinks that are ordered
     *
     * @return \Illuminate\Http\Request  validated $request
     */
    public function validateOrder()
    {
        $rules = [
            'id' => ['required', 'numeric', 'exists:drinks,id']
        ];

        $messages = [
            'id.required' => 'Kies een frisdrank.',
            'id.*' => 'Er gaat iets mis, probeer later opnieuw.'
        ];

        return $this->validate(request(), $rules, $messages);
    }

    /**
     * Processes the reducing (by one) of drinks (store)
     *
     * @param  App\Drink  $drink
     */
    public function takeOne(Drink $drink)
    {
        $newStock = $drink->stock - 1;

        $drink->update(['stock' => $newStock]);
    }

    /**
     * Processes the adding of drinks (validation, store)
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function add(Request $request)
    {
        session()->flash('drink', true);

        $attributes = $this->validateAdd();

        $added = $attributes['stock'];
        $drink = Drink::findOrFail($attributes['id']);
        $stock = $drink->stock;

        if (($stock + $added) > 20) {
            $error = "Maximum capaciteit bereikt (20 blikjes). De overige blikjes werden niet toegevoegd";
            session()->flash('error', $error);
            $attributes['stock'] = 20;
            
        } else {
            $attributes['stock'] = $stock + $added;
        }

        $drink->update($attributes);
    }

    /**
     * Validates the drinks that are added
     *
     * @return \Illuminate\Http\Request  validated $request
     */
    public function validateAdd()
    {
        $rules = [
            'id' => ['required', 'numeric', 'exists:drinks,id'],
            'stock' => ['required', 'numeric', 'min:1']
        ];

        $messages = [
            'stock.required' => 'Aantal blikjes is een verplicht veld',
            'stock.*' => 'Aantal blikjes moet groter dan 0 zijn',
            'id.*' => 'Er gaat iets mis, probeer later opnieuw.'
        ];

        return $this->validate(request(), $rules, $messages);
    }
}
