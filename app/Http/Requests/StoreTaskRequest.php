<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tasks')->where(function ($query) {
                    return $query->where('due_date', $this->input('due_date'));
                }),
            ],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'priority' => ['required', Rule::in(Task::PRIORITIES)],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.unique' => 'A task with the same title already exists for this due date.',
            'due_date.required' => 'The due date field is required.',
            'due_date.date' => 'The due date must be a valid date.',
            'due_date.after_or_equal' => 'The due date must be today or a future date.',
            'priority.required' => 'The priority field is required.',
            'priority.in' => 'Priority must be one of: low, medium, high.',
        ];
    }
}