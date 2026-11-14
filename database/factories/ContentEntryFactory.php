<?php

namespace Database\Factories;

use App\Models\ContentEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContentEntry>
 */
class ContentEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'insight',
            'title' => fake()->sentence(5),
            'slug' => fake()->unique()->slug(),
            'summary' => fake()->sentence(),
            'content_html' => '<p>'.fake()->paragraph().'</p>',
            'status' => 'draft',
        ];
    }
}
