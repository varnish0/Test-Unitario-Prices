<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Calculadora de precios</title>
    <link rel="stylesheet" href="{{ asset('css/prices.css') }}">
</head>
<body>
    <main class="card">
        <h1>Calculadora de precios</h1>
        <p class="subtitle">Conversión EUR ⇄ USD (1 EUR = {{ \App\Http\Controllers\PriceController::EUR_TO_USD_RATE }} USD) y descuento.</p>

        @if ($errors->any())
            <ul class="errors">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('prices.calculate') }}">
            @csrf
            <label for="amount">Precio</label>
            <input type="number" step="0.01" min="0" id="amount" name="amount" value="{{ old('amount', $input['amount'] ?? '') }}" required>

            <div class="row">
                <div>
                    <label for="from">De</label>
                    <select id="from" name="from">
                        @foreach (['EUR', 'USD'] as $currency)
                            <option value="{{ $currency }}" @selected(old('from', $input['from'] ?? 'EUR') === $currency)>{{ $currency }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="to">A</label>
                    <select id="to" name="to">
                        @foreach (['USD', 'EUR'] as $currency)
                            <option value="{{ $currency }}" @selected(old('to', $input['to'] ?? 'USD') === $currency)>{{ $currency }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <label for="discount">Descuento (%)</label>
            <input type="number" step="0.01" min="0" max="100" id="discount" name="discount" value="{{ old('discount', $input['discount'] ?? 0) }}" required>

            <button type="submit">Calcular</button>
        </form>

        @isset($finalPrice)
            <section class="result">
                <p>Precio convertido: <strong>{{ number_format($convertedAmount, 2) }} {{ $input['to'] }}</strong></p>
                <p>Precio final con {{ $input['discount'] }}% de descuento: <strong>{{ number_format($finalPrice, 2) }} {{ $input['to'] }}</strong></p>
            </section>
        @endisset
    </main>
</body>
</html>
