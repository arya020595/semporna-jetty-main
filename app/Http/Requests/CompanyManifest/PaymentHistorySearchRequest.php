<?php

namespace App\Http\Requests\CompanyManifest;

use Illuminate\Foundation\Http\FormRequest;

class PaymentHistorySearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'search_fields' => 'nullable|array',
            'search_values' => 'nullable|array',
            'page'          => 'nullable|numeric',
            'per_page'      => 'nullable|numeric',
            'order_by'      => 'nullable|in:code,created_at,first_name,amount,status',
            'order_type'    => 'nullable|in:asc,desc,ASC,DESC',
            'date_from'     => 'nullable|date',
            'date_to'       => 'nullable|date|after_or_equal:date_from',
        ];
    }
}
