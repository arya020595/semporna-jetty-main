<?php

namespace App\Http\Controllers\Auth;

use App\Actions\CompanyProfile\GenerateNumbers;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Company;
use App\Models\RefDestination;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{

    public function form()
    {
        return Inertia::render("Auth/Register", [
            "title" => "Register Account",
            "additional" => [
                "urlSubmit" => route("register"),
                "arrJetty" => RefDestination::query()
                    ->where("type", RefDestination::TYPE_DEPARTURE)
                    ->get()
            ]
        ]);
    }

    public function store(RegisterRequest $request, GenerateNumbers $generateNumbers)
    {
        $arrData = $request->validated();

        $roleId = $arrData["role"] ?? User::ROLE_AGENT;

        DB::transaction(function () use ($arrData, $roleId, $generateNumbers) {
            $user = User::create([
                "staf_id" => $arrData["staf_id"] ?? null,
                "email" => $arrData["email"],
                "name" => $roleId == User::ROLE_AGENT
                    ? $arrData["username"]
                    : $arrData["name"],
                "ic_no" => $arrData["ic_no"],
                "password" => Hash::make($arrData["password"]),
                "status" => User::STATUS_PENDING,
                "jetty_id" => $arrData["jetty_id"] ?? null
            ]);

            if ($roleId == User::ROLE_AGENT) {
                $arrCompany = $generateNumbers->execute("CP");
                $arrCompany["name"] = $arrData["name"];
                $arrCompany["registration_no"] = $arrData["ic_no"];
                $arrCompany["user_id"] = $user->id;
                $company = Company::create($arrCompany);

                $user->company_id = $company->id;
                $user->status = User::STATUS_ACTIVE;
                $user->save();
            }

            $user->assignRole($roleId);


            if ($roleId == User::ROLE_AGENT) {
                Auth::login($user);
            }

            return $user;
        });

        $role = Role::find($roleId);

        return redirect()->route($role->default_route)
            ->with("message", [
                "status" => "success",
                "message" => "Register Account Success!"
            ]);
    }
}
