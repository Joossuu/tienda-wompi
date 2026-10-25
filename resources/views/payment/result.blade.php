
@extends('layouts.app')

@section('content')
    <h1>Resultado del pago</h1>

    <p>Pedido: {{ $order->reference }}</p>
    <p>Estado: {{ $order->status }}</p>
    <p>Total: ${{ number_format((float) $order->total, 2) }}</p>

    @if ($order->status === 'paid')
        <p>¡Tu pago ha sido aprobado!</p>
    @else
        <p>Tu pedido está pendiente de confirmación del pago.</p>
    @endif

    <h2>Productos del pedido</h2>

    @foreach ($order->items as $item)
        <article>
            <p>{{ $item->name }}</p>
            <p>Cantidad: {{ $item->quantity }}</p>
            <p>Subtotal: ${{ number_format((float) $item->total, 2) }}</p>
        </article>
    @endforeach

    <a href="{{ route('catalogo.index') }}">Volver a la tienda</a>

    @if (config('services.wompi.demo_mode') && $order->status === 'pending')
        <form action="{{ route('payment.demo', $order) }}" method="POST">
            @csrf
            <button type="submit">Simular pago aprobado (demostración)</button>
        </form>
    @endif
@endsection
