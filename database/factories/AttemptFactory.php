<?php

namespace Database\Factories;

use App\Models\Attempt;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttemptFactory extends Factory
{
    protected $model = Attempt::class;

    public function definition(): array
    {
        $started = $this->faker->dateTimeBetween('-7 days', 'now');
        $completed = (clone $started)->modify('+' . $this->faker->numberBetween(5, 30) . ' minutes');

        return [
            'user_id' => User::factory(),
            'quiz_id' => Quiz::factory(),
            'score' => $this->faker->numberBetween(0, 100),
            'started_at' => $started,
            'completed_at' => $completed,
        ];
    }
}
