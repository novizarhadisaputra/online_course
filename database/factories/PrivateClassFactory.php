<?php

namespace Database\Factories;

use App\Models\PrivateClass;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PrivateClass>
 */
class PrivateClassFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PrivateClass::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->sentence(3);
        
        return [
            'id' => fake()->uuid(),
            'name' => $name,
            'slug' => Str::slug($name),
            'short_description' => fake()->sentence(10),
            'description' => fake()->paragraph(5),
            'duration' => fake()->numberBetween(1, 12),
            'duration_units' => fake()->randomElement(['hours', 'days', 'weeks', 'months']),
            'max_participants' => fake()->numberBetween(5, 50),
            'start_date' => fake()->dateTimeBetween('now', '+1 month'),
            'end_date' => fake()->dateTimeBetween('+1 month', '+6 months'),
            'status' => fake()->boolean(80), // 80% chance of being active
            'is_paid' => fake()->boolean(70), // 70% chance of being paid
        ];
    }

    /**
     * Indicate that the private class should be active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => true,
        ]);
    }

    /**
     * Indicate that the private class should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }

    /**
     * Indicate that the private class should be paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_paid' => true,
        ]);
    }

    /**
     * Indicate that the private class should be free.
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_paid' => false,
        ]);
    }
}