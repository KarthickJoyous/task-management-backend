<?php

namespace App\Http\Requests\Api\Task;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TaskIndexRequest extends FormRequest
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
            'search' => ['nullable', 'string', 'max:30'],
            'status' => ['nullable', Rule::in(PENDING, COMPLETED)],
            'skip' => ['required', 'integer', 'min:0'],
            'take' => ['required', 'integer', 'min:1', 'max:20'],
            'order_by' => ['nullable', Rule::in([LATEST, OLDEST])]
        ];
    }
}
