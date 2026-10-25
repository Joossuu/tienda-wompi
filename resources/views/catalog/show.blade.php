
@extends('layouts.app')

@section('content')
    <section class="product-detail">
        <a href="{{ route('catalogo.index') }}" class="back-link">
            ← Volver a los productos
        </a>

        <div class="product-card">
            <div class="product-visual">
                @if ($product->image)
                    <img
                        src="{{ asset('storage/' . $product->image) }}"
                        alt="{{ $product->name }}"
                        class="product-image"
                    >
                @else
                    <div class="product-placeholder" aria-label="Sin imagen">
                        <span>🛍️</span>
                        <p>NOVA · Selección para ti</p>
                    </div>
                @endif

                <span class="visual-caption">NOVA · TIENDA EN LÍNEA</span>
            </div>

            <div class="product-info">
                <p class="eyebrow">DESCUBRE TU PRÓXIMO FAVORITO</p>

                <h1>{{ $product->name }}</h1>

                <p class="product-description">
                    {{ $product->description }}
                </p>

                <div class="price-block">
                    <span class="price-label">Precio</span>
                    <p class="product-price">
                        ${{ number_format((float) $product->price, 2) }}
                    </p>
                </div>

                <div class="stock-status">
                    <span class="stock-dot"
                          style="background: {{ $product->stock > 0 ? '#247052' : '#b42318' }}">
                    </span>

                    @if ($product->stock > 0)
                        <span>{{ $product->stock }} unidades disponibles</span>
                    @else
                        <span>Producto agotado</span>
                    @endif
                </div>

                @if ($product->stock > 0)
                    <form
                        action="{{ route('cart.store', $product) }}"
                        method="POST"
                        class="product-form"
                    >
                        @csrf

                        <label for="quantity">Cantidad</label>

                        <div class="purchase-controls">
                            <input
                                type="number"
                                id="quantity"
                                name="quantity"
                                value="{{ old('quantity', 1) }}"
                                min="1"
                                max="{{ min(20, $product->stock) }}"
                                required
                            >

                            <button type="submit" class="add-button">
                                Agregar al carrito <span>→</span>
                            </button>
                        </div>

                        @error('quantity')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </form>
                @else
                    <button class="add-button" disabled>
                        Producto agotado
                    </button>
                @endif

                <div class="product-benefits">
                    <div>
                        <span class="benefit-icon">✓</span>
                        <span>Compra sencilla y segura</span>
                    </div>
                    <div>
                        <span class="benefit-icon">♡</span>
                        <span>Elegido para acompañarte</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .product-detail {
            max-width: 1080px;
            margin: 0 auto;
            padding: 28px 22px 64px;
            color: #17271f;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 22px;
            color: #245f49;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .product-card {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            overflow: hidden;
            background: #fff;
            border: 1px solid #e5e6de;
            border-radius: 20px;
            box-shadow: 0 14px 38px rgba(23, 39, 31, 0.07);
        }

        .product-visual {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            min-height: 440px;
            padding: 35px;
            background: #edf0e8;
        }

        .product-image {
            display: block;
            width: 100%;
            height: 340px;
            object-fit: contain;
        }

        .product-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            min-height: 340px;
            color: #245f49;
        }

        .product-placeholder span {
            font-size: 100px;
        }

        .product-placeholder p {
            font-size: 12px;
            letter-spacing: 2px;
        }

        .visual-caption {
            margin-top: 16px;
            color: #52695c;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .product-info {
            align-self: center;
            padding: 44px;
        }

        .eyebrow {
            margin: 0 0 12px;
            color: #247052;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.8px;
        }

        .product-info h1 {
            margin: 0 0 16px;
            color: #17271f;
            font-size: clamp(28px, 3vw, 38px);
            line-height: 1.15;
            overflow-wrap: anywhere;
        }

        .product-description {
            margin: 0;
            color: #68736c;
            font-size: 15px;
            line-height: 1.8;
            white-space: pre-line;
        }

        .price-block {
            margin-top: 26px;
            padding: 20px 0;
            border-top: 1px solid #e8e9e3;
            border-bottom: 1px solid #e8e9e3;
        }

        .price-label {
            color: #69736d;
            font-size: 12px;
        }

        .product-price {
            margin: 5px 0 0;
            color: #216448;
            font-size: 34px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .stock-status {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 20px 0;
            color: #526158;
            font-size: 13px;
        }

        .stock-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .product-form label {
            display: block;
            margin-bottom: 9px;
            font-size: 13px;
            font-weight: 700;
        }

        .purchase-controls {
            display: flex;
            gap: 10px;
        }

        .purchase-controls input {
            width: 76px;
            min-width: 0;
            padding: 12px;
            border: 1px solid #d9ddd5;
            border-radius: 8px;
            background: #fff;
            color: #17271f;
            font: inherit;
        }

        .purchase-controls input:focus {
            outline: 2px solid #8bb5a0;
            outline-offset: 2px;
        }

        .add-button {
            flex: 1;
            padding: 13px 17px;
            border: 0;
            border-radius: 8px;
            background: #245f49;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
        }

        .add-button:hover:not(:disabled) {
            background: #194b38;
            transform: translateY(-1px);
        }

        .add-button:disabled {
            background: #929b94;
            cursor: not-allowed;
        }

        .form-error {
            color: #b42318;
            font-size: 13px;
        }

        .product-benefits {
            display: grid;
            gap: 12px;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #e8e9e3;
        }

        .product-benefits > div {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #647168;
            font-size: 12px;
        }

        .benefit-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #edf3ed;
            color: #245f49;
            font-weight: 800;
        }

        @media (max-width: 720px) {
            .product-detail {
                padding: 20px 14px 40px;
            }

            .product-card {
                grid-template-columns: 1fr;
                border-radius: 14px;
            }

            .product-visual {
                min-height: 280px;
                padding: 22px;
            }

            .product-image,
            .product-placeholder {
                height: 240px;
                min-height: 240px;
            }

            .product-placeholder span {
                font-size: 75px;
            }

            .product-info {
                padding: 26px 22px;
            }

            .product-info h1 {
                font-size: 29px;
            }

            .product-price {
                font-size: 30px;
            }

            .purchase-controls {
                flex-wrap: wrap;
            }

            .purchase-controls input {
                width: 75px;
            }

            .add-button {
                min-height: 46px;
            }
        }
    </style>

@endsection
