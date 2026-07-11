<?php

namespace Database\Factories;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizFactory extends Factory
{
    protected $model = Quiz::class;

    public function definition(): array
    {
        $attemptType = $this->faker->randomElement(['once', 'repeatable', 'custom']);
        $maxAttempts = ($attemptType === 'custom') ? $this->faker->numberBetween(2, 5) : (($attemptType === 'once') ? 1 : null);

        return [
            'title' => $this->faker->sentence(4) . ' Assessment',
            'description' => $this->faker->paragraph(3),
            'duration_minutes' => $this->faker->randomElement([15, 30, 45, 60, 90]),
            'is_published' => $this->faker->boolean(80), // 80% published by default
            'attempt_limit_type' => $attemptType,
            'max_attempts' => $maxAttempts,
        ];
    }
}
