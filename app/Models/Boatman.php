<?php

namespace App\Models;

use App\Models\Concerns\HasLang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Boatman extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = "boatman";

    const TYPE_BOATMAN = 1;
    const TYPE_ASSISTANT = 2;
    const TYPE_INSTRUCTOR = 3;
    const TYPE_DIVEMASTER = 4;
    const TYPE_GUIDE = 5;

    const FILEABLE_IC = 'ic_no';
    const FILEABLE_MATE_CARD = 'mate_card';
    const FILEABLE_SEAMAN_CARD = 'seaman_card';


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

    public function fileable(): MorphMany
    {
        return $this->morphMany(Fileable::class, 'fileable');
    }
}
