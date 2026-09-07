<?php

namespace App\Http\Requests\CompanyProfile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FormBoatRequest extends FormRequest
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
        $rules = [
            'id' => ['nullable', 'numeric'],
            'capacity' => ['required', 'numeric', 'min:1'],
            'license' => ['required', 'string', 'max:100'],
            'license_expiry_date' => ['required', 'date_format:Y-m-d', 'after:today'],

            'license_file_existing' => ['array', 'max:3'],
            'license_file' => ['array', 'max:3'],
            'license_file.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,pdf', 'max:10000'],

            'boatman' => ['required', 'array', 'min:1'],
            'boatman.*.id' => ['nullable', 'numeric'],
            'boatman.*.name' => ['required', 'string'],
            'boatman.*.ic_no' => ['required', 'string', 'max:100'],
            'boatman.*.mate_card' => ['required', 'string', 'max:100'],
            'boatman.*.seaman_card_no' => ['required', 'string', 'max:100'],

            'boatman.*.ic_no_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,pdf', 'max:10000'],
            'boatman.*.mate_card_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,pdf', 'max:10000'],
            'boatman.*.seaman_card_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,pdf', 'max:10000'],

            'asst' => ['required', 'array', 'min:1'],
            'asst.*.id' => ['nullable', 'numeric'],
            'asst.*.name' => ['required', 'string'],
            'asst.*.ic_no' => ['required', 'string', 'max:100'],
            'asst.*.mate_card' => ['nullable', 'string', 'max:100'],
            'asst.*.seaman_card_no' => ['nullable', 'string', 'max:100'],
        ];

        // License Page 1 is required if no existing file
        if (!$this->input('id') || !$this->input('license_file_existing.0')) {
            $rules['license_file.0'] = ['required', 'file', 'mimes:jpg,jpeg,png,gif,pdf', 'max:10000'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'capacity.required' => 'Boat capacity is required',
            'capacity.min' => 'Boat capacity must be at least 1',
            'license_expiry_date.after' => 'License expiry date must be after today',
            'license_file.0.required' => 'Boat License Page 1 is required',

            'boatman.*.name.required' => 'Boatman name is required',
            'boatman.*.ic_no.required' => 'Boatman IC is required',
            'boatman.*.mate_card.required' => 'Boatman Mate Card No. is required',
            'boatman.*.seaman_card_no.required' => 'Boatman Seaman Card No. is required',

            'asst.*.name.required' => 'Assistant Boatman name is required',
            'asst.*.ic_no.required' => 'Assistant Boatman IC is required',
        ];
    }
}
