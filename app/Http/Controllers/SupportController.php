<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupportRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendEmailSupportAdmin;
use App\Models\Support;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SupportController extends Controller
{
    public function create(Request $request)
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        $user = $user->load("roles");

        return Inertia::render('Support/Create', [
            "title" => "Support",
            "additional" => [
                "urlStore" => route("panel.support.store"),
                "user" => (new UserResource($user))->toArray($request)
            ]
        ]);
    }

    public function store(SupportRequest $request)
    {
        $arrData = $request->validated();

        DB::transaction(function () use ($arrData) {
            $authId = Auth::id();

            $arrData["user_id"] = $authId;

            $support = Support::create($arrData);

            SendEmailSupportAdmin::dispatch(
                config("mail.admin.email", "dio.22ratar@gmail.com"),
                $support->id
            );

            return $support;
        });

        return redirect()->back()
            ->with("message", [
                "status" => "success",
                "message" => "Submit Success!"
            ]);
    }
}
