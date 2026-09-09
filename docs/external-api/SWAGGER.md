# Swagger UI for the External Partner API — What We Built

This document explains the architecture behind the live, interactive API docs at
`/docs/external-api`. For the API contract itself, see [`openapi.yaml`](openapi.yaml)
and [`README.md`](README.md).

## Why this exists

`openapi.yaml` was previously a standalone file — useful, but you had to paste it
into [editor.swagger.io](https://editor.swagger.io) by hand to browse it or try a
request. This wires the same file into the running app as a real page, so partners
and developers can open one URL and get interactive docs (browse endpoints, expand
schemas, click "Try it out").

## Request flow

```
Browser
  │
  ├─ GET /docs/external-api
  │     → ExternalApiDocsController@index
  │     → view('docs.swagger', ['specUrl' => route('docs.external-api.spec')])
  │     → returns an HTML page that loads Swagger UI (JS/CSS) from a CDN
  │
  └─ (page load, from inside Swagger UI's JS)
        GET /docs/external-api/openapi.yaml
          → ExternalApiDocsController@spec
          → response()->file(docs/external-api/openapi.yaml)
          → Swagger UI parses this and renders the interactive page

  (separately — this is the real API Swagger UI's "Try it out" calls)
        GET /api/external/destinations
        GET /api/external/activities
          → routes/api.php's `external` group
          → guarded by the `external.token` middleware (static bearer token)
```

Two routes, one controller, one shared Blade view. No new API surface — the
`spec()` route just streams the existing `docs/external-api/openapi.yaml` file
that already lived in the repo, so there is exactly one source of truth for the
contract.

## Files

| File | Role |
|---|---|
| [`routes/web.php`](../../routes/web.php) | Registers `GET /docs/external-api` and `GET /docs/external-api/openapi.yaml`, grouped under `prefix => 'docs/external-api'`. |
| [`app/Http/Controllers/Docs/ExternalApiDocsController.php`](../../app/Http/Controllers/Docs/ExternalApiDocsController.php) | `index()` renders the Swagger UI page; `spec()` streams the yaml file. |
| [`resources/views/docs/swagger.blade.php`](../../resources/views/docs/swagger.blade.php) | Generic Swagger UI shell — takes `title` and `specUrl` as view data, so it isn't tied to this one API. |
| [`docs/external-api/openapi.yaml`](openapi.yaml) | The actual contract (unchanged by this work) — remains the single source of truth. |

## Design decisions

- **Controller, not route closures.** The routes call controller methods rather
  than inline closures. This matches every other route in `routes/web.php` (there
  are zero closures anywhere else in `routes/`) and, more importantly, closures
  cannot be serialized by `php artisan route:cache` — a controller-based route can be.
- **`spec()` returns `BinaryFileResponse`, not `Illuminate\Http\Response`.**
  `response()->file(...)` returns `Symfony\Component\HttpFoundation\BinaryFileResponse`.
  Type-hinting the narrower `Illuminate\Http\Response` throws a `TypeError` at
  request time — hit this exact 500 during development, so it's called out here
  to save the next person the same trip through the logs.
- **Content-Type: `application/yaml`.** The IANA-registered MIME type (not the
  older unofficial `text/yaml`).
- **Swagger UI is loaded from a CDN, version-pinned.** `swagger-ui-dist@5.32.15`
  (exact version, not a floating `@5`) — matches this app's existing convention
  of pinning exact CDN versions (e.g. `cdn.datatables.net/1.13.2` in
  `resources/views/app.blade.php`). No new composer/npm dependency was needed.
- **The spec file is served straight from `docs/`, not copied into `public/`.**
  Avoids having two copies of the contract that can drift out of sync.

## CI

[`.github/workflows/openapi-lint.yml`](../../.github/workflows/openapi-lint.yml)
runs on any push/PR touching `docs/external-api/openapi.yaml`,
`docs/external-api/postman_collection.json`, or `redocly.yaml`:

- Lints `openapi.yaml` with [Redocly CLI](https://redocly.com/docs/cli/) against
  the `recommended` ruleset (config in [`redocly.yaml`](../../redocly.yaml) at
  the repo root — two rules are intentionally disabled there: `info-license`
  since this isn't a publicly published API, and `no-server-example.com` since
  the only server is deliberately `localhost` for local dev).
- Checks `postman_collection.json` parses as valid JSON.

This catches spec-syntax mistakes (e.g. the `nullable` field needing a
sibling `type` in OpenAPI 3.0 — a real bug this lint found in the original
spec, fixed alongside adding the workflow). It does **not** catch the spec
drifting out of sync with the actual Laravel routes — that's a manual
discipline documented in the root [`CLAUDE.md`](../../CLAUDE.md).

## Known limitations / things to revisit

- **`/docs/external-api` is currently unauthenticated**, same as the raw yaml
  file already sitting in the repo. If this needs to be restricted (e.g. to
  `local`/`staging` only, or behind the app's `auth` middleware), that's a small
  addition to the route group in `routes/web.php` — not done here because it
  wasn't asked for and the underlying reference data isn't sensitive.
- **This only covers the External Partner API.** The app's own mobile API
  (Sanctum-based, `routes/api.php`'s non-`external` routes) has no OpenAPI spec
  or Swagger UI yet. See [`../HOW-TO-ADD-SWAGGER-UI.md`](../HOW-TO-ADD-SWAGGER-UI.md)
  if that's ever wanted.
- **This app's Docker setup has a quirk** that affects anyone editing these
  files: see the "Local Docker dev quirk" section in the root
  [`CLAUDE.md`](../../CLAUDE.md).
