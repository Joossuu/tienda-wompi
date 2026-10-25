<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\WompiService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class PaymentController extends Controller
{
    
    public function status(Order $order): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => $order->fresh()->status,
        ]);
    }

    public function result(
        Request $request,
        Order $order,
        WompiService $wompi
    ): View {
        $transactionId = $request->query('idTransaccion');
        $hash = $request->query('hash');

        if (
            $order->status === 'pending'
            && is_string($transactionId)
            && $transactionId !== ''
            && is_string($hash)
            && $wompi->validLinkRedirect($request->query())
        ) {
            try {
                $response = $wompi->getTransaction($transactionId);

                $transaction = data_get($response, 'data', $response);

                $approved = filter_var(
                    data_get($transaction, 'esAprobada'),
                    FILTER_VALIDATE_BOOLEAN
                );

                $real = filter_var(
                    data_get($transaction, 'esReal'),
                    FILTER_VALIDATE_BOOLEAN
                );

                $sameAmount = abs(
                    (float) data_get($transaction, 'monto')
                    - (float) $order->total
                ) < 0.001;

                $sameId = (string) data_get($transaction, 'idTransaccion')
                    === $transactionId;

                if ($approved && $real && $sameAmount && $sameId) {
                    $this->markAsPaid($order, [
                        'IdTransaccion' => $transactionId,
                        'respuesta' => $transaction,
                    ]);
                }
            } catch (Throwable $e) {
                report($e);
            }
        }

        return view('payment.result', [
            'order' => $order->fresh(['items', 'payments']),
        ]);
    }



    /**
     * Inicia el pago real redirigiendo al checkout alojado por Wompi.
     */
    public function startWompi(
        Order $order,
        WompiService $wompi
    ) {
        abort_unless($order->status === 'pending', 422);

        abort_unless($wompi->configured(), 503,
            'Wompi no está configurado. Revisa las credenciales del servidor.'
        );

        try {
            $payment = $wompi->createPaymentLink($order);

            return redirect()->away($payment['urlEnlace']);
        } catch (Throwable $e) {
            report($e);

            return to_route('payment.result', $order)
                ->with('error', 'No se pudo iniciar el pago con Wompi. Intenta nuevamente.');
        }
    }


    public function webhook(Request $request, WompiService $wompi)
    {
        $raw = $request->getContent();
        $signature = $request->header('wompi_hash');

        // 1. Validar la firma del webhook usando el cuerpo original.
        abort_unless(
            $wompi->validWebhook($raw, $signature),
            401,
            'Firma de webhook inválida.'
        );

        // 2. Interpretar el evento.
        try {
            $event = json_decode(
                $raw,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $e) {
            return response()->json([
                'received' => false,
                'error' => 'JSON inválido.',
            ], 400);
        }

        if (! is_array($event)) {
            return response()->json([
                'received' => false,
                'error' => 'Formato de evento inválido.',
            ], 400);
        }

        // 3. Validar los datos indispensables.
        $reference = data_get(
            $event,
            'EnlacePago.IdentificadorEnlaceComercio'
        );

        $transactionId = data_get($event, 'IdTransaccion');
        $amount = data_get($event, 'Monto');
        $result = data_get($event, 'ResultadoTransaccion');

        if (
            ! is_string($reference)
            || trim($reference) === ''
            || ! is_string($transactionId)
            || trim($transactionId) === ''
            || ! is_numeric($amount)
            || (float) $amount <= 0
            || ! is_string($result)
            || $result === ''
        ) {
            return response()->json([
                'received' => false,
                'error' => 'Faltan datos obligatorios.',
            ], 400);
        }

        // 4. Buscar el pedido asociado a la referencia del enlace.
        $order = Order::where('reference', $reference)->first();

        if (! $order) {
            return response()->json([
                'received' => false,
                'error' => 'Pedido no encontrado.',
            ], 404);
        }

        $approved = $result === 'ExitosaAprobada';

        // Una notificación que no sea de aprobación no debe marcar
        // el pedido como pagado ni degradar un pago ya aprobado.
        if (! $approved) {
            return response()->json([
                'received' => true,
                'ignored' => true,
            ]);
        }

        // 5. Verificar que el monto recibido coincida con el pedido.
        $sameAmount = abs(
            (float) $amount - (float) $order->total
        ) < 0.001;

        if (! $sameAmount) {
            return response()->json([
                'received' => false,
                'error' => 'El monto no coincide con el pedido.',
            ], 422);
        }

        // 6. Registrar el pago de forma transaccional e idempotente.
        DB::transaction(function () use (
            $order,
            $event,
            $transactionId,
            $amount
        ) {
            $locked = Order::whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            // No volver a modificar un pedido ya pagado.
            if ($locked->status === 'paid') {
                return;
            }

            $payment = $locked->payments()->firstOrNew([
                'provider' => 'wompi',
                'external_id' => $transactionId,
            ]);

            // Si esta transacción ya estaba aprobada, conservar ese estado.
            if ($payment->exists && $payment->status === 'approved') {
                return;
            }

            $payment->fill([
                'status' => 'approved',
                'amount' => $amount,
                'payload' => $event,
            ]);

            $payment->save();

            $locked->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        });

        return response()->json([
            'received' => true,
        ]);
    }


    public function demo(Order $order)
    {
        abort_unless(config('services.wompi.demo_mode'), 404);

        abort_unless($order->status === 'pending', 422);

        $this->markAsPaid($order, [
            'modo' => 'demostracion',
        ], 'wompi-demo');

        return to_route('payment.result', $order);
    }

    private function markAsPaid(
        Order $order,
        array $payload,
        string $provider = 'wompi'
    ): void {
        DB::transaction(function () use ($order, $payload, $provider) {
            $locked = Order::whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status === 'paid') {
                return;
            }

            $transactionId = $payload['IdTransaccion']
                ?? 'demo-'.$locked->reference;

            $locked->payments()->updateOrCreate(
                [
                    'provider' => $provider,
                    'external_id' => (string) $transactionId,
                ],
                [
                    'status' => 'approved',
                    'amount' => $locked->total,
                    'payload' => $payload,
                ]
            );

            $locked->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        });
    }
}
