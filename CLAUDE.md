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
cannot reach. `.github/workflows/ci.yml` removes it from the
ephemeral CI checkout's `composer.json`/`composer.lock` before `composer
install` — the same workaround [`SETUP.md`](SETUP.md) already documents for
contributors without GitLab access ("just an auto-deploy helper, not core
app logic"). This is never committed back to the repo; don't mistake a CI
failure here for the package being genuinely broken.

If a change ever makes the app depend on `easycode/autopull` at boot/runtime
(rather than just being an optional auto-deploy hook), that assumption
breaks — `migrate` and the full test suite were verified to pass without it
before this workflow was added, but re-verify if that provider's role changes.

## Production deploy: `cd-build.yml` + `cd-deploy.yml`

Two workflows handle production deploys (separate from `ci.yml`, which only
tests) to a Docker/GHCR-based server — unrelated to the legacy
`easycode/autopull`/PM2 mechanism described above, which this does not use.

- `.github/workflows/cd-build.yml` — on push to `main` (or manually), builds
  `Dockerfile.production` and pushes to `ghcr.io/arya020595/semporna-jetty`
  (tags: `latest`, `main-<short-sha>`). Strips `easycode/autopull` the same
  way `ci.yml` does, before `docker build` — same rationale, same "never
  committed back" caveat.
- `.github/workflows/cd-deploy.yml` — triggered by `cd-build.yml` completing
  successfully on `main`, or manually via `workflow_dispatch`. SSHes into
  the production server (`docker-compose.prod.yml`, `/opt/st_semporna_jetty`),
  pulls the matching image tag, runs migrations, health-checks `/up`.
  **Rollback** is a manual run with the `image_tag` input set to an older
  `main-<sha>` — don't try to roll back by editing anything on the server.
  A guard step skips the run entirely if `main` has moved on since the
  build it would deploy, so an out-of-order/late-finishing build can't
  clobber a newer deploy that already shipped.

Production topology (`docker-compose.prod.yml`, not used in local dev —
that's still plain `docker-compose.yml`): `app` (php-fpm, image-only, no
bind mount), `nginx`, `db` (real credentials, healthchecked), `queue`
(`artisan queue:work`, runs as `www-data`), `scheduler` (`artisan
schedule:run` loop, runs as `www-data`) — queue/scheduler are containerized
here, replacing the legacy PM2 (`queue-worker.yml`)/host-cron approach for
this deployment.

nginx has no direct filesystem access to `public/` in production (code
ships baked into the `app` image, no bind mount) — a `public_shared` named
volume plus `app`'s `docker/entrypoint-prod.sh` (copies `public/` into it on
every container start) is what makes `docker-compose/nginx/default.conf`
work unchanged. Don't remove that volume/entrypoint thinking it's dead
weight.

The `/up` health route is registered directly in
`RouteServiceProvider::boot()`, deliberately **outside** the `web`/`api`
route groups — `web` pulls in `StartSession` (sessions are
`SESSION_DRIVER=database`) and `VerifyCsrfToken`, which would make a
"is PHP alive" check silently depend on the database. Don't move it into
`routes/web.php` or `routes/api.php`.

## Staging deploy: `cd-build-staging.yml` + `cd-deploy-staging.yml` + `cd-rollback-staging.yml`

A second, fully parallel pair of workflows deploys to the **staging**
server (`217.217.252.45:/opt/st_semporna_jetty`) — the box referenced
above as "production" doesn't actually exist yet; `217.217.252.45` is
staging. Topology, the nginx shared-volume mechanism, `Dockerfile.production`,
and `docker/entrypoint-prod.sh` are all identical to production (see
above) — only the trigger branch, image tags, secrets, and concurrency
group differ:

- Triggers off push to `develop` (not `main`), via `cd-build-staging.yml`
  → `cd-deploy-staging.yml`.
- Image tags are prefixed `staging-` (`staging-latest`, `staging-<sha>`)
  in the same `ghcr.io/arya020595/semporna-jetty` repo — never touching
  `latest`/`main-<sha>`, so a `develop` build can't clobber what a future
  production build owns.
- Deploys via `docker-compose.staging.yml` (a separate file from
  `docker-compose.prod.yml`, not a parameterization of it — same reasoning
  as `docker-compose.yml` vs `docker-compose.prod.yml` already being
  separate files per environment).
- Uses `STAGING_HOST`/`STAGING_USER`/`STAGING_SSH_KEY` secrets (distinct
  from any future `PRODUCTION_*` secrets) and its own concurrency group
  (`cd-deploy-staging`, distinct from `cd-deploy-production`) — a staging
  deploy and a future production deploy can never queue behind or cancel
  each other.
- Health checks use `docker inspect` against `nginx`'s container
  healthcheck (not a bare curl loop from the runner), and each deploy
  appends a line to `/opt/st_semporna_jetty/.deploy_history` — a
  human-readable audit trail for picking a rollback target. Rollback is
  its own workflow, `cd-rollback-staging.yml` (`workflow_dispatch` only,
  explicit `image_tag` required), not an input on `cd-deploy-staging.yml` —
  it shares `cd-deploy-staging.yml`'s concurrency group so the two can
  never race against the server.

Full runbook: [`STAGING_DEPLOYMENT_GUIDE.md`](STAGING_DEPLOYMENT_GUIDE.md).
`PRODUCTION_DEPLOYMENT_GUIDE.md` describes the intended, not-yet-live
production pipeline for whenever a separate host is provisioned — don't
confuse the two, and don't point `PRODUCTION_*` secrets at the staging
box.
