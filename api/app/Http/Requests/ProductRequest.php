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

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->exists('is_active')) {
            $merge['is_active'] = filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if ($this->input('barcode') === '') {
            $merge['barcode'] = null;
        }

        if ($this->input('description') === '') {
            $merge['description'] = null;
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('id');
        $isUpdate = $productId !== null;

        return [
            'category_id' => [$isUpdate ? 'sometimes' : 'required', 'integer', 'exists:categories,id'],
            'name' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sku' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:100', 'unique:products,sku,' . $productId],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:products,barcode,' . $productId],
            'price' => [$isUpdate ? 'sometimes' : 'required', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'stock_quantity' => [$isUpdate ? 'sometimes' : 'required', 'integer', 'min:0'],
            'min_stock_level' => ['nullable', 'integer', 'min:0'],
        ];
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
            'stock_quantity' => 'stock quantity',
            'min_stock_level' => 'minimum stock level',
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
            'stock_quantity.required' => 'The stock quantity is required.',
        ];
    }
}
