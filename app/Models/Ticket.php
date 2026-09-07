<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = "ticket";

    const STATUS_AVAILABLE = 0;
    const STATUS_TAKEN = 1;
    const STATUS_USE = 2;
    const STATUS_EXPIRED = -1;

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

    public function manifest(): BelongsTo
    {
        return $this->belongsTo(Manifest::class, "manifest_id");
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class, "passenger_id");
    }
}
