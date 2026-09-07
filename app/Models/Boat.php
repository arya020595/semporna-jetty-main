<?php

namespace App\Models;

use App\Models\Concerns\HasLang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Boat extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = "boat";

    const FILEABLE_LICENSE = "boat_license";
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

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function boatman(): HasMany
    {
        return $this->hasMany(Boatman::class);
    }

    public function boatmanMain(): HasMany
    {
        return $this->boatman()
            ->where("type", Boatman::TYPE_BOATMAN);
    }

    public function boatmanAsst(): HasMany
    {
        return $this->boatman()
            ->where("type", Boatman::TYPE_ASSISTANT);
    }


    public function fileable(): MorphMany
    {
        return $this->morphMany(Fileable::class, "fileable");
    }
}
