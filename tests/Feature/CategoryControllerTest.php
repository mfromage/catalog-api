<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;


class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
    }
    
    public function testIndex()
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function testStore()
    {
        $data = [
            'name' => 'New Category',
            'image_path' => 'image-name-1.jpg',
            'sort_order' => 1
        ];

        $response = $this->postJson('/api/categories', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment($data);

        $this->assertDatabaseHas('categories', $data);
    }

    public function testShow()
    {
        $category = Category::factory()->create();

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $category->id,
                     'name' => $category->name,
                     'sort_order' => $category->sort_order,
                     'image_path' => $category->image_path,
                 ]);
    }

    public function testUpdate()
    {
        $category = Category::factory()->create();

        $data = [
            'name' => 'Updated Category',
            'sort_order' => 5,
        ];

        $response = $this->putJson("/api/categories/{$category->id}", $data);

        $response->assertStatus(200)
                 ->assertJsonFragment($data);

        $this->assertDatabaseHas('categories', $data);
    }

    public function testDestroy()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(204);

        $category->refresh();
        
        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }
}