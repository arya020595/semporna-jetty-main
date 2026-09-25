# External Partner API — Destinations, Activities, Nationalities, Boats & Boatmen

Read-only endpoints for external partner systems (currently the Semporna Jetty Resort Manifest Form). Implementation: `app/Http/Controllers/Api/External/{Destination,Activity,Nationality,Boat,Boatman}Controller.php`, secured by `app/Http/Middleware/VerifyExternalApiToken.php`.

## Browsing the docs

With the app running, open **`http://localhost:8000/docs/external-api`** for a
live, interactive Swagger UI (browse endpoints, expand schemas, "Try it out").
See [`SWAGGER.md`](SWAGGER.md) for how that page is wired up.

`openapi.yaml` can also be pasted into [editor.swagger.io](https://editor.swagger.io)
(or any OpenAPI viewer) if you want to browse it without the app running.

## Files in this folder

- **`openapi.yaml`** — OpenAPI 3.0 contract: all endpoints, request/response shapes, auth scheme, and example payloads. This is the source of truth — the Swagger UI page above just renders this file.
- **`postman_collection.json`** — Postman Collection v2.1 with every request pre-built, saved 200/401 example responses, and a couple of assertion tests per request so "Run Collection" gives a pass/fail result.
- **`SWAGGER.md`** — architecture notes for the `/docs/external-api` Swagger UI page.
- **[`BOATS_FRONTEND_GUIDE.md`](BOATS_FRONTEND_GUIDE.md)** — practical frontend integration guide for `/boats` and `/boatmen`: response → UI field mapping and copy-pasteable implementations for the cascading Boat No. → Boatman/Assistant dropdowns and the boat-agnostic Instructor/Divemaster/Guide dropdowns, for whoever builds the Manifest Form's crew sections.

## How to test against a running app

1. In Postman: **Import** → select `postman_collection.json`.
2. Open the collection's **Variables** tab:
   - `base_url` — defaults to `http://localhost:8000`; change it if your app runs elsewhere.
   - `external_api_token` — leave blank in this file on purpose. Set it to your local `.env`'s `EXTERNAL_API_TOKEN` value (or whatever token the target environment uses). **Never commit a real token into the collection file.**
3. Start the app (`php artisan serve`, or the project's Docker setup) and send any request, or click **Run collection** to execute all of them with their test assertions.

## Notes

- All endpoints return the full active list every time — no pagination, no `?search=` query param, since they back static `<select>` dropdowns rather than a typeahead.
- `/destinations` is pre-filtered server-side to actual tour/dive destinations (`RefDestination::TYPE_DESTINATION`) — departure/jetty points, which live in the same underlying table, are never returned here.
- `/nationalities` has no `type`-style filter — every non-deleted `ref_nationality` row is returned.
- `/boats` returns every boat across **all** partner companies (not scoped by company — the shared bearer token has no per-partner identity today) with its **boat-assigned** crew nested inline (`boatman[]`, only ever types 1/2 — Boatman/Assistant). `/boatmen` returns every crew record of **all 5 types**, flat, each with its own `company` — this is the only source for Instructor/Divemaster/Guide (types 3-5), since those aren't tied to any boat (`boat_id: null`) and so never appear under `/boats`. See [`BOATS_FRONTEND_GUIDE.md`](BOATS_FRONTEND_GUIDE.md) for both dropdown implementations.
- Auth is a static bearer token (`Authorization: Bearer <token>`), unrelated to this app's own Sanctum-based mobile API — see `config/external_api.php` for where the expected token is configured.
