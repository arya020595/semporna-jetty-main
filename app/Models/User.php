<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use SoftDeletes, HasFactory, Notifiable, HasRoles, HasApiTokens;

    const FILEABLE_PROFILE_CODE = "profile_picture";

    const ROLE_SUPERADMIN = 1;
    const ROLE_HARTAWAN_STABIL = 2;
    const ROLE_PDRM = 3;
    const ROLE_JABATAN_LAUT = 4;
    const ROLE_SABAH_PARKS = 5;
    const ROLE_JABATAN_PELABUHAN = 6;
    const ROLE_AGENT = 7;
    const ROLE_OPERATOR_JETTY = 8;
    const ROLE_AGENT_EMPLOYEE = 9;

    const STATUS_ACTIVE = 1;
    const STATUS_PENDING = 0;
    const STATUS_NONACTIVE = -1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'original_data' => 'array'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, "company_id");
    }

    public function jetty(): BelongsTo
    {
        return $this->belongsTo(RefDestination::class, "jetty_id");
    }

    public function activeRole()
    {
        return $this->roles->first();
    }

    public function accessLog(): HasMany
    {
        return $this->hasMany(UserAccessLog::class, "user_id");
    }

    public function getStatusTextAttribute(): String
    {
        if ($this->status == self::STATUS_ACTIVE) {
            return "Active";
        }

        return $this->status == self::STATUS_PENDING
            ? "Pending"
            : "Reject";
    }
}
