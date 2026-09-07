<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory;

    protected $table = "payment";

    const STATUS_PAID = 1;
    const STATUS_FAILED = -1;
    const STATUS_PENDING = 0;

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

    public function manifestFee(): HasMany
    {
        return $this->hasMany(ManifestFee::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
