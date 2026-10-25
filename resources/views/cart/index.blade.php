
@extends('layouts.app')

@section('content')
    <h1>Mi carrito</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if ($items->isEmpty())
        <p>Tu carrito está vacío.</p>
        <a href="{{ route('catalogo.index') }}">Volver a la tienda</a>
    @else
        @foreach ($items as $item)
            <article>
                <h2>{{ $item['product']->name }}</h2>

                <p>Precio: ${{ number_format((float) $item['product']->price, 2) }}</p>

                <form action="{{ route('cart.update', $item['product']) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <label>
                        Cantidad:
                        <input
                            type="number"
                            name="quantity"
                            value="{{ $item['quantity'] }}"
                            min="1"
                            max="20"
                            required
                        >
                    </label>

                    <button type="submit">Actualizar</button>
                </form>

                <p>Subtotal: ${{ number_format($item['line_total'], 2) }}</p>

                <form action="{{ route('cart.destroy', $item['product']) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Eliminar</button>
                </form>
            </article>
            <hr>
        @endforeach

        <h2>Total: ${{ number_format($total, 2) }}</h2>

        <form action="{{ route('cart.clear') }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit">Vaciar carrito</button>
        </form>
    @endif
@endsection
