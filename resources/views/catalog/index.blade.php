
{{-- resources/views/catalog/index.blade.php --}}
@extends('layouts.app')

@section('content')
<h1>Productos</h1>
<div class="product-grid">
@foreach ($products as $product)
    <article>
        <h2>{{ $product->name }}</h2>
        <p>${{ number_format((float) $product->price, 2) }}</p>
        <a href="{{ route('catalogo.show', ['product' => $product->id]) }}">Ver producto</a>
    </article>
@endforeach
</div>
{{ $products->links() }}
@endsection
