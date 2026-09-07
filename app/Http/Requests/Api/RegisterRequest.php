<?php

namespace App\Http\Requests\Api;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
            ],
            'ic_no' => ['required', 'alpha_num'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms_condition' => ['accepted'],
            'role' => ['required', 'numeric', Rule::in([
                User::ROLE_JABATAN_LAUT,
                User::ROLE_JABATAN_PELABUHAN,
                User::ROLE_PDRM,
                User::ROLE_SABAH_PARKS
            ])],
            'jetty_id' => ['nullable', 'numeric'],
        ];
    }
}
