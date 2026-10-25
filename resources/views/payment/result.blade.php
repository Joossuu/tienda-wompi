
@extends('layouts.app')

@section('content')
@php
    $paymentStatus = $order->status === 'paid'
        ? 'approved'
        : (in_array($order->status, ['failed', 'rejected', 'cancelled'], true)
            ? 'rejected'
            : 'pending');

    $paymentTitle = [
        'approved' => '¡Pago aprobado!',
        'pending' => 'Tu pedido está en proceso',
        'rejected' => 'No se pudo completar el pago',
    ][$paymentStatus];

    $paymentMessage = [
        'approved' => 'Tu compra se ha confirmado correctamente. Gracias por elegir NOVA.',
        'pending' => 'Tu pedido está registrado, pero el pago aún está pendiente de confirmación.',
        'rejected' => 'El pago no se ha completado. Puedes volver a la tienda e intentarlo nuevamente.',
    ][$paymentStatus];

    $paymentIcon = [
        'approved' => '✓',
        'pending' => '…',
        'rejected' => '!',
    ][$paymentStatus];

    $paymentLabel = [
        'approved' => 'Pagado',
        'pending' => 'Pendiente',
        'rejected' => 'No aprobado',
    ][$paymentStatus];
@endphp

<style>
    .payment-page {
        max-width: 1000px;
        margin: 0 auto;
        padding: 55px 24px 80px;
        color: #17231e;
    }

    .payment-card {
        max-width: 760px;
        margin: 0 auto;
        overflow: hidden;
        background: #fff;
        border: 1px solid #e7eae5;
        border-radius: 22px;
        box-shadow: 0 15px 45px rgba(24, 43, 33, .07);
    }

    .payment-header {
        padding: 42px 30px 34px;
        text-align: center;
        background: #f4f7f2;
        border-bottom: 1px solid #e7eae5;
    }

    .payment-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 76px;
        height: 76px;
        margin: 0 auto 20px;
        border-radius: 50%;
        font-size: 36px;
        font-weight: 700;
    }

    .payment-icon.approved {
        color: #176b48;
        background: #dff4e8;
    }

    .payment-icon.pending {
        color: #926516;
        background: #fff0cf;
    }

    .payment-icon.rejected {
        color: #b42318;
        background: #fee4e2;
    }

    .payment-eyebrow {
        margin: 0 0 10px;
        color: #587063;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    .payment-header h1 {
        margin: 0 0 12px;
        font-size: clamp(25px, 4vw, 34px);
        font-weight: 750;
        letter-spacing: -1px;
    }

    .payment-description {
        max-width: 440px;
        margin: 0 auto;
        color: #66736b;
        font-size: 15px;
        line-height: 1.7;
    }

    .payment-body {
        padding: 32px;
    }

    .payment-reference {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 24px;
        border-bottom: 1px solid #edf0eb;
    }

    .payment-label {
        display: block;
        margin-bottom: 6px;
        color: #7a857d;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .7px;
    }

    .payment-reference code {
        color: #34463a;
        font-size: 13px;
        overflow-wrap: anywhere;
    }

    .payment-status {
        display: inline-block;
        padding: 7px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    .payment-status.approved {
        color: #176b48;
        background: #dff4e8;
    }

    .payment-status.pending {
        color: #926516;
        background: #fff0cf;
    }

    .payment-status.rejected {
        color: #b42318;
        background: #fee4e2;
    }

    .payment-total {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 25px 0;
        border-bottom: 1px solid #edf0eb;
    }

    .payment-total span {
        color: #59665e;
        font-size: 15px;
    }

    .payment-total strong {
        color: #1f6148;
        font-size: 29px;
        letter-spacing: -1px;
    }

    .payment-products {
        padding-top: 28px;
    }

    .payment-products h2 {
        margin: 0 0 20px;
        font-size: 19px;
        font-weight: 700;
    }

    .payment-product {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 17px 0;
        border-bottom: 1px solid #edf0eb;
    }

    .payment-product:last-child {
        border-bottom: none;
    }

    .payment-product-name {
        margin: 0 0 6px;
        font-size: 15px;
        font-weight: 650;
    }

    .payment-product-quantity {
        margin: 0;
        color: #78827b;
        font-size: 13px;
    }

    .payment-product-price {
        flex-shrink: 0;
        font-size: 15px;
        font-weight: 700;
    }

    .payment-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 12px;
        margin-top: 30px;
    }

    .payment-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        padding: 12px 20px;
        border: 1px solid transparent;
        border-radius: 10px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: background .2s, transform .2s;
    }

    .payment-button:hover {
        transform: translateY(-2px);
    }

    .payment-button-primary {
        color: #fff;
        background: #205c45;
    }

    .payment-button-primary:hover {
        color: #fff;
        background: #174733;
    }

    .payment-button-secondary {
        color: #34463a;
        background: #fff;
        border-color: #dce3dc;
    }

    .payment-button-secondary:hover {
        background: #f5f7f4;
    }

    .payment-demo-note {
        margin: 20px 0 0;
        color: #818981;
        font-size: 12px;
        line-height: 1.6;
        text-align: center;
    }

    @media (max-width: 600px) {
        .payment-page {
            padding: 28px 14px 45px;
        }

        .payment-header {
            padding: 32px 20px;
        }

        .payment-body {
            padding: 23px 20px;
        }

        .payment-reference {
            align-items: flex-start;
            flex-direction: column;
        }

        .payment-total strong {
            font-size: 25px;
        }

        .payment-actions {
            flex-direction: column;
        }

        .payment-actions form,
        .payment-button {
            width: 100%;
            box-sizing: border-box;
        }

        .payment-actions form .payment-button {
            width: 100%;
        }
    }
</style>

<main class="payment-page">
    <section class="payment-card">
        <header class="payment-header">
            <div class="payment-icon {{ $paymentStatus }}" aria-hidden="true">
                {{ $paymentIcon }}
            </div>

            <p class="payment-eyebrow">NOVA · Tu compra</p>
            <h1>{{ $paymentTitle }}</h1>

            <p class="payment-description">
                {{ $paymentMessage }}
            </p>
        </header>

        <div class="payment-body">
            <div class="payment-reference">
                <div>
                    <span class="payment-label">Número de pedido</span>
                    <code>{{ $order->reference }}</code>
                </div>

                <div>
                    <span class="payment-label">Estado del pedido</span>
                    <span class="payment-status {{ $paymentStatus }}">
                        {{ $paymentLabel }}
                    </span>
                </div>
            </div>

            <div class="payment-total">
                <span>Total del pedido</span>
                <strong>${{ number_format((float) $order->total, 2) }}</strong>
            </div>

            <section class="payment-products">
                <h2>Resumen de tu compra</h2>

                @forelse ($order->items as $item)
                    <article class="payment-product">
                        <div>
                            <p class="payment-product-name">
                                {{ $item->name }}
                            </p>
                            <p class="payment-product-quantity">
                                Cantidad: {{ $item->quantity }}
                            </p>
                        </div>

                        <span class="payment-product-price">
                            ${{ number_format((float) $item->total, 2) }}
                        </span>
                    </article>
                @empty
                    <p>No hay productos disponibles para mostrar.</p>
                @endforelse
            </section>

            <div class="payment-actions">
                @if ($order->status === 'pending')
                    <form action="{{ route('payment.wompi', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="payment-button payment-button-primary">
                            Pagar con Wompi
                        </button>
                    </form>

                    @if (config('services.wompi.demo_mode'))
                        <form action="{{ route('payment.demo', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="payment-button payment-button-primary">
                                Simular pago aprobado
                            </button>
                        </form>
                    @endif
                @endif

                <a href="{{ route('catalogo.index') }}"
                   class="payment-button payment-button-secondary">
                    Volver a la tienda
                </a>
            </div>

            @if ($order->status === 'pending' && config('services.wompi.demo_mode'))
                <p class="payment-demo-note">
                    Modo de demostración: la simulación no realiza un cobro real.
                    Wompi requiere que su integración y sus credenciales estén configuradas.
                </p>
            @endif
        </div>
    </section>
</main>


@if ($order->status === 'pending')
<script>
(() => {
    const statusUrl = @json(route('payment.status', $order, false));
    let reloading = false;

    async function checkPaymentStatus() {
        if (reloading) return;

        try {
            const response = await fetch(statusUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                cache: 'no-store'
            });

            if (!response.ok) {
                console.error('Status HTTP:', response.status);
                return;
            }

            const data = await response.json();
            console.log('Estado del pago:', data.status);

            if (data.status !== 'pending') {
                reloading = true;
                window.location.reload();
            }
        } catch (error) {
            console.error('Error consultando el pago:', error);
        }
    }

    checkPaymentStatus();
    setInterval(checkPaymentStatus, 1500);
})();
</script>
@endif

@endsection
