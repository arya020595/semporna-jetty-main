# CLAUDE.md

Instructions for AI assistants working in this repo. See [`README.md`](README.md)
and [`SETUP.md`](SETUP.md) for stack/setup details — not repeated here.

## Local Docker dev quirk

The app runs via `docker compose` (`crims-app` = PHP-FPM, `crims-nginx`,
`crims-mysql`, port 8000). The container's bind mount does **not** reliably
reflect edits made in this workspace in real time — a file edited here can
have a different inode than the same path inside `crims-app`, so the running
app keeps serving the old version.

After editing any PHP, Blade, or route file, sync it into the container and
clear caches before testing against `http://localhost:8000`:

```bash
docker cp <path> crims-app:/var/www/<path>
docker exec crims-app php artisan view:clear
docker exec crims-app php artisan route:clear
```

Verify with `curl`, not just by editing the file — a stale container copy
will silently keep returning old behavior (or a 404) otherwise.

## External Partner API — keep the OpenAPI spec and Swagger UI in sync

`routes/api.php`'s `external` route group (`/api/external/destinations`,
`/api/external/activities`, guarded by the `external.token` middleware) is
documented by a hand-maintained OpenAPI spec at
[`docs/external-api/openapi.yaml`](docs/external-api/openapi.yaml), served
live as an interactive Swagger UI page at `/docs/external-api`
(`app/Http/Controllers/Docs/ExternalApiDocsController.php`).

**Whenever you add, remove, or change an endpoint in that route group** —
new field, new endpoint, changed auth, changed response shape — update
`docs/external-api/openapi.yaml` (and `docs/external-api/postman_collection.json`
if it's affected) in the same change. Don't let the spec drift from the real
routes; it's the only thing partners have to go on.

CI (`.github/workflows/openapi-lint.yml`) lints `openapi.yaml` with Redocly
and checks the Postman collection is valid JSON on any PR touching those
files — it only catches spec-validity/syntax issues, not drift from the
actual routes, so the rule above still relies on you.

- Architecture of the Swagger UI wiring: [`docs/external-api/SWAGGER.md`](docs/external-api/SWAGGER.md)
- Adding the same pattern for a *different* API (e.g. if the app's own
  Sanctum-based mobile API ever gets a spec): [`docs/HOW-TO-ADD-SWAGGER-UI.md`](docs/HOW-TO-ADD-SWAGGER-UI.md)

## CI: `easycode/autopull` is stripped from the CI checkout — by design

`composer.json` requires `easycode/autopull` from a private GitLab repo over
SSH (`gitlab.com/easycode.id/framework/autodeploy`), which GitHub Actions
cannot reach. `.github/workflows/laravel-tests.yml` removes it from the
ephemeral CI checkout's `composer.json`/`composer.lock` before `composer
install` — the same workaround [`SETUP.md`](SETUP.md) already documents for
contributors without GitLab access ("just an auto-deploy helper, not core
app logic"). This is never committed back to the repo; don't mistake a CI
failure here for the package being genuinely broken.

If a change ever makes the app depend on `easycode/autopull` at boot/runtime
(rather than just being an optional auto-deploy hook), that assumption
breaks — `migrate` and the full test suite were verified to pass without it
before this workflow was added, but re-verify if that provider's role changes.
