<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:480'],
            'is_published' => ['sometimes', 'boolean'],
            'attempt_limit_type' => ['required', 'string', 'in:once,repeatable,custom'],
            'max_attempts' => ['required_if:attempt_limit_type,custom', 'nullable', 'integer', 'min:1'],
        ];
    }
}
