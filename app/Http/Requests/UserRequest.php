<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
                'email' => ['required', Rule::unique('users', 'email')],
                'password' => ['required', 'min:8', 'confirmed']
            ];
        }

        return array_merge([
            'staf_id' => ['nullable'],
            'name' => 'required',
            'ic_no' => 'nullable',
            'role' => 'required',
            'jetty_id' => 'required_if:role,' . User::ROLE_OPERATOR_JETTY,
            'company_id' => 'required_if:role,' . User::ROLE_AGENT,
            'status' => ['required', Rule::in(1, 0, true, false)],
            'file_picture' => ['nullable', 'file', 'image', 'max:1024'],
            'old_picture' => ['nullable', 'string']
        ], $arrCreds);
    }
}
