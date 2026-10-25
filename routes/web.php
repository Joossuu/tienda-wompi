<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CheckoutController;

Route::get('/', [CatalogoController::class, 'index'])
    ->name('catalogo.index');

Route::get('/productos/{product}', [CatalogoController::class, 'show'])
    ->name('catalogo.show');

// Carrito
Route::get('/carrito', [CartController::class, 'index'])
    ->name('cart.index');

Route::post('/carrito/{product}', [CartController::class, 'store'])
    ->name('cart.store');

Route::patch('/carrito/{product}', [CartController::class, 'update'])
    ->name('cart.update');

Route::delete('/carrito/{product}', [CartController::class, 'destroy'])
    ->name('cart.destroy');

Route::delete('/carrito', [CartController::class, 'clear'])
    ->name('cart.clear');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])
    ->name('checkout.index');

Route::post('/checkout', [CheckoutController::class, 'store'])
    ->name('checkout.store');

// Resultado del pago
Route::get('/pago/resultado/{order:reference}', [PaymentController::class, 'result'])
    ->name('payment.result');

Route::get('/pago/estado/{order:reference}', [PaymentController::class, 'status'])
    ->name('payment.status');

// Pago simulado: se conserva para las demostraciones
Route::post('/pago/demo/{order:reference}', [PaymentController::class, 'demo'])
    ->name('payment.demo');

// Inicio del pago real con Wompi
Route::post('/pago/wompi/{order:reference}', [PaymentController::class, 'startWompi'])
    ->name('payment.wompi');

// Notificaciones de Wompi
Route::post('/wompi/webhook', [PaymentController::class, 'webhook'])
    ->name('wompi.webhook');