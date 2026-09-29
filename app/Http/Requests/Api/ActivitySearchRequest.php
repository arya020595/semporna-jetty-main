<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ActivitySearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'filter' => 'nullable|array',
            'filter.search' => 'nullable|string|max:100',
            'filter.status' => ['nullable', 'regex:/^-?\d+(,-?\d+)*$/'],
            'filter.payment_status' => ['nullable', 'regex:/^-?\d+(,-?\d+)*$/'],
            'filter.departure_date' => 'nullable|date_format:Y-m-d',
            'filter.departure_date_from' => 'nullable|date_format:Y-m-d',
            'filter.departure_date_to' => 'nullable|date_format:Y-m-d',
            'sort' => 'nullable|string',
            'page' => 'nullable|numeric',
            'per_page' => 'nullable|numeric',

            // Legacy search parameters, still sent by released versions of the mobile app
            'search_fields' => 'nullable|array',
            'search_values' => 'nullable|array',
            'order_type' => 'nullable|in:asc,desc,ASC,DESC',
        ];
    }
}
