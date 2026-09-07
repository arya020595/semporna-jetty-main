<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Models\Fileable;
use Illuminate\Http\Request;

class FileableController extends Controller
{
    public function show(Request $request, Fileable $fileable)
    {
        if (
            !$request->access_key
            || $fileable->access_key != $request->access_key
        ) {
            abort(404);
        }

        $fullPath = storage_path('app/' . $fileable->file);

        return response()->file($fullPath);
    }
}
