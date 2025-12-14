<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'email' => fake()->email(),
            'message' => fake()->paragraph(),
            'phone' => fake()->optional()->phoneNumber(),
            'job_title' => fake()->optional()->jobTitle(),
            'company_name' => fake()->optional()->company(),
            'company_size' => fake()->optional()->randomElement(['1-10', '11-50', '51-200', '201-500', '500+']),
            'industry' => fake()->optional()->companySuffix(),
            'website' => fake()->optional()->url(),
            'country' => fake()->optional()->country(),
            'budget' => fake()->optional()->randomElement(['<$10k', '$10k-$50k', '$50k-$100k', '$100k+']),
            'timeline' => fake()->optional()->randomElement(['Immediate', '1-3 months', '3-6 months', '6+ months']),
            'source' => fake()->optional()->randomElement(['Google Ads', 'LinkedIn', 'Referral', 'Direct', 'Other']),
            'linkedin_url' => fake()->optional()->url(),
            'facebook_url' => fake()->optional()->url(),
            'instagram_url' => fake()->optional()->url(),
            'twitter_url' => fake()->optional()->url(),
            'extra_info' => null,
            'status' => 'pending',
        ];
    }
}
