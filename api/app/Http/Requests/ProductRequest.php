<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $productId = $this->route('id');
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sku' => 'required|string|max:100|unique:products,sku,' . $productId,
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $productId,
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'image' => $this->isMethod('post') ? 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' : 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];

        // Additional rules for creation
        if ($this->isMethod('post')) {
            $rules['initial_stock'] = 'required|integer|min:0';
            $rules['min_stock'] = 'nullable|integer|min:0';
            $rules['max_stock'] = 'nullable|integer|min:0';
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'name' => 'product name',
            'sku' => 'SKU',
            'barcode' => 'barcode',
            'price' => 'selling price',
            'cost' => 'cost price',
            'initial_stock' => 'initial stock quantity',
            'min_stock' => 'minimum stock level',
            'max_stock' => 'maximum stock level',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category does not exist.',
            'name.required' => 'The product name is required.',
            'sku.required' => 'The SKU is required.',
            'sku.unique' => 'This SKU already exists.',
            'barcode.unique' => 'This barcode already exists.',
            'price.required' => 'The selling price is required.',
            'price.min' => 'The price must be a positive value.',
            'initial_stock.required' => 'The initial stock quantity is required.',
        ];
    }
}
