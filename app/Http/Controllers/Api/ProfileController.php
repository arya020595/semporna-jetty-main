<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileCredentialsRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserOnesignal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        $user = $user->load("roles");

        return response()
            ->json([
                "message" => "Get User Profile",
                "data" => [
                    "user" => (new UserResource($user))->toArray($request)
                ]
            ]);
    }

    public function update(ProfileRequest $request)
    {
        /**
         * @var User $user
         */
        $user = Auth::user();

        DB::transaction(function () use ($request, $user) {
            $arrUser = $request->validated();

            $arrUser["updated_by"] = Auth::id();

            $user->update($arrUser);
        });

        $user = $user->load("roles");

        return response()
            ->json([
                "message" => "Update Profile Success!",
                "data" => [
                    "user" => (new UserResource($user))->toArray($request)
                ]
            ]);
    }

    public function updateCredentials(ProfileCredentialsRequest $request)
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        $arrUser = $request->validated();

        // check old password
        if ($user->password && !Hash::check($arrUser["password_old"], $user->password)) {
            throw ValidationException::withMessages([
                "password_old" => 'Old password not match!'
            ]);
        }

        DB::transaction(function () use ($arrUser, $user) {
            $user->email = $arrUser["email"];
            if ($arrUser["password"]) {
                $user->password = Hash::make($arrUser["password"]);
            }
            $user->updated_by = Auth::id();
            $user->save();
        });

        $user = $user->load("roles");

        return response()
            ->json([
                "message" => "Update Profile Credentials Success!",
                "data" => [
                    "user" => (new UserResource($user))->toArray($request)
                ]
            ]);
    }

    public function subscribeOnesignal(Request $request)
    {
        $request->validate(["uid" => "required"]);

        /**
         * @var User
         */
        $user = Auth::user();

        $userOnesignal = UserOnesignal::updateOrCreate([
            "user_id" => $user->id,
            "type" => "api"
        ], [
            "user_id" => $user->id,
            "uid" => $request->uid,
            "type" => "api",
            "is_active" => 1
        ]);

        // delete onesignal from another user with same uid
        UserOnesignal::query()
            ->where("user_id", "!=", $user->id)
            ->where("uid", $request->uid)
            ->where("type", "api")
            ->delete();

        return response()
            ->json([
                "message" => "Subscribe onesignal success!",
                "data" => []
            ]);
    }
}
