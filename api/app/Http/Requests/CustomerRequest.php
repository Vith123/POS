<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
        $customerId = $this->route('id');

        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customerId,
            'phone' => 'required|string|max:20|unique:customers,phone,' . $customerId,
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_vip' => 'nullable|boolean',
            'loyalty_points' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'customer name',
            'email' => 'email address',
            'phone' => 'phone number',
            'address' => 'address',
            'city' => 'city',
            'postal_code' => 'postal code',
            'is_vip' => 'VIP status',
            'loyalty_points' => 'loyalty points',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The customer name is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone.required' => 'The phone number is required.',
            'phone.unique' => 'This phone number is already registered.',
        ];
    }
}
