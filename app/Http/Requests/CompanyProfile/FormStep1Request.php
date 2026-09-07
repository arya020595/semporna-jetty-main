<?php

namespace App\Http\Requests\CompanyProfile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormStep1Request extends FormRequest
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
            'registration_no' => ['nullable', 'string', 'max:100'],

            'boat.id' => ['nullable', 'numeric'],
            'boat.number' => ['nullable', 'string', 'max:100'],
            'boat.license' => ['nullable', 'string', 'max:100'],
            'boat.license_file' => ['nullable', 'array'],
            'boat.license_file.*' => ['nullable', 'file', 'image', 'max:10000'],

            'boat.boatman.*.id' => ['nullable', 'numeric'],
            'boat.boatman.*.boat_id' => ['nullable', 'numeric'],
            'boat.boatman.*.name' => ['nullable', 'string'],
            'boat.boatman.*.ic_no' => ['nullable', 'string', 'max:100'],
            'boat.boatman.*.mate_card' => ['nullable', 'string', 'max:100'],
            'boat.boatman.*.seaman_card_no' => ['nullable', 'string', 'max:100'],

            'boat.boatman.*.ic_no_file' => ['nullable', 'file', 'image', 'max:10000'],
            'boat.boatman.*.mate_card_file' => ['nullable', 'file', 'image', 'max:10000'],
            'boat.boatman.*.seaman_card_file' => ['nullable', 'file', 'image', 'max:10000'],


            'boat.asst.*.id' => ['nullable', 'numeric'],
            'boat.asst.*.boat_id' => ['nullable', 'numeric'],
            'boat.asst.*.name' => ['required', 'string'],
            'boat.asst.*.ic_no' => ['required', 'string', 'max:100'],
            'boat.asst.*.mate_card' => ['nullable', 'string', 'max:100'],

            'instructor' => ['nullable', 'array'],
            'instructor.*.id' => ['nullable', 'numeric'],
            'instructor.*.name' => ['nullable', 'string', 'max:100'],
            'instructor.*.ic_no' => ['nullable', 'max:100'],

            'divemaster' => ['nullable', 'array'],
            'divemaster.*.id' => ['nullable', 'numeric'],
            'divemaster.*.name' => ['nullable', 'string', 'max:100'],
            'divemaster.*.ic_no' => ['nullable', 'max:100'],

            'guide' => ['nullable', 'array'],
            'guide.*.id' => ['nullable', 'numeric'],
            'guide.*.name' => ['nullable', 'string', 'max:100'],
            'guide.*.ic_no' => ['nullable', 'max:100'],
        ];
    }
}
