<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FeedbackFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow unauthenticated requests for feedback
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'phone' => 'required|string|regex:/^\+[1-9]\d{1,14}$/|max:15',
            'name' => 'required|string|max:255',
            'theme' => 'required|string|max:255',
            'text' => 'required|string',
            'attachments' => 'nullable|array|max:5', // Maximum 5 attachments from feedback widget
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
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'phone.regex' => 'Phone number must be in international format (e.g., +1234567890).',
            'phone.required' => 'Phone is required.',
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
