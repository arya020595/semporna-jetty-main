<?php

namespace App\Http\Requests\ReferenceTable;

use App\Actions\Activity\GetActivity;
use App\Helpers\DatatablesHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $columns = (new GetActivity)->getColumns();
        $orderField = DatatablesHelper::getOrderAbleField($columns);
        $searchField = DatatablesHelper::getSearchableField($columns);

        return [
            'search_fields' => ['nullable', 'array', Rule::in($searchField)],
            'search_values' => 'nullable|array',
            'page' => 'nullable|numeric',
            'per_page' => 'nullable|numeric',
            'order_by' => ['nullable', Rule::in($orderField)],
            'order_type' => 'nullable|in:asc,desc,ASC,DESC'
        ];
    }
}
