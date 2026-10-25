
@extends('layouts.app')

@section('content')
    <h1>Finalizar compra</h1>

    <h2>Resumen del pedido</h2>

    @foreach ($items as $item)
        <article>
            <h3>{{ $item['product']->name }}</h3>
            <p>Cantidad: {{ $item['quantity'] }}</p>
            <p>Subtotal: ${{ number_format($item['line_total'], 2) }}</p>
        </article>
    @endforeach

    <h2>Total: ${{ number_format($total, 2) }}</h2>

    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf

        <div>
            <label for="customer_name">Nombre completo</label>
            <input
                type="text"
                id="customer_name"
                name="customer_name"
                value="{{ old('customer_name') }}"
                maxlength="120"
                required
            >
            @error('customer_name')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="customer_email">Correo electrónico</label>
            <input
                type="email"
                id="customer_email"
                name="customer_email"
                value="{{ old('customer_email') }}"
                maxlength="160"
                required
            >
            @error('customer_email')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="customer_phone">Teléfono</label>
            <input
                type="text"
                id="customer_phone"
                name="customer_phone"
                value="{{ old('customer_phone') }}"
                maxlength="30"
            >
            @error('customer_phone')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="shipping_address">Dirección de entrega</label>
            <textarea
                id="shipping_address"
                name="shipping_address"
                maxlength="500"
                required
            >{{ old('shipping_address') }}</textarea>
            @error('shipping_address')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">Confirmar pedido</button>
    </form>

    <p><a href="{{ route('cart.index') }}">Volver al carrito</a></p>
@endsection
