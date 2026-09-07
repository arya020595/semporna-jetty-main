<?php

namespace App\Http\Requests\CompanyManifest;

use Illuminate\Foundation\Http\FormRequest;

class FormPaymentRequest extends FormRequest
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
            "manifest_id" => ["required", "array"],
            "manifest_id.*" => ["required", "numeric"],
        ];
    }
}
