<?php

namespace App\Actions\User;

use App\Models\User;
use App\Models\UserAccessLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class SetUserAccessLog
{

    /**
     * Execute the action
     *
     * @param  User  $user
     * @param  string  $type
     * @param  array  $options
     * @return UserAccessLog
     * @throws Throwable
     */
    public function execute($userId, string $type = "visit", array $options = []): UserAccessLog
    {
        return UserAccessLog::create([
            "user_id" => $userId,
            "action" => $type,
            "url" => $options["url"] ?? request()->url(),
            "ip_address" => request()->ip(),
            "user_agent" => request()->userAgent()
        ]);
    }
}
