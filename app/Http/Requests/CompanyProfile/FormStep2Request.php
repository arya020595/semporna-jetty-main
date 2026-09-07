<?php

namespace App\Http\Requests\CompanyProfile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormStep2Request extends FormRequest
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

            'instructor.*.name' => ['nullable', 'string', 'max:100'],
            'instructor.*.ic_no' => ['nullable', 'max:100'],

            'divemaster.*.name' => ['nullable', 'string', 'max:100'],
            'divemaster.*.ic_no' => ['nullable', 'max:100'],

            'guide.*.name' => ['nullable', 'string', 'max:100'],
            'guide.*.ic_no' => ['nullable', 'max:100'],
        ];
    }
}
