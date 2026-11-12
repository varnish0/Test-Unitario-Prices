<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class PriceController extends Controller
{
    /**
     * Tipo de cambio fijo: 1 EUR = 1.08 USD.
     */
    public const EUR_TO_USD_RATE = 1.08;

    /**
     * Monedas soportadas.
     *
     * @var list<string>
     */
    public const SUPPORTED_CURRENCIES = ['EUR', 'USD'];

    /**
     * Muestra el formulario de la calculadora de precios.
     */
    public function index(): View
    {
        return view('prices.index');
    }

    /**
     * Procesa el formulario: convierte la moneda y aplica el descuento.
     */
    public function calculate(Request $request): View
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'from' => ['required', 'in:EUR,USD'],
            'to' => ['required', 'in:EUR,USD'],
            'discount' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $convertedAmount = $this->convertCurrency((float) $validated['amount'], $validated['from'], $validated['to']);
        $finalPrice = $this->applyDiscount($convertedAmount, (float) $validated['discount']);

        return view('prices.index', [
            'input' => $validated,
            'convertedAmount' => $convertedAmount,
            'finalPrice' => $finalPrice,
        ]);
    }

    /**
     * Convierte una cantidad entre euros y dólares.
     *
     * @throws InvalidArgumentException
     */
    public function convertCurrency(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('La cantidad no puede ser negativa.');
        }

        $fromCurrency = strtoupper($fromCurrency);
        $toCurrency = strtoupper($toCurrency);

        foreach ([$fromCurrency, $toCurrency] as $currency) {
            if (! in_array($currency, self::SUPPORTED_CURRENCIES, true)) {
                throw new InvalidArgumentException("Moneda no soportada: {$currency}.");
            }
        }

        if ($fromCurrency === $toCurrency) {
            return round($amount, 2);
        }

        $converted = $fromCurrency === 'EUR'
            ? $amount * self::EUR_TO_USD_RATE
            : $amount / self::EUR_TO_USD_RATE;

        return round($converted, 2);
    }

    /**
     * Aplica un descuento porcentual (0-100) a un precio.
     *
     * @throws InvalidArgumentException
     */
    public function applyDiscount(float $price, float $discountPercentage): float
    {
        if ($price < 0) {
            throw new InvalidArgumentException('El precio no puede ser negativo.');
        }

        if ($discountPercentage < 0 || $discountPercentage > 100) {
            throw new InvalidArgumentException('El descuento debe estar entre 0 y 100.');
        }

        return round($price - ($price * $discountPercentage / 100), 2);
    }
}
