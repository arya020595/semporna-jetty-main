<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManifestBoatman extends Model
{
    use HasFactory;

    protected $table = "manifest_boatman";
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

    public function boatman(): BelongsTo
    {
        return $this->belongsTo(Boatman::class);
    }

    public function manifest(): BelongsTo
    {
        return $this->belongsTo(Manifest::class);
    }
}
