<?php

namespace App\Http\Requests\CompanyManifest;

use Illuminate\Foundation\Http\FormRequest;

class FormManifestRequest extends FormRequest
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
        $rules = [

            'id' => ['nullable', 'numeric'],
            // 'type' => ['nullable', Rule::in([Manifest::TYPE_BY_COMPANY, Manifest::TYPE_RENTAL])],
            'is_rent' => ['nullable', 'boolean'],
            // Form1
            'departure_date' => ['required', 'string', 'date_format:Y-m-d'],
            'departure_time' => ['required', 'string', 'date_format:H:i'],

            'company_id' => ['required'],
            'company_name' => ['required', 'string'],

            'boat_id' => ['required_if:is_rent,false'],
            'boat_number' => ['required', 'string', 'max:100'],

            'boatman_id' => ['required_if:is_rent,false'],
            'boatman_name' => ['required', 'string', 'max:100'],
            'boatman_mate_no' => ['required', 'string', 'max:100'],
            'seaman_no' => ['required', 'string', 'max:100'],
            'boatman_ic_no' => ['required', 'string', 'max:100'],

            "assistant_id" => ['nullable'],
            "assistant_name" => ['required', 'string', 'max:100'],
            "assistant_mate_no" => ['nullable', 'string', 'max:100'],
            "assistant_ic_no" => ['required', 'string', 'max:100'],
            "assistant_seaman_no" => ['nullable', 'string', 'max:100'],

            "is_dive_activity" => ["nullable"],

            // Form2
            "instructor" => ['nullable', 'array'],
            "instructor.*.boatman_id" => ['nullable', 'numeric'],
            "instructor.*.name" => ['nullable', 'string', 'max:100'],
            "instructor.*.ic_no" => ['nullable', 'max:100'],

            "divemaster" => ['nullable', 'array'],
            "divemaster.*.boatman_id" => ['nullable', 'numeric'],
            "divemaster.*.name" => ['required_with:divemaster.*.boatman_id'],
            "divemaster.*.ic_no" => ['required_with:divemaster.*.boatman_id', 'max:100'],

            "guide" => ['nullable', 'array'],
            "guide.*.boatman_id" => ['nullable', 'numeric'],
            "guide.*.name" => ['required_with:guide.*.boatman_id'],
            "guide.*.ic_no" => ['required_with:guide.*.boatman_id', 'max:100'],


            // Form3
            "departure_id" => ['required'],
            "destination" => ['required', 'array'],

            "destination_activity" => ["array"],
            "destination_activity.*.destination_id" => ["required"],
            "destination_activity.*.activity" => ["array"],


            //Form4
            "is_final" => ['required', 'in:0,1'],
            "passengers" => ['required', 'array'],

            "passengers.*.id" => ['nullable'],
            "passengers.*.name" => ['required', 'string', 'max:100'],
            "passengers.*.ic_no" => ['required', 'alpha_num', 'max:100'],
            "passengers.*.nationality_id" => ['nullable'],
            "passengers.*.nationality_name" => ['required', "string"],
            "passengers.*.year_of_birth" => ['nullable', 'numeric', 'digits:4'],
            "passengers.*.age" => ['required', 'numeric', 'min:1', 'max:100'],
            "passengers.*.gender" => ['required', 'in:F,M'],
            "passengers.*.next_of_kin" => ['required', 'string', 'max:100'],
            "passengers.*.emergency_contact" => ['required', 'string', 'max:100'],
            "passengers.*.activity_ids" => ['required', 'array'],
            "passengers.*.activity_ids.*" => ['required', 'numeric', 'exists:ref_activity,id'],
            "passengers.*.is_stay_resort" => ['nullable'],
            "passengers.*.resort_name" => ['nullable'],
            "passengers.*.ticket_code" => ['nullable'],

            "staff" => ['nullable', 'array'],

            "staff.*.id" => ['nullable'],
            "staff.*.name" => ['required', 'string', 'max:100'],
            "staff.*.ic_no" => ['required', 'alpha_num', 'max:100'],
            "staff.*.nationality_id" => ['nullable'],
            "staff.*.nationality_name" => ['required', "string"],
            "staff.*.year_of_birth" => ['nullable', 'numeric', 'digits:4'],
            "staff.*.age" => ['required', 'numeric', 'min:1', 'max:100'],
            "staff.*.gender" => ['required', 'in:F,M'],
            "staff.*.next_of_kin" => ['nullable', 'string', 'max:100'],
            "staff.*.emergency_contact" => ['nullable', 'string', 'max:100'],
            "staff.*.activity_id" => ['nullable', 'numeric'],
            "staff.*.activity_ids" => ['nullable', 'array'],
            "staff.*.activity_ids.*" => ['nullable', 'numeric'],
            "staff.*.is_stay_resort" => ['nullable'],
            "staff.*.resort_name" => ['nullable', 'string', 'max:100'],
        ];

        $manifest = $this->route('manifest');

        if ($manifest) {
            if ($this->input('departure_date') !== $manifest->departure_date) {
                $rules['departure_date'][] = 'after_or_equal:today';
            }
        } else {
            $rules['departure_date'][] = 'after_or_equal:today';
        }

        return $rules;
    }
}
