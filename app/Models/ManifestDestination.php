<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManifestDestination extends Model
{
    use HasFactory;

    protected $table = "manifest_destination";
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

    public function destination(): BelongsTo
    {
        return $this->belongsTo(RefDestination::class, "ref_destination_id");
    }

    public function manifest(): BelongsTo
    {
        return $this->belongsTo(Manifest::class);
    }

    public function manifestDestinationActivity(): HasMany
    {
        return $this->hasMany(ManifestDestinationActivity::class, "manifest_destination_id");
    }
}
