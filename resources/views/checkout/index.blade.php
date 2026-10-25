
@extends('layouts.app')

@section('content')
<style>
    .checkout-page {
        max-width: 900px;
        margin: 0 auto;
    }

    .checkout-heading {
        margin-bottom: 8px;
        font-size: 32px;
    }

    .checkout-description {
        margin-bottom: 28px;
        color: #68736c;
    }

    .checkout-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        align-items: start;
    }

    .checkout-card {
        padding: 24px;
        background: #fff;
        border: 1px solid #e5e5dc;
        border-radius: 12px;
    }

    .checkout-card h2 {
        margin-top: 0;
        margin-bottom: 20px;
        font-size: 21px;
    }

    .checkout-item {
        padding: 14px 0;
        border-bottom: 1px solid #ecece5;
    }

    .checkout-item:first-of-type {
        padding-top: 0;
    }

    .checkout-item h3 {
        margin: 0 0 8px;
        font-size: 16px;
    }

    .checkout-item p {
        margin: 5px 0;
        color: #68736c;
        font-size: 14px;
    }

    .checkout-total {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-top: 20px;
        padding: 18px;
        background: #e8ede5;
        border-radius: 8px;
        font-size: 20px;
        font-weight: 700;
    }

    .checkout-form {
        display: grid;
        gap: 17px;
    }

    .checkout-field label {
        display: block;
        margin-bottom: 7px;
        font-size: 14px;
        font-weight: 600;
    }

    .checkout-field input,
    .checkout-field textarea {
        display: block;
        width: 100%;
        max-width: 100%;
        padding: 12px 14px;
        border: 1px solid #d6d9d0;
        border-radius: 6px;
        background: #fff;
        color: #17231d;
        font: inherit;
    }

    .checkout-field input:focus,
    .checkout-field textarea:focus {
        outline: 2px solid #b7cbbd;
        border-color: #245d47;
    }

    .checkout-field textarea {
        min-height: 110px;
        resize: vertical;
    }

    .checkout-error {
        margin: 6px 0 0;
        color: #b42318;
        font-size: 13px;
    }

    .checkout-submit {
        width: 100%;
        margin-top: 5px;
        padding: 14px;
        border: 0;
        border-radius: 6px;
        background: #245d47;
        color: #fff;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
    }

    .checkout-submit:hover {
        background: #194733;
    }

    .checkout-back {
        display: inline-block;
        margin-top: 22px;
        color: #245d47;
        text-decoration: none;
        font-weight: 600;
    }

    .checkout-back:hover {
        text-decoration: underline;
    }

    @media (max-width: 700px) {
        .checkout-grid {
            grid-template-columns: 1fr;
        }

        .checkout-heading {
            font-size: 27px;
        }

        .checkout-card {
            padding: 19px;
        }
    }
</style>

<div class="checkout-page">
    <h1 class="checkout-heading">Finalizar compra</h1>
    <p class="checkout-description">
        Revisa tu pedido y completa tus datos para coordinar la entrega.
    </p>

    @if ($errors->any())
        <div role="alert" style="margin-bottom: 20px; padding: 14px; background: #fff0ee; border-radius: 8px; color: #b42318;">
            Revisa los campos señalados antes de continuar.
        </div>
    @endif

    <div class="checkout-grid">
        <section class="checkout-card">
            <h2>Resumen del pedido</h2>

            @foreach ($items as $item)
                <article class="checkout-item">
                    <h3>{{ $item['product']->name }}</h3>

                    <p>Cantidad: {{ $item['quantity'] }}</p>

                    <p>
                        Subtotal:
                        ${{ number_format($item['line_total'], 2) }}
                    </p>
                </article>
            @endforeach

            <div class="checkout-total">
                <span>Total</span>
                <span>${{ number_format($total, 2) }}</span>
            </div>
        </section>

        <section class="checkout-card">
            <h2>Datos de entrega</h2>

            <form
                class="checkout-form"
                action="{{ route('checkout.store') }}"
                method="POST"
            >
                @csrf

                <div class="checkout-field">
                    <label for="customer_name">Nombre completo *</label>
                    <input
                        type="text"
                        id="customer_name"
                        name="customer_name"
                        value="{{ old('customer_name') }}"
                        maxlength="120"
                        autocomplete="name"
                        required
                    >
                    @error('customer_name')
                        <p class="checkout-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="checkout-field">
                    <label for="customer_email">Correo electrónico *</label>
                    <input
                        type="email"
                        id="customer_email"
                        name="customer_email"
                        value="{{ old('customer_email') }}"
                        maxlength="160"
                        autocomplete="email"
                        required
                    >
                    @error('customer_email')
                        <p class="checkout-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="checkout-field">
                    <label for="customer_phone">Teléfono</label>
                    <input
                        type="tel"
                        id="customer_phone"
                        name="customer_phone"
                        value="{{ old('customer_phone') }}"
                        maxlength="30"
                        autocomplete="tel"
                    >
                    @error('customer_phone')
                        <p class="checkout-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="checkout-field">
                    <label for="shipping_address">Dirección de entrega *</label>
                    <textarea
                        id="shipping_address"
                        name="shipping_address"
                        maxlength="500"
                        autocomplete="street-address"
                        required
                    >{{ old('shipping_address') }}</textarea>
                    @error('shipping_address')
                        <p class="checkout-error">{{ $message }}</p>
                    @enderror
                </div>

                <button class="checkout-submit" type="submit">
                    Confirmar pedido →
                </button>
            </form>
        </section>
    </div>

    <a class="checkout-back" href="{{ route('cart.index') }}">
        ← Volver al carrito
    </a>
</div>
@endsection
