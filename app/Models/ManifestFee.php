<?php

namespace App\Models;

use App\Models\Concerns\HasLang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManifestFee extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = "manifest_fee";

    const CONFIG_KEY_MANFIEST_FEE = "manifest_fee";

    const TYPE_PRIMARY = 1;
    const TYPE_ADDITIONAL = 2;

    const STATUS_PAID = 1;
    const STATUS_FAILED = -1;
    const STATUS_PENDING = 0;

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
}
