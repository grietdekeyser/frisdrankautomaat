@extends('layouts.app')

@section('content')
<p>Voer eerst uw munten in en kies vervolgens het gewenste drankje</p>
@if (session('order'))
    <div class="alert alert-success">
        <p>Bedankt voor uw aankoop. Haal je {{ session('order') }} uit de automaat.</p>
        <p><a href="/" class="btn btn-secondary">Bestel nog een drankje</a></p>
    </div>
@endif
<div class="row">
    <section class="col-md-8">
        <h1>Automaat</h1>
        <form method="POST" action="">
            @csrf
            <div class="row">
                <?php $i = 0; ?>
                @foreach ($drinks as $drink)
                    <div class="col-ld-4 col-6 drink">
                        @if ($drink->stock)
                            <input type="radio" name="id" value="{{ $drink->id }}" required @if ($i == 0) {{ "checked" }}@endif> {{ $drink->name }} -  € {{ $drink->price }}
                            <img src="/img/{{ $drink->image }}"> 
                        @else
                            <p>Niet voorradig.
                                <img src="/img/{{ $drink->image }}">
                            </p>
                        @endif    
                    </div>
                    <?php $i++; ?>
                @endforeach  
            </div>
            <br>
            @if (!session('ordered'))   
                <button type="submit" class="btn btn-primary">Ontvang drankje</button>
            @endif

            @if (session('drink'))
                @include('errors')
            @endif
        </form>
    </section>
    <section class="col-md-4">
        <h1>Betalen</h1>
        <div class="row payment">
            @foreach ($coins as $coin)
                <form method="POST" action="/" class="col">
                    @csrf
                    <input type="hidden" name="id" value="{{ $coin->id }}">
                    <input type="hidden" name="coin" value="{{ $coin->coin }}">
                    <button type="submit" class="btn btn-primary">€ {{ $coin->coin }}</button>
                </form>
            @endforeach
        </div>
        @if (session('coin'))
            @include('errors')
        @endif
        <br>
        <h5><strong>Ingeworpen: </strong>€ {{ $total }}</h5>
        @if (session('order'))
            <h5><strong>Terug:</strong></h5>
            <p>
                @foreach (session('changeCoins') as $coin => $amount)
                    {{ $amount }} x € {{ $coin }}
                    <br>
                @endforeach
            </p>
        @endif
        <br>
        <br>
        @if ($total && ! session('order'))
            <p><a href="/clear" class="btn btn-secondary">Neem munten terug</a></p>
        @endif
    </section>
</div>
@endsection
