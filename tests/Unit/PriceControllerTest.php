<?php

use App\Http\Controllers\PriceController;

beforeEach(function () {
    $this->controller = new PriceController;
});

describe('convertCurrency', function () {
    test('convierte euros a dólares', function () {
        expect($this->controller->convertCurrency(100, 'EUR', 'USD'))->toBe(108.0);
    });

    test('convierte dólares a euros', function () {
        expect($this->controller->convertCurrency(108, 'USD', 'EUR'))->toBe(100.0);
    });

    test('redondea el resultado a dos decimales', function () {
        expect($this->controller->convertCurrency(10, 'USD', 'EUR'))->toBe(9.26);
    });

    test('devuelve la misma cantidad si la moneda es igual', function () {
        expect($this->controller->convertCurrency(50, 'EUR', 'EUR'))->toBe(50.0)
            ->and($this->controller->convertCurrency(50, 'USD', 'USD'))->toBe(50.0);
    });

    test('acepta códigos de moneda en minúsculas', function () {
        expect($this->controller->convertCurrency(100, 'eur', 'usd'))->toBe(108.0);
    });

    test('convertir cero devuelve cero', function () {
        expect($this->controller->convertCurrency(0, 'EUR', 'USD'))->toBe(0.0);
    });

    test('lanza excepción con una cantidad negativa', function () {
        $this->controller->convertCurrency(-1, 'EUR', 'USD');
    })->throws(InvalidArgumentException::class, 'La cantidad no puede ser negativa.');

    test('lanza excepción con una moneda no soportada', function () {
        $this->controller->convertCurrency(100, 'EUR', 'GBP');
    })->throws(InvalidArgumentException::class, 'Moneda no soportada: GBP.');
});

describe('applyDiscount', function () {
    test('aplica un descuento del 20%', function () {
        expect($this->controller->applyDiscount(100, 20))->toBe(80.0);
    });

    test('aplica un descuento con decimales', function () {
        expect($this->controller->applyDiscount(59.99, 15))->toBe(50.99);
    });

    test('un descuento del 0% no cambia el precio', function () {
        expect($this->controller->applyDiscount(100, 0))->toBe(100.0);
    });

    test('un descuento del 100% deja el precio a cero', function () {
        expect($this->controller->applyDiscount(100, 100))->toBe(0.0);
    });

    test('lanza excepción con un descuento negativo', function () {
        $this->controller->applyDiscount(100, -5);
    })->throws(InvalidArgumentException::class, 'El descuento debe estar entre 0 y 100.');

    test('lanza excepción con un descuento mayor que 100', function () {
        $this->controller->applyDiscount(100, 150);
    })->throws(InvalidArgumentException::class, 'El descuento debe estar entre 0 y 100.');

    test('lanza excepción con un precio negativo', function () {
        $this->controller->applyDiscount(-10, 10);
    })->throws(InvalidArgumentException::class, 'El precio no puede ser negativo.');
});

test('convierte y aplica descuento combinados', function () {
    $dollars = $this->controller->convertCurrency(200, 'EUR', 'USD');

    expect($dollars)->toBe(216.0)
        ->and($this->controller->applyDiscount($dollars, 25))->toBe(162.0);
});
