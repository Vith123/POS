<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            // Rules for creating a return
            return [
                'sale_id' => 'required|exists:sales,id',
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'reason' => 'required|string|max:500',
                'refund_amount' => 'required|numeric|min:0',
            ];
        } else {
            // Rules for updating return status
            return [
                'status' => 'required|in:pending,approved,rejected',
            ];
        }
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'sale_id' => 'sale',
            'product_id' => 'product',
            'quantity' => 'quantity',
            'reason' => 'return reason',
            'refund_amount' => 'refund amount',
            'status' => 'return status',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'sale_id.required' => 'The sale reference is required.',
            'sale_id.exists' => 'The selected sale does not exist.',
            'product_id.required' => 'Please select a product.',
            'product_id.exists' => 'The selected product does not exist.',
            'quantity.required' => 'The quantity is required.',
            'quantity.min' => 'The quantity must be at least 1.',
            'reason.required' => 'The return reason is required.',
            'refund_amount.required' => 'The refund amount is required.',
            'status.required' => 'The status is required.',
            'status.in' => 'The status must be pending, approved, or rejected.',
        ];
    }
}
