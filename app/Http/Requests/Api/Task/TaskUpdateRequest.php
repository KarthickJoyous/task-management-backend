<?php

namespace App\Http\Requests\Api\Task;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TaskUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'min:1', 'max:255', 'exclude_if:title,null'],
            'description' => ['nullable', 'string', 'min:1', 'max:255', 'exclude_if:description,null'],
            'status' => ['nullable', Rule::in(PENDING, COMPLETED), 'exclude_if:status,null']
        ];
    }
}
