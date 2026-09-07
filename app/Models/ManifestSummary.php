<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManifestSummary extends Model
{
    use HasFactory;

    protected $table = "manifest_summary";

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

    public function manifest(): BelongsTo
    {
        return $this->belongsTo(Manifest::class, "manifest_id");
    }
}
