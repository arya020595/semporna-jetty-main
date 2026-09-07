<?php

namespace App\Models;

use App\Models\Concerns\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Fileable extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "fileable";

    protected $guarded = ["id", "created_at", "updated_at", "deleted_at"];

    protected $hidden = ["file"];

    protected $appends = ["file_url"];

    public function referenceTable(): MorphTo
    {
        return $this->morphTo('fileable');
    }

    public static function prepareForDB($requestFile, $path, $fileName = null)
    {
        // $fullpath = storage_path($path);

        $fileName ??= date("YmdHis") . "_" . $requestFile->getClientOriginalName();
        $extension = $requestFile->extension();

        $requestFile->storeAs($path, $fileName . "." . $extension);

        return [
            "file_name" => $fileName,
            "file_type" => $requestFile->getClientMimeType(),
            "file_size" => $requestFile->getSize(),
            "file" => $path . "/" . $fileName . "." . $extension
        ];
    }

    public function getFileUrlAttribute()
    {
        return route('resources.fileable.show', [
            'fileable' => $this->id,
            'access_key' => $this->access_key
        ]);
    }
}
