<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['multiple_choice', 'essay']);
        $options = null;
        $correct_answer = '';

        if ($type === 'multiple_choice') {
            $options = [
                'a' => $this->faker->words(3, true),
                'b' => $this->faker->words(3, true),
                'c' => $this->faker->words(3, true),
                'd' => $this->faker->words(3, true),
            ];
            $correct_answer = $this->faker->randomElement(['a', 'b', 'c', 'd']);
        } else {
            $correct_answer = 'This is an answer key guide showing what keywords ' . 
                'like ' . implode(', ', $this->faker->words(3)) . ' should be present in the response.';
        }

        return [
            'quiz_id' => Quiz::factory(),
            'question_text' => $this->faker->sentence(10) . '?',
            'type' => $type,
            'options' => $options,
            'correct_answer' => $correct_answer,
        ];
    }
}
