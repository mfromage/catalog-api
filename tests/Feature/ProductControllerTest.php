<?php
namespace Tests\Feature;

use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
    }

    public function test_store_product_with_main_image()
    {
        $category = Category::factory()->create();
        $image1 = Image::factory()->create();
        $image2 = Image::factory()->create();

        $payload = [
            'name' => 'Sample Product',
            'description' => 'This is a sample product description.',
            'category_id' => $category->id,
            'main_image_id' => $image1->id,
            'attributes' => [
                'Height' => '170cm',
                'Weight' => '100kg'
            ],
            'images' => [
                $image1->id,
                $image2->id
            ]
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('main_image.id', $image1->id)
                 ->assertJsonPath('images.0.id', $image1->id)
                 ->assertJsonPath('images.1.id', $image2->id);
    }

    public function test_store_product_without_main_image()
    {
        $category = Category::factory()->create();
        $image1 = Image::factory()->create();
        $image2 = Image::factory()->create();

        $payload = [
            'name' => 'Sample Product',
            'description' => 'This is a sample product description.',
            'category_id' => $category->id,
            'attributes' => [
                'Height' => '170cm',
                'Weight' => '100kg'
            ],
            'sort_order' => 1,
            'images' => [
                $image1->id,
                $image2->id
            ]
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('main_image.id', $image1->id)
                 ->assertJsonPath('images.0.id', $image1->id)
                 ->assertJsonPath('images.1.id', $image2->id);
    }

    public function test_update_product_with_main_image()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $image1 = Image::factory()->create();
        $image2 = Image::factory()->create();

        $payload = [
            'name' => 'Updated Product',
            'description' => 'This is an updated product description.',
            'main_image_id' => $image1->id,
            'attributes' => [
                'Height' => '180cm',
                'Weight' => '90kg'
            ],
            'sort_order' => 2,
            'images' => [
                $image1->id,
                $image2->id
            ]
        ];

        $response = $this->putJson("/api/products/{$product->id}", $payload);

        $response->assertStatus(200)
                 ->assertJsonPath('main_image.id', $image1->id)
                 ->assertJsonPath('images.0.id', $image1->id)
                 ->assertJsonPath('images.1.id', $image2->id);
    }

    public function test_update_product_without_main_image()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $image1 = Image::factory()->create();
        $image2 = Image::factory()->create();

        $payload = [
            'name' => 'Updated Product',
            'description' => 'This is an updated product description.',
            'attributes' => [
                'Height' => '180cm',
                'Weight' => '90kg'
            ],
            'sort_order' => 2,
            'images' => [
                $image1->id,
                $image2->id
            ]
        ];

        $response = $this->putJson("/api/products/{$product->id}", $payload);

        $response->assertStatus(200)
                 ->assertJsonPath('main_image.id', $image1->id)
                 ->assertJsonPath('images.0.id', $image1->id)
                 ->assertJsonPath('images.1.id', $image2->id);
    }

    public function test_delete_product()
    {
        $category = Category::factory()->create();
        $product1 = Product::factory()->create(['category_id' => $category->id, 'sort_order' => 1]);
        $product2 = Product::factory()->create(['category_id' => $category->id, 'sort_order' => 2]);

        $response = $this->deleteJson("/api/products/{$product1->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('products', ['id' => $product1->id]);
        $this->assertDatabaseHas('products', ['id' => $product2->id, 'sort_order' => 1]);
    }

    public function test_update_product_sort_order()
    {
        $category = Category::factory()->create();
        $product1 = Product::factory()->create(['category_id' => $category->id, 'sort_order' => 1]);
        $product2 = Product::factory()->create(['category_id' => $category->id, 'sort_order' => 2]);

        $payload = [
            'sort_order' => 1
        ];

        $response = $this->putJson("/api/products/{$product2->id}", $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('products', ['id' => $product1->id, 'sort_order' => 2]);
        $this->assertDatabaseHas('products', ['id' => $product2->id, 'sort_order' => 1]);
    }
}