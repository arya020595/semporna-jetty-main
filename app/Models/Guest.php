<?php

namespace App\Models;

use App\Models\Concerns\HasLang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guest extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = "guests";

    protected $appends = [
        'gender_text'
    ];

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
        return $this->belongsTo(Manifest::class);
    }

    public function refNationality(): BelongsTo
    {
        return $this->belongsTo(RefNationality::class, "nationality_id");
    }

    // public function refActivity(): BelongsTo
    // {
    //     return $this->belongsTo(RefActivity::class, "activity_id");
    // }

    public function getGenderTextAttribute(): String
    {
        return $this->gender == 'M' ? 'Male' : 'Female';
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(
            RefActivity::class,
            'guest_has_activities',
            'guest_id',
            'ref_activity_id'
        )->withTimestamps();
    }

    public function manifestFee(): BelongsTo
    {
        return $this->belongsTo(ManifestFee::class, "manifest_fee_id");
    }
}
