<?php

namespace Database\Factories;

use App\Models\PrivateClassItem;
use App\Models\PrivateClass;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PrivateClassItem>
 */
class PrivateClassItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PrivateClassItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'private_class_id' => PrivateClass::factory(),
            'model_id' => Course::factory(),
            'model_type' => Course::class,
            'order' => fake()->numberBetween(1, 10),
        ];
    }

    /**
     * Set the private class for this item.
     */
    public function forPrivateClass(PrivateClass $privateClass): static
    {
        return $this->state(fn (array $attributes) => [
            'private_class_id' => $privateClass->id,
        ]);
    }

    /**
     * Set the model for this item.
     */
    public function forModel($model): static
    {
        return $this->state(fn (array $attributes) => [
            'model_id' => $model->id,
            'model_type' => get_class($model),
        ]);
    }

    /**
     * Set the order for this item.
     */
    public function withOrder(int $order): static
    {
        return $this->state(fn (array $attributes) => [
            'order' => $order,
        ]);
    }
}