<?php

namespace Database\Factories;

use App\Models\Qaror;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Qaror>
 */
class QarorFactory extends Factory
{
    protected $model = Qaror::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'published_id' => fake()->unique()->randomNumber(5, true),
            'title' => fake()->sentence(8),
            'number' => fake()->unique()->numberBetween(1, 9999),
            'created_date' => fake()->dateTimeBetween('-5 years', 'now'),
            'pdf_path' => 'qarorlar/qaror_' . fake()->uuid() . '.pdf',
            'file' => fake()->optional(0.7)->filePath(),
            'text' => fake()->optional(0.5)->paragraphs(3, true),
            'views' => fake()->numberBetween(0, 1000),
        ];
    }

    /**
     * Indicate that the qaror is from a specific year.
     */
    public function year(int $year): static
    {
        return $this->state(fn (array $attributes) => [
            'created_date' => fake()->dateTimeBetween("$year-01-01", "$year-12-31"),
        ]);
    }

    /**
     * Indicate that the qaror has high views.
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'views' => fake()->numberBetween(5000, 50000),
        ]);
    }
}
