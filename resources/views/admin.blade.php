@extends('layouts.app')

@section('admin')
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
    <span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarSupportedContent">
    <!-- Right Side Of Navbar -->
    <ul class="navbar-nav ml-auto">
        <!-- Authentication Links -->
        @guest
            <li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
            </li>
            @if (Route::has('register'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li>
            @endif
        @else
            <li class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    {{ Auth::user()->name }} <span class="caret"></span>
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf

                    </form>
                </div>
            </li>
        @endguest
    </ul>
</div>
@endsection

@section('content')
@if (session('new user'))
    <div class="alert alert-success">
        <p>Een nieuwe gebruiker met naam {{ session('new user') }} werd toegevoegd.</p>
    </div>
@endif
<section>
    <h1>Frisdrank</h1>
    <p class="text-info">Maximum capaciteit: 20 blikjes per soort</p>
    @if (session('drink'))
        @include('errors')
    @endif
    <div class="row">
        <p class="col-4">Frisdrank</p>
        <p class="col-2">Aantal</p>
    </div>
    @foreach ($drinks as $drink)
        <div class="row">
            <p class="col-4">{{ $drink->name }}</p>
            <p class="col-2 @if ($drink->stock < 5) {{ 'text-danger' }} @endif">{{ $drink->stock }}</p>
            <form method="POST" action="/admin"  class="form-inline">
                @csrf

                <input type="hidden" name="id" value="{{ $drink->id }}">
                <input type="number" name="stock" class="form-control" required placeholder="{{ $drink->name }} blikjes">
                <button type="submit" name="drink" class="btn btn-primary ml-1">Toevoegen</button>
            </form>
        </div>
    @endforeach
</section>
<section>
    <h1>Geldlade</h1>
    @if (session('coin'))
        @include('errors')
    @endif
    <div class="row">
        <p class="col-4">Muntstukken</p>
        <p class="col-2">Aantal</p>
    </div>
    @foreach ($coins as $coin)
        <div class="row">
            <p class="col-4">€ {{ $coin->coin }}</p>
            <p class="col-2 @if ($coin->stock < 5) {{ 'text-danger' }} @endif">{{ $coin->stock }}</p>
            <form method="POST" action="/admin" class="form-inline">
                @csrf

                <input type="hidden" name="id" value="{{ $coin->id }}">
                <input type="number" name="stock" class="form-control" required placeholder="Aantal (€ {{ $coin->coin }})">
                <button type="submit" name="coin" class="btn btn-primary ml-1">Uit geldlade halen</button>
            </form>
        </div>
    @endforeach
</section>
<section>
    <h1>Gebruikers</h1>
    <p>Enkel ingelogde gebruikers kunnen nieuwe gebruikers toevoegen.</p>
    <p><a href="/admin/register" class="btn btn-primary">Registreer een nieuwe gebruiker</a></p>
</section>
@endsection
