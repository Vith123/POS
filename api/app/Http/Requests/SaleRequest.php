<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
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
        return [
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'payments' => 'required|array|min:1',
            'payments.*.method' => 'required|in:cash,card,mobile',
            'payments.*.amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'customer_id' => 'customer',
            'items' => 'sale items',
            'items.*.product_id' => 'product',
            'items.*.quantity' => 'quantity',
            'items.*.price' => 'price',
            'discount_type' => 'discount type',
            'discount_value' => 'discount value',
            'tax_rate' => 'tax rate',
            'payments' => 'payments',
            'payments.*.method' => 'payment method',
            'payments.*.amount' => 'payment amount',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'items.required' => 'At least one item is required.',
            'items.min' => 'At least one item is required.',
            'items.*.product_id.required' => 'Product is required for each item.',
            'items.*.product_id.exists' => 'One or more products do not exist.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.price.required' => 'Price is required for each item.',
            'discount_type.in' => 'Discount type must be percentage or fixed.',
            'payments.required' => 'At least one payment is required.',
            'payments.min' => 'At least one payment is required.',
            'payments.*.method.required' => 'Payment method is required.',
            'payments.*.method.in' => 'Payment method must be cash, card, or mobile.',
            'payments.*.amount.required' => 'Payment amount is required.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Skip if items validation already failed
            if (!isset($this->items) || !is_array($this->items)) {
                return;
            }

            // Calculate subtotal from items
            $subtotal = collect($this->items)->sum(function ($item) {
                return ($item['quantity'] ?? 0) * ($item['price'] ?? 0);
            });

            // Calculate discount
            $discount = 0;
            if ($this->discount_type === 'percentage') {
                $discount = $subtotal * (($this->discount_value ?? 0) / 100);
            } elseif ($this->discount_type === 'fixed') {
                $discount = $this->discount_value ?? 0;
            }

            // Calculate tax
            $taxableAmount = $subtotal - $discount;
            $tax = $taxableAmount * (($this->tax_rate ?? 0) / 100);

            // Calculate total
            $total = $taxableAmount + $tax;

            // Calculate total payments
            if (!isset($this->payments) || !is_array($this->payments)) {
                return;
            }

            $totalPayments = collect($this->payments)->sum('amount');

            // Validate that payments cover the total
            if ($totalPayments < $total) {
                $validator->errors()->add('payments', 'Total payment amount is less than the sale total.');
            }
        });
    }
}
