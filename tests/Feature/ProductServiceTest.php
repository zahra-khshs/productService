<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_products()
    {
         Product::factory()->count(5)->create();

         $response = $this->getJson('/api/products');

         $response->assertStatus(200)
                  ->assertJsonStructure([
                      'current_page',
                      'data' => [['id', 'name', 'description', 'price', 'category']],
                      'last_page',
                      'total',
                  ]);
    }

    public function test_show_returns_single_product()
    {
         $product = Product::factory()->create();

         $response = $this->getJson("/api/products/{$product->id}");

         $response->assertStatus(200)
                  ->assertJson([
                      'id' => $product->id,
                      'name' => $product->name,
                      'description' => $product->description,
                      'price' => $product->price,
                      'category' => $product->category,
                  ]);
    }

    public function test_by_category_returns_products_in_category()
    {
         $category = 'Electronics';
         Product::factory()->count(3)->create(['category' => $category]);
         Product::factory()->count(2)->create(['category' => 'Books']);

         $response = $this->getJson("/api/products/category/{$category}");

         $response->assertStatus(200)
                  ->assertJsonStructure([
                      'current_page',
                      'data' => [['id', 'name', 'description', 'price', 'category']],
                      'last_page',
                      'total',
                  ]);

         $this->assertCount(3, $response->json('data'));
    }
}
