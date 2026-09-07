<?php

namespace App\Models;

use App\Models\Concerns\HasLang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefNationality extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = "ref_nationality";

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
}
