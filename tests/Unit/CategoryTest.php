<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_category_with_uuid()
    {
        $category = Category::create([
            'name' => 'Electronics',
            'image_path' => 'images/electronics.jpg',
            'sort_order' => 1,
        ]);

        $this->assertNotNull($category->id);
        $this->assertTrue(Str::isUuid($category->id));
    }

    #[Test]
    public function it_soft_deletes_a_category()
    {
        $category = Category::create([
            'name' => 'Books',
            'image_path' => 'images/books.jpg',
            'sort_order' => 2,
        ]);

        $category->delete();

        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    #[Test]
    public function it_restores_a_soft_deleted_category()
    {
        $category = Category::create([
            'name' => 'Toys',
            'image_path' => 'images/toys.jpg',
            'sort_order' => 3,
        ]);

        $category->delete();
        $category->restore();

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'deleted_at' => null]);
    }

    #[Test]
    public function it_updates_a_category()
    {
        $category = Category::create([
            'name' => 'Clothing',
            'image_path' => 'images/clothing.jpg',
            'sort_order' => 4,
        ]);

        $category->update([
            'name' => 'Apparel',
            'image_path' => 'images/apparel.jpg',
            'sort_order' => 5,
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Apparel',
            'image_path' => 'images/apparel.jpg',
            'sort_order' => 5,
        ]);
    }
}