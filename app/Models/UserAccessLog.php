<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAccessLog extends Model
{

    const TYPE_LOGIN = "login";
    const TYPE_LOGOUT = "logout";
    const TYPE_VISIT = "visit";

    protected $table = "user_access_log";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
