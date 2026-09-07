<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefDestination extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = "ref_destination";

    const TYPE_DESTINATION = 1;
    const TYPE_DEPARTURE = 2;

    const CODE_SEMPORNA = 'DPTR_00001';
    const CODE_SEAFEST = 'DPTR_00002';

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

    public function manifest(): HasMany
    {
        return $this->hasMany(Manifest::class, "manifest_id");
    }
}
