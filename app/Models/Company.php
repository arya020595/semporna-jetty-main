<?php

namespace App\Models;

use App\Models\Concerns\HasLang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = "company";

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

    public function user(): HasMany
    {
        return $this->hasMany(User::class, "company_id");
    }

    public function boat(): HasMany
    {
        return $this->hasMany(Boat::class);
    }

    public function boatman(): HasMany
    {
        return $this->hasMany(Boatman::class);
    }

    public function boatmanInstructor(): HasMany
    {
        return $this->boatman()
            ->where("type", Boatman::TYPE_INSTRUCTOR);
    }

    public function boatmanDivemaster(): HasMany
    {
        return $this->boatman()
            ->where("type", Boatman::TYPE_DIVEMASTER);
    }

    public function boatmanGuide(): HasMany
    {
        return $this->boatman()
            ->where("type", Boatman::TYPE_GUIDE);
    }
}
