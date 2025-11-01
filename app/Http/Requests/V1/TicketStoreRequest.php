<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class TicketStoreRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'status' => 'sometimes|in:open_in,in_progress,on_hold,closed',
            'priority' => 'sometimes|in:low,medium,high,urgent'
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status must be one of: open, in_progress, on_hold, closed.',
            'priority.in' => 'Priority must be one of: low, medium, high, urgent.',
        ];
    }
}
