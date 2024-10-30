<?php
namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'id' => (string) Str::uuid(),
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'category_id' => \App\Models\Category::factory(),
            'main_image_id' => \App\Models\Image::factory(),
            'attributes' => [
                'Height' => $this->faker->randomNumber(3) . 'cm',
                'Weight' => $this->faker->randomNumber(2) . 'kg',
            ],
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }
}