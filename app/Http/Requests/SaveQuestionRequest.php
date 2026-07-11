<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $rules = [
            'question_text' => ['required', 'string'],
            'type' => ['required', 'in:multiple_choice,essay'],
        ];

        if ($this->input('type') === 'multiple_choice') {
            $rules['options'] = ['required', 'array', 'min:4', 'max:4'];
            $rules['options.*'] = ['required', 'string', 'max:255'];
            $rules['correct_answer'] = ['required', 'in:a,b,c,d'];
        } else {
            $rules['correct_answer'] = ['required', 'string'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'options.required' => 'Multiple choice questions require exactly 4 options.',
            'options.array' => 'Options must be provided as an array.',
            'options.min' => 'Multiple choice questions require exactly 4 options.',
            'options.max' => 'Multiple choice questions require exactly 4 options.',
            'options.*.required' => 'Each option field is required.',
            'correct_answer.in' => 'For multiple choice, correct answer must be one of a, b, c, or d.',
            'correct_answer.required' => 'The correct answer/answer guide field is required.',
        ];
    }
}
