<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function createProduct(int $stock = 10): Product
    {
        $category = Category::create([
            'name' => 'Accesorios',
            'slug' => 'accesorios',
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Audifonos',
            'slug' => 'audifonos',
            'description' => 'Producto de prueba',
            'price' => 25.00,
            'stock' => $stock,
            'is_active' => true,
        ]);
    }

    private function checkoutData(): array
    {
        return [
            'customer_name' => 'Cliente de prueba',
            'customer_email' => 'cliente@example.com',
            'customer_phone' => '70000000',
            'shipping_address' => 'San Salvador',
        ];
    }

    public function test_carrito_vacio_no_crea_pedido(): void
    {
        $response = $this->from('/checkout')
            ->post(route('checkout.store'), $this->checkoutData());

        $response->assertStatus(422);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_stock_insuficiente_no_crea_pedido_ni_descuenta_inventario(): void
    {
        $product = $this->createProduct(2);

        $response = $this->withSession([
            'cart' => [$product->id => ['quantity' => 3]],
        ])->post(route('checkout.store'), $this->checkoutData());

        $response->assertStatus(422);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 2,
        ]);
    }

    public function test_checkout_calcula_precio_desde_base_de_datos_y_descuenta_stock(): void
    {
        config(['services.wompi.demo_mode' => true]);

        $product = $this->createProduct(10);

        $response = $this->withSession([
            'cart' => [$product->id => ['quantity' => 2]],
        ])->post(route('checkout.store'), [
            ...$this->checkoutData(),
            'price' => 0.01,
            'total' => 0.01,
        ]);

        $order = Order::first();

        $this->assertNotNull($order);
        $this->assertEquals(50.00, (float) $order->total);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'total' => 50.00,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8,
        ]);

        $response->assertRedirect(route('payment.result', $order));
    }
}
