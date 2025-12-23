<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Customer;
use App\Models\Ticket;
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
            'customer_id' => 'nullable|exists:customers,id',
            'name' => 'required_without:customer_id|string|max:255',
            'phone' => 'required_without:customer_id|string|max:20',
            'email' => [
                'required_without:customer_id',
                'email',
                function ($attribute, $value, $fail) {
                    if (! $this->has('customer_id')) {
                        $existingCustomer = Customer::where('email', $value)->first();
                        // TODO: think about it tomorrow
                        // For update operations, allow if it's the same customer
                        if (request()->routeIs('tickets.update')) {
                            /** @var Ticket|null $ticket */
                            $ticket = Ticket::with(['customer'])->find(request()->route('ticket'));
                            if ($ticket && $ticket->customer) {
                                /** @var Customer $customer */
                                $customer = $ticket->customer;
                                if ($customer->email === $value) {
                                    return;
                                }
                            }
                        }
                        // For create operations, don't fail validation for existing emails
                        // The business logic will handle finding existing customers
                    }
                },
            ],
            'theme' => 'required|string|max:255',
            'text' => 'required|string',
            'status' => 'nullable|in:new,in_progress,done',
            'date_answer' => 'nullable|date',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240', // 10MB max
        ];
    }
}
