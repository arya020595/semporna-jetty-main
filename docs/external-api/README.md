# External Partner API — Destinations, Activities & Nationalities

Read-only reference-data endpoints for external partner systems (currently the Semporna Jetty Resort Manifest Form). Implementation: `app/Http/Controllers/Api/External/{Destination,Activity,Nationality}Controller.php`, secured by `app/Http/Middleware/VerifyExternalApiToken.php`.

## Browsing the docs

With the app running, open **`http://localhost:8000/docs/external-api`** for a
live, interactive Swagger UI (browse endpoints, expand schemas, "Try it out").
See [`SWAGGER.md`](SWAGGER.md) for how that page is wired up.

`openapi.yaml` can also be pasted into [editor.swagger.io](https://editor.swagger.io)
(or any OpenAPI viewer) if you want to browse it without the app running.

## Files in this folder

- **`openapi.yaml`** — OpenAPI 3.0 contract: both endpoints, request/response shapes, auth scheme, and example payloads. This is the source of truth — the Swagger UI page above just renders this file.
- **`postman_collection.json`** — Postman Collection v2.1 with both requests pre-built, saved 200/401 example responses, and a couple of assertion tests per request so "Run Collection" gives a pass/fail result.
- **`SWAGGER.md`** — architecture notes for the `/docs/external-api` Swagger UI page.

## How to test against a running app

1. In Postman: **Import** → select `postman_collection.json`.
2. Open the collection's **Variables** tab:
   - `base_url` — defaults to `http://localhost:8000`; change it if your app runs elsewhere.
   - `external_api_token` — leave blank in this file on purpose. Set it to your local `.env`'s `EXTERNAL_API_TOKEN` value (or whatever token the target environment uses). **Never commit a real token into the collection file.**
3. Start the app (`php artisan serve`, or the project's Docker setup) and send either request, or click **Run collection** to execute both with their test assertions.

## Notes

- All endpoints return the full active list every time — no pagination, no `?search=` query param, since they back static `<select>` dropdowns rather than a typeahead.
- `/destinations` is pre-filtered server-side to actual tour/dive destinations (`RefDestination::TYPE_DESTINATION`) — departure/jetty points, which live in the same underlying table, are never returned here.
- `/nationalities` has no `type`-style filter — every non-deleted `ref_nationality` row is returned.
- Auth is a static bearer token (`Authorization: Bearer <token>`), unrelated to this app's own Sanctum-based mobile API — see `config/external_api.php` for where the expected token is configured.
