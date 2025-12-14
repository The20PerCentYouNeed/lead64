<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Evaluation>
 */
class EvaluationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'score' => fake()->numberBetween(0, 100),
            'classification' => fake()->randomElement(['hot', 'warm', 'cold']),
            'reasoning' => fake()->paragraph(),
            'insights' => [
                'strengths' => [fake()->sentence(), fake()->sentence()],
                'concerns' => [fake()->sentence()],
            ],
            'recommendations' => [
                fake()->sentence(),
                fake()->sentence(),
            ],
        ];
    }
}
