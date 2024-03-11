<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing>
 */
class ListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function definition(): array
    {
        $date_time = fake()->dateTimeBetween('-1 month', 'now');
        $title = fake()->sentence(\random_int(5, 7));
        $content = '';
        for ($i = 0; $i < 5; $i++) {
            $content .= '<p class="mb-4">' . fake()->sentence(random_int(5, 10), true) . '</p>';
        }

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . random_int(1111, 9999),
            'company' => fake()->company,
            'location' => fake()->country,
            'logo' => fake()->image(storage_path('app/public')),
            'is_highlighted' => (random_int(1, 9) > 7),
            'is_active' => true,
            'content' => $content,
            'link' => fake()->url,
            'created_at' => $date_time,
            'updated_at' => $date_time
        ];
    }
}
