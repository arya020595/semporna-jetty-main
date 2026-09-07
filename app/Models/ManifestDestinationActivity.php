<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManifestDestinationActivity extends Model
{
    use HasFactory;

    protected $table = "manifest_destination_activity";
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

    public function manifestDestination(): BelongsTo
    {
        return $this->belongsTo(ManifestDestination::class, "manifest_destination_id");
    }

    public function activity(): HasMany
    {
        return $this->hasMany(RefActivity::class);
    }
}
