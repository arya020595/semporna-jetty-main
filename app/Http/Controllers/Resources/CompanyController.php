<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $list = Company::query()
            ->where('name', 'LIKE', "%{$request->search}%")
            ->select('id', 'name as description')
            ->limit(20)
            ->get();

        return response([
            'message' => 'Search Company',
            'data' => $list
        ], 200);
    }

    public function show(Request $request, Company $company)
    {
        $company->description = $company->name;

        return response([
            'message' => 'Show Company',
            'data' => $company
        ], 200);
    }
}
