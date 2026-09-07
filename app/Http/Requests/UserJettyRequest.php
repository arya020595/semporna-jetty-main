<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserJettyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $arrCreds = [];
        if (!$this->user) {
            $arrCreds = [
                'email' => [
                    'required',
                    Rule::unique('users', 'email')->whereNull('deleted_at')
                ],
                'password' => ['required', 'min:8', 'confirmed']
            ];
        }

        return array_merge([
            'staf_id' => ['nullable'],
            'name' => 'required',
            'ic_no' => 'nullable',
            'status' => ['required', Rule::in(
                User::STATUS_ACTIVE,
                User::STATUS_PENDING,
                User::STATUS_NONACTIVE,
            )],
        ], $arrCreds);
    }
}
