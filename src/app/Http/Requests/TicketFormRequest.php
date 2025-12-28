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
            'phone' => 'required_without:customer_id|string|regex:/^\+[1-9]\d{1,14}$/|max:15',
            'name' => 'required_without:customer_id|string|max:255',
            'theme' => 'required|string|max:255',
            'text' => 'required|string',
            'status' => 'nullable|in:new,in_progress,done',
            'date_answer' => 'nullable|date',
            'files' => 'nullable|array|max:5', // Maximum 5 files
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,txt|max:10240', // 10MB max per file
            'attachments' => 'nullable|array|max:5', // Maximum 5 attachments
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,txt|max:10240', // 10MB max per attachment
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_id.required_without_all' => 'Either customer_id or both email and phone must be provided.',
            'email.required_without' => 'Email is required when customer_id is not provided.',
            'email.email' => 'Email must be a valid email address.',
            'phone.required_without' => 'Phone is required when customer_id is not provided.',
            'phone.regex' => 'Phone number must be in international format (e.g., +1234567890).',
            'name.required' => 'Customer name is required.',
            'theme.required' => 'Ticket theme is required.',
            'text.required' => 'Ticket text is required.',
            'attachments.max' => 'You may not upload more than 5 attachments.',
            'attachments.*.file' => 'Each attachment must be a valid file.',
            'attachments.*.mimes' => 'Attachments must be of type: jpg, jpeg, png, pdf, doc, docx, or txt.',
            'attachments.*.max' => 'Each attachment may not be larger than 10MB.',
        ];
    }
}
