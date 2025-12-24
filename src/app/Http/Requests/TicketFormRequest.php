<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TicketFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow if user is authenticated (for API requests via Sanctum)
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required_without_all:email,phone',
            'email' => 'required_without:customer_id|email',
            'phone' => 'required_without:customer_id|string|max:20',
            'name' => 'nullable|string|max:255',
            'theme' => 'required|string|max:255',
            'text' => 'required|string',
            'status' => 'nullable|in:new,in_progress,done',
            'date_answer' => 'nullable|date',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240', // 10MB max
        ];
    }
}
