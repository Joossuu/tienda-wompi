<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Services\WompiService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function result(
        Request $request,
        Order $order,
        WompiService $wompi
    ): View {
        if (
            $request->filled('hash')
            && $wompi->validLinkRedirect($request->query())
        ) {
            $sameAmount = abs(
                (float) $request->query('monto') - (float) $order->total
            ) < 0.001;

            if ($sameAmount) {
                $this->markAsPaid($order, [
                    'IdTransaccion' => $request->query('idTransaccion'),
                    'EnlacePago' => [
                        'Id' => $request->query('idEnlace'),
                    ],
                ]);
            }
        }

        return view('payment.result', [
            'order' => $order->fresh(['items', 'payments']),
        ]);
    }

    public function webhook(Request $request)
    {
        $raw = $request->getContent();
        $received = (string) $request->header('wompi_hash');
        $secret = (string) config('services.wompi.client_secret');

        abort_unless(
            $secret !== ''
            && $received !== ''
            && hash_equals(
                hash_hmac('sha256', $raw, $secret),
                strtolower($received)
            ),
            401
        );

        $event = json_decode($raw, true, flags: JSON_THROW_ON_ERROR);

        $reference = data_get(
            $event,
            'EnlacePago.IdentificadorEnlaceComercio'
        );

        $order = Order::where('reference', $reference)->firstOrFail();

        $approved = data_get($event, 'ResultadoTransaccion')
            === 'ExitosaAprobada';

        $sameAmount = abs(
            (float) data_get($event, 'Monto') - (float) $order->total
        ) < 0.001;

        DB::transaction(function () use (
            $order,
            $event,
            $approved,
            $sameAmount
        ) {
            $locked = Order::whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            $transactionId = data_get($event, 'IdTransaccion');

            $payment = $locked->payments()->firstOrNew([
                'provider' => 'wompi',
                'external_id' => $transactionId,
            ]);

            $payment->fill([
                'status' => $approved && $sameAmount
                    ? 'approved'
                    : 'rejected',
                'amount' => data_get($event, 'Monto'),
                'payload' => $event,
            ])->save();

            if (
                $approved
                && $sameAmount
                && $locked->status !== 'paid'
            ) {
                $locked->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            }
        });

        return response()->json(['received' => true]);
    }

    public function demo(Order $order)
    {
        abort_unless(
            config('services.wompi.demo_mode'),
            404
        );

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
