# How to Add a Swagger UI Page for an API in This App

This is the reusable recipe behind `/docs/external-api` (see
[`external-api/SWAGGER.md`](external-api/SWAGGER.md) for that concrete example).
Follow it whenever a new API in this app gets an OpenAPI spec and needs a
browsable, interactive docs page.

## Prerequisite: you already have an `openapi.yaml`

This guide wires up an *existing* OpenAPI 3.0 spec file — it doesn't generate
one from code annotations. Write the spec by hand (or with AI help) first,
following the shape of [`external-api/openapi.yaml`](external-api/openapi.yaml)
as a template: `info`, `servers`, `security`, `paths`, `components.schemas`.

Put it at `docs/<your-api-name>/openapi.yaml`.

## Step 1 — Add a controller

Create `app/Http/Controllers/Docs/<Name>DocsController.php`:

```php
<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class <Name>DocsController extends Controller
{
    public function index()
    {
        return view('docs.swagger', [
            'title' => '<Your API Title> — Docs',
            'specUrl' => route('docs.<your-api-name>.spec'),
        ]);
    }

    public function spec(): BinaryFileResponse
    {
        return response()->file(base_path('docs/<your-api-name>/openapi.yaml'), [
            'Content-Type' => 'application/yaml',
        ]);
    }
}
```

**Gotcha:** `spec()` must be type-hinted `BinaryFileResponse`
(`Symfony\Component\HttpFoundation\BinaryFileResponse`), not
`Illuminate\Http\Response` — `response()->file()` returns the former, and a
narrower type-hint throws a `TypeError` (500) at request time.

## Step 2 — Register routes

In `routes/web.php`, add near the other `docs.*` group:

```php
use App\Http\Controllers\Docs\<Name>DocsController;

Route::group(['prefix' => 'docs/<your-api-name>', 'as' => 'docs.<your-api-name>.'], function () {
    Route::get('/', [<Name>DocsController::class, 'index'])->name('index');
    Route::get('/openapi.yaml', [<Name>DocsController::class, 'spec'])->name('spec');
});
```

Use a real controller — **not** a closure. Every route in this file is
controller-based, and closures can't be cached by `php artisan route:cache`.

## Step 3 — Reuse the existing Swagger UI view

Don't create a new Blade view. `resources/views/docs/swagger.blade.php`
already takes `title` and `specUrl` as parameters and is generic — the
`index()` method above just needs to pass those two values.

If you ever need to bump the Swagger UI version, that CDN URL (currently
pinned to `swagger-ui-dist@5.32.15`) lives in that one file — check
`https://registry.npmjs.org/swagger-ui-dist/latest` for the current version
before bumping, and pin the exact version (not a floating major).

## Step 4 — Deploy and verify

This app's local Docker setup has a quirk where file edits on this side
don't automatically reach the running container — see "Local Docker dev
quirk" in the root [`CLAUDE.md`](../CLAUDE.md). After adding these files,
copy them into the container and clear caches before testing:

```bash
docker cp routes/web.php crims-app:/var/www/routes/web.php
docker cp app/Http/Controllers/Docs/<Name>DocsController.php crims-app:/var/www/app/Http/Controllers/Docs/<Name>DocsController.php
docker exec crims-app php artisan view:clear
docker exec crims-app php artisan route:clear
```

Then confirm both routes:

```bash
curl -s -o /dev/null -w "UI:   %{http_code}\n" http://localhost:8000/docs/<your-api-name>
curl -s -o /dev/null -w "YAML: %{http_code}\n" http://localhost:8000/docs/<your-api-name>/openapi.yaml
```

Both should return `200`.

## Step 5 (optional) — Restrict access

If the API being documented is more sensitive than the External Partner API
(e.g. it exposes the app's own internal endpoints), consider gating the
`index()` route behind the `auth` middleware or an environment check, rather
than leaving it open like `/docs/external-api` currently is.
