
@extends('layouts.app')

@section('content')
<style>
    .cart-page { max-width: 950px; margin: 0 auto; }
    .cart-page h1 { font-size: 34px; margin: 0 0 8px; }
    .cart-subtitle { color: #66716a; margin-bottom: 30px; }
    .cart-item {
        display: flex; justify-content: space-between; align-items: center;
        gap: 24px; padding: 24px; margin: 16px 0;
        background: #fff; border: 1px solid #e5e5dc; border-radius: 12px;
    }
    .cart-item h2 { font-size: 19px; margin: 0 0 10px; }
    .cart-price { color: #245d47; font-weight: 700; }
    .cart-form { display: flex; flex-wrap: wrap; align-items: end; gap: 10px; margin-top: 16px; }
    .cart-form label { display: block; width: 100%; font-size: 13px; }
    .cart-form input { width: 100px; }
    .cart-remove { background: #fff; color: #9b3434; border-color: #e3caca; }
    .cart-summary {
        max-width: 430px; margin: 28px 0 0 auto; padding: 25px;
        background: #e8ede5; border-radius: 12px;
    }
    .cart-total { display: flex; justify-content: space-between; gap: 20px; font-size: 23px; font-weight: 800; }
    .cart-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 22px; }
    .cart-empty { padding: 50px 20px; text-align: center; background: #fff; border-radius: 12px; }
    .cart-message { padding: 14px; margin-bottom: 20px; background: #e8ede5; border-radius: 8px; }
    @media (max-width: 600px) {
        .cart-page h1 { font-size: 28px; }
        .cart-item { align-items: flex-start; flex-direction: column; }
        .cart-summary { max-width: none; }
        .cart-total { font-size: 20px; }
    }
</style>

<div class="cart-page">
    <h1>Mi carrito</h1>
    <p class="cart-subtitle">Revisa tus productos antes de finalizar tu compra.</p>

    @if (session('success'))
        <div class="cart-message">{{ session('success') }}</div>
    @endif

    @if ($items->isEmpty())
        <div class="cart-empty">
            <h2>Tu carrito está vacío</h2>
            <p>Encuentra algo que vaya contigo.</p>
            <a class="button" href="{{ route('catalogo.index') }}">Explorar productos</a>
        </div>
    @else
        @foreach ($items as $item)
            <article class="cart-item">
                <div>
                    <h2>{{ $item['product']->name }}</h2>
                    <p>Precio unitario: ${{ number_format((float) $item['product']->price, 2) }}</p>
                    <p class="cart-price">
                        Subtotal: ${{ number_format((float) $item['line_total'], 2) }}
                    </p>
                </div>

                <div>
                    <form class="cart-form" action="{{ route('cart.update', $item['product']) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="quantity-{{ $item['product']->id }}">Cantidad</label>
                            <input
                                id="quantity-{{ $item['product']->id }}"
                                type="number"
                                name="quantity"
                                min="1"
                                max="{{ $item['product']->stock }}"
                                value="{{ $item['quantity'] }}"
                                required
                            >
                        </div>
                        <button type="submit">Actualizar</button>
                    </form>

                    <form action="{{ route('cart.destroy', $item['product']) }}" method="POST" style="margin-top: 12px">
                        @csrf
                        @method('DELETE')
                        <button class="cart-remove" type="submit">Eliminar producto</button>
                    </form>
                </div>
            </article>
        @endforeach

        <div class="cart-summary">
            <div class="cart-total">
                <span>Total</span>
                <span>${{ number_format((float) $total, 2) }}</span>
            </div>

            <div class="cart-actions">
                <a class="button" href="{{ route('checkout.index') }}">Finalizar compra →</a>

                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="cart-remove" type="submit">Vaciar carrito</button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
