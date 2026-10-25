
@extends('layouts.app')

@section('title', 'NOVA | Catálogo')

@section('content')
<style>
    .catalog-hero {
        background: #e8ede4;
        border-radius: 14px;
        padding: 42px;
        margin-bottom: 38px;
    }

    .catalog-hero .eyebrow {
        color: #245c45;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    .catalog-hero h1 {
        max-width: 650px;
        margin: 12px 0;
        font-size: clamp(32px, 4vw, 48px);
    }

    .catalog-hero p {
        font-size: 16px;
        max-width: 560px;
        margin-bottom: 0;
        line-height: 1.8;
    }

    .catalog-heading {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 22px;
    }

    .catalog-heading h2 {
        margin: 0;
        font-size: 28px;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
        gap: 24px;
    }

    .product-card {
        overflow: hidden;
        border: 1px solid #e7e8e1;
        border-radius: 12px;
        background: white;
        transition: transform .2s, box-shadow .2s;
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px #17211d12;
    }

    .product-visual {
        height: 240px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #eef0e9;
        color: #245c45;
        font-size: 72px;
    }

    .product-visual img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-info {
        padding: 20px;
    }

    .product-info h3 {
        margin: 0 0 8px;
        font-size: 19px;
    }

    .product-price {
        margin: 0 0 18px;
        color: #245c45;
        font-size: 22px;
        font-weight: 800;
    }

    .product-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 9px;
    }

    .product-actions a,
    .product-actions button {
        width: 100%;
        min-height: 43px;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 10px;
        font-size: 13px;
    }

    .product-actions .btn-outline {
        color: #245c45;
        background: white;
        border: 1px solid #245c45;
        border-radius: 5px;
    }

    .product-actions .btn-outline:hover {
        background: #eef0e9;
    }

    .catalog-empty {
        background: white;
        border: 1px solid #e7e8e1;
        border-radius: 12px;
        padding: 35px;
    }

    @media (max-width: 600px) {
        .catalog-hero { padding: 25px; }
        .product-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .product-visual { height: 160px; font-size: 48px; }
        .product-info { padding: 13px; }
        .product-info h3 { font-size: 16px; }
        .product-price { font-size: 19px; }
        .product-actions { grid-template-columns: 1fr; }
    }
</style>

<section class="catalog-hero">
    <span class="eyebrow">NOVA · Tienda en línea</span>
    <h1>Encuentra eso que va contigo.</h1>
    <p>
        Productos para tu día a día, con una experiencia de compra
        sencilla, práctica y segura.
    </p>
</section>

<div class="catalog-heading">
    <div>
        <h2>Nuestros productos</h2>
        <p>Descubre lo que tenemos para ti.</p>
    </div>
</div>

@if ($products->count())
    <div class="product-grid">
        @foreach ($products as $product)
            <article class="product-card">
                <a href="{{ route('catalogo.show', $product) }}"
                   class="product-visual"
                   aria-label="Ver {{ $product->name }}">
                    @if ($product->image)
                        <img
                            src="{{ asset('storage/'.$product->image) }}"
                            alt="{{ $product->name }}"
                        >
                    @else
                        <span aria-hidden="true">🎒</span>
                    @endif
                </a>

                <div class="product-info">
                    <h3>{{ $product->name }}</h3>

                    <p class="product-price">
                        ${{ number_format((float) $product->price, 2) }}
                    </p>

                    <div class="product-actions">
                        <a class="btn-outline"
                           href="{{ route('catalogo.show', $product) }}">
                            Ver producto
                        </a>

                        <form method="POST"
                              action="{{ route('cart.store', $product) }}">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit">Agregar +</button>
                        </form>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div style="margin-top:28px">
        {{ $products->links() }}
    </div>
@else
    <div class="catalog-empty">
        <h3>Aún no hay productos disponibles</h3>
        <p>Cuando agregues productos al catálogo, aparecerán aquí.</p>
    </div>
@endif
@endsection
