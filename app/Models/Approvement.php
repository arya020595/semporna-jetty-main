<?php

namespace App\Models;

use App\Models\Concerns\HasLang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class Approvement extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = "approvement";

    const STATUS_APPROVED = 1;
    const STATUS_AMEND = 3;
    const STATUS_REJECTED = -1;

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

    protected $appends = [
        "status_text"
    ];

    protected $casts = [
        'status'  => 'integer',
        'role_id' => 'integer',
        'version' => 'integer',
    ];

    public function manifest(): BelongsTo
    {
        return $this->belongsTo(Manifest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function getStatusTextAttribute(): String
    {
        if ($this->status == self::STATUS_APPROVED) {
            return "Approved";
        }

        return $this->status == self::STATUS_AMEND
            ? "Amend"
            : "Rejected";
    }
}
