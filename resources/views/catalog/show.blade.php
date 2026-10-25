
@extends('layouts.app')

@section('content')
    <h1>{{ $product->name }}</h1>

    <p>{{ $product->description }}</p>

    <p>
        Precio: ${{ number_format((float) $product->price, 2) }}
    </p>

    <p>Stock disponible: {{ $product->stock }}</p>

    <form action="{{ route('cart.store', $product) }}" method="POST">
        @csrf

        <label for="quantity">Cantidad:</label>

        <input
            type="number"
            id="quantity"
            name="quantity"
            value="1"
            min="1"
            max="20"
            required
        >

        <button type="submit">Agregar al carrito</button>
    </form>

    <p>
        <a href="{{ route('catalogo.index') }}">Volver a los productos</a>
    </p>
@endsection
