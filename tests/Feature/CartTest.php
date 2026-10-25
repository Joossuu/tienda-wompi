<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_agrega_actualiza_y_elimina_productos_del_carrito(): void
    {
        $category = Category::create([
            'name' => 'Accesorios',
            'slug' => 'accesorios',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Audifonos de prueba',
            'slug' => 'audifonos-de-prueba',
            'description' => 'Producto para pruebas',
            'price' => 25.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->post(route('cart.store', $product), [
            'quantity' => 2,
        ])->assertRedirect(route('cart.index'));

        $this->assertEquals(
            2,
            session('cart')[$product->id]['quantity']
        );

        $this->patch(route('cart.update', $product), [
            'quantity' => 3,
        ])->assertRedirect(route('cart.index'));

        $this->assertEquals(
            3,
            session('cart')[$product->id]['quantity']
        );

        $this->delete(route('cart.destroy', $product))
            ->assertRedirect(route('cart.index'));

        $this->assertArrayNotHasKey($product->id, session('cart', []));
    }
}
