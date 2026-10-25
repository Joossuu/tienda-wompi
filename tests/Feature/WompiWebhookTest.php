<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Services\WompiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WompiWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function createPendingOrder(): Order
    {
        $category = Category::create([
            'name' => 'Accesorios',
            'slug' => 'accesorios',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Audifonos',
            'slug' => 'audifonos',
            'description' => 'Producto de prueba',
            'price' => 25.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        return Order::create([
            'reference' => 'pedido-prueba-001',
            'customer_name' => 'Cliente de prueba',
            'customer_email' => 'cliente@example.com',
            'customer_phone' => '70000000',
            'shipping_address' => 'San Salvador',
            'subtotal' => 50.00,
            'total' => 50.00,
            'status' => 'pending',
        ]);
    }

    private function configureWebhookSignature(): void
    {
        config([
            'services.wompi.client_secret' => 'secreto-prueba',
        ]);
    }

    private function signedWebhook(array $event)
    {
        $raw = json_encode($event, JSON_UNESCAPED_SLASHES);

        $signature = hash_hmac(
            'sha256',
            $raw,
            'secreto-prueba'
        );

        return $this->call(
            'POST',
            route('wompi.webhook'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_WOMPI_HASH' => $signature,
            ],
            $raw
        );
    }

    private function approvedEvent(Order $order, string $transactionId): array
    {
        return [
            'EnlacePago' => [
                'IdentificadorEnlaceComercio' => $order->reference,
            ],
            'IdTransaccion' => $transactionId,
            'Monto' => 50.00,
            'ResultadoTransaccion' => 'ExitosaAprobada',
        ];
    }

    public function test_rechaza_webhook_con_firma_invalida(): void
    {
        $this->configureWebhookSignature();
        $order = $this->createPendingOrder();

        $event = $this->approvedEvent($order, 'transaccion-001');
        $raw = json_encode($event, JSON_UNESCAPED_SLASHES);

        $response = $this->call(
            'POST',
            route('wompi.webhook'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_WOMPI_HASH' => 'firma-incorrecta',
            ],
            $raw
        );

        $response->assertStatus(401);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_monto_incorrecto_no_aprueba_el_pedido(): void
    {
        $this->configureWebhookSignature();
        $order = $this->createPendingOrder();

        $event = $this->approvedEvent($order, 'transaccion-002');
        $event['Monto'] = 10.00;

        $response = $this->signedWebhook($event);

        $response->assertStatus(422);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_webhook_aprobado_paga_el_pedido_y_es_idempotente(): void
    {
        $this->configureWebhookSignature();
        $order = $this->createPendingOrder();

        $event = $this->approvedEvent($order, 'transaccion-003');

        $this->signedWebhook($event)->assertOk();
        $this->signedWebhook($event)->assertOk();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'paid',
        ]);

        $this->assertNotNull($order->fresh()->paid_at);

        $this->assertDatabaseCount('payments', 1);
    }

    public function test_wompi_no_disponible_mantiene_pedido_pendiente(): void
    {
        $order = $this->createPendingOrder();

        $wompi = \Mockery::mock(\App\Services\WompiService::class);
        $wompi->shouldReceive('configured')->once()->andReturn(true);
        $wompi->shouldReceive('createPaymentLink')
            ->once()
            ->with(\Mockery::type(\App\Models\Order::class))
            ->andThrow(new \RuntimeException('Wompi no disponible'));

        $this->app->instance(\App\Services\WompiService::class, $wompi);

        \Illuminate\Support\Facades\Log::shouldReceive('error')->zeroOrMoreTimes();
        \Illuminate\Support\Facades\Log::shouldReceive('critical')->zeroOrMoreTimes();

        $response = $this->post(route('payment.wompi', $order));

        $response->assertRedirect(route('payment.result', $order));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseCount('payments', 0);
    }

}
