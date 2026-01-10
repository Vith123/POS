<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComparativeReportRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'period1_start' => ['sometimes', 'date', 'before_or_equal:period1_end'],
            'period1_end' => ['sometimes', 'date', 'after_or_equal:period1_start'],
            'period2_start' => ['sometimes', 'date', 'before_or_equal:period2_end'],
            'period2_end' => ['sometimes', 'date', 'after_or_equal:period2_start'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'period1_start' => 'period 1 start date',
            'period1_end' => 'period 1 end date',
            'period2_start' => 'period 2 start date',
            'period2_end' => 'period 2 end date',
        ];
    }

    /**
     * Get validated period 1 start date with default
     */
    public function getPeriod1Start(): string
    {
        return $this->input('period1_start', now()->subMonth()->startOfMonth()->format('Y-m-d'));
    }

    /**
     * Get validated period 1 end date with default
     */
    public function getPeriod1End(): string
    {
        return $this->input('period1_end', now()->subMonth()->endOfMonth()->format('Y-m-d'));
    }

    /**
     * Get validated period 2 start date with default
     */
    public function getPeriod2Start(): string
    {
        return $this->input('period2_start', now()->startOfMonth()->format('Y-m-d'));
    }

    /**
     * Get validated period 2 end date with default
     */
    public function getPeriod2End(): string
    {
        return $this->input('period2_end', now()->format('Y-m-d'));
    }
}
