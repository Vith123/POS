<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportDateRangeRequest extends FormRequest
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
            'start_date' => ['sometimes', 'date', 'before_or_equal:end_date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'start_date' => 'start date',
            'end_date' => 'end date',
        ];
    }

    /**
     * Get validated start date with default
     */
    public function getStartDate(): string
    {
        return $this->input('start_date', now()->startOfMonth()->format('Y-m-d'));
    }

    /**
     * Get validated end date with default
     */
    public function getEndDate(): string
    {
        return $this->input('end_date', now()->format('Y-m-d'));
    }
}
