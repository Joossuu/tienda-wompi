<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $category = Category::create([
            'name' => 'Accesorios',
            'slug' => 'accesorios'
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Mochila urbana',
            'slug' => 'mochila-urbana',
            'description' => 'Mochila para uso diario',
            'price' => 34.90,
            'stock' => 15,
            'is_active' => true,
        ]);
    }
}
