<?php

namespace App\Http\Controllers;

use App\Coin;
use App\Drink;
use App\Http\Controllers\CoinController;
use App\Http\Controllers\DrinkController;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only('admin', 'formAdmin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $coins = Coin::all()->sortBy('coin');
        $drinks = Drink::all()->sortBy('name');

        if (session('total')) {
            $total = session('total');    
        } else {
            $total = 0;
        }

        if (session('order')) {
            session()->forget('coins');
        }

        return view('index', compact('coins', 'drinks', 'total'));
    }

    /**
     * Redirects to the admin dashboard.
     *
     * @return redirect
     */
    public function home()
    {
        return redirect('/admin');
    }

    /**
     * Show the application admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function admin()
    {
        $coins = Coin::all()->sortBy('coin');
        $drinks = Drink::all()->sortBy('name');

        return view('admin', compact('coins', 'drinks'));
    }

    /**
     * Processes the forms on the index page (add coins, order drinks).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return redirect
     */
    public function processForm(Request $request)
    {
        if ($request['coin']) {
            $coinCtr = new CoinController();
            $coinCtr->total($request);
        } else {
            $drinkCtr = new DrinkController();
            $drinkCtr->buy($request);
        }

        //herbekijken : flash not working 
        // $coins = Coin::all();
        // $drinks = Drink::all();
        // $total = session('total');
        // $change = session('change');
        // return view('index', compact('coins', 'drinks', 'total', 'change'));

        return redirect('/');
    }

    /**
     * Clears what is stored in the session.
     *
     * @return redirect
     */
    public function clear()
    {
        session()->flush();

        return redirect('/');
    }

    /**
     * Processes the forms on the admin page (add drinks, empty coins).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return redirect
     */
    public function formAdmin(Request $request)
    {
        if (isset($request['drink'])) {
            $drinkCtr = new DrinkController();
            $drinkCtr->add($request);
        } elseif (isset($request['coin'])) {
            $coinCtr = new CoinController();
            $coinCtr->take($request);
        }

        return redirect('/admin');
    }
}
