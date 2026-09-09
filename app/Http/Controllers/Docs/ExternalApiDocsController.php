<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExternalApiDocsController extends Controller
{
    /**
     * Show the Swagger UI page for the External Partner API.
     */
    public function index()
    {
        return view('docs.swagger', [
            'title' => 'External Partner API — Docs',
            'specUrl' => route('docs.external-api.spec'),
        ]);
    }

    /**
     * Serve the raw OpenAPI contract backing the Swagger UI page.
     */
    public function spec(): BinaryFileResponse
    {
        return response()->file(base_path('docs/external-api/openapi.yaml'), [
            'Content-Type' => 'application/yaml',
        ]);
    }
}
