<?php

namespace App\Http\Requests\ReferenceTable;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NationalityRequest extends FormRequest
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
            'code' => [
                optional($this->nationality)->id ? 'required' : 'nullable',
                Rule::unique('ref_nationality', 'code')
                    ->ignore(optional($this->nationality)->id)
            ],
            'title' => ['required'],
        ];
    }
}
