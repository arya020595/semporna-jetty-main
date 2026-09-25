# Production Deployment Guide (GHCR)

How this Laravel app deploys to production via GitHub Container Registry
(GHCR) and GitHub Actions. This is a **production** doc — for local Docker
dev setup, see [`SETUP.md`](SETUP.md) instead; the two setups are
deliberately different (dev bind-mounts source and runs an empty-password
MySQL, production bakes code into the image and runs everything behind
real credentials).

## Table of Contents

- [Architecture](#architecture)
- [One-Time Server Setup](#one-time-server-setup)
- [GitHub Setup](#github-setup)
- [First Deployment](#first-deployment)
- [Day-to-Day Deploys](#day-to-day-deploys)
- [Rollback](#rollback)
- [Troubleshooting](#troubleshooting)

---

## Architecture

```
push to main
     │
     ▼
cd-build.yml
 ├── strip easycode/autopull (unreachable from CI, see CLAUDE.md)
 ├── build Dockerfile.production
 └── push ghcr.io/arya020595/semporna-jetty:latest, :main-<sha>
     │
     ▼ (workflow_run, on success)
cd-deploy.yml
 ├── skip if main has since moved on (stale-build guard)
 ├── scp docker-compose.prod.yml + nginx conf to the server
 ├── ssh: docker login (temp credential, deleted after use) → pull → up -d
 ├── ssh: php artisan migrate --force
 ├── ssh: curl-loop /up until healthy
 └── Slack notification (success or failure)
     │
     ▼
production server (<PRODUCTION_HOST — not yet provisioned>:/opt/st_semporna_jetty)
 ├── nginx        — serves public/ via a shared volume (see below), proxies PHP to app:9000
 ├── app          — php-fpm, code baked into the image, no bind mount
 ├── db           — MySQL 8, real credentials, healthchecked
 ├── queue        — `artisan queue:work`, runs as www-data
 └── scheduler    — `artisan schedule:run` loop, runs as www-data
```

`ci.yml` (tests) is unrelated to this pipeline and unaffected by it.

**A real production server does not exist yet.** `217.217.252.45:/opt/st_semporna_jetty` — referenced throughout this doc as "the production server" below — is actually the **staging** server; see [`STAGING_DEPLOYMENT_GUIDE.md`](STAGING_DEPLOYMENT_GUIDE.md) for the pipeline that actually targets it today (`develop` → `cd-build-staging.yml` → `cd-deploy-staging.yml`, distinct tags/secrets/concurrency group). Everything below describes the intended production pipeline (triggered from `main`) for whenever a separate production host is provisioned — replace `<PRODUCTION_HOST — not yet provisioned>` with its real IP/hostname at that point.

**Why nginx needs a shared volume:** in local dev, `docker-compose.yml`
bind-mounts the whole repo into both `app` and `nginx`, so nginx can read
`public/` directly. In production, `app`'s image has the code baked in —
nginx has no other way to see `public/`. `app`'s
[`docker/entrypoint-prod.sh`](docker/entrypoint-prod.sh) copies `public/`
into a `public_shared` named volume on every container start; `nginx`
mounts that same volume read-only. `docker-compose/nginx/default.conf`
itself needed no changes for this.

## One-Time Server Setup

### 1. Install Docker

```bash
ssh root@<PRODUCTION_HOST — not yet provisioned>
curl -fsSL https://get.docker.com | sh
docker compose version   # verify the compose plugin is present
```

### 2. Create the deploy directory

```bash
mkdir -p /opt/st_semporna_jetty
```

### 3. Generate a dedicated deploy SSH key (on your local machine, not the server)

```bash
ssh-keygen -t ed25519 -f ~/.ssh/semporna_jetty_deploy -C "cd-deploy@semporna-jetty" -N ""
cat ~/.ssh/semporna_jetty_deploy.pub
```

Use a **dedicated** key for this, not a personal one. Append the public key
to the server:

```bash
# on the server
nano ~/.ssh/authorized_keys   # paste the public key, save
chmod 600 ~/.ssh/authorized_keys
chmod 700 ~/.ssh
```

Confirm it works: `ssh -i ~/.ssh/semporna_jetty_deploy root@<PRODUCTION_HOST — not yet provisioned>`.

### 4. Create the production `.env` on the server

`/opt/st_semporna_jetty/.env` — **never commit this file**. It's read both
by Laravel (via `env_file:` in `docker-compose.prod.yml`) and by Docker
Compose itself (for `${DB_PASSWORD}`-style interpolation), since Compose
automatically loads a co-located `.env`.

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://<PRODUCTION_HOST — not yet provisioned>:8080
APP_KEY=                          # see below

DB_HOST=db
DB_DATABASE=survey
DB_USERNAME=<real-non-root-user>
DB_PASSWORD=<real-password>
DB_ROOT_PASSWORD=<real-root-password>   # new — used by docker-compose.prod.yml's db service only

CACHE_DRIVER=file
QUEUE_CONNECTION=database
SESSION_DRIVER=database

EXTERNAL_API_TOKEN=<rotate away from the .env.example placeholder>
# ...plus real SenangPay / OneSignal / reCAPTCHA keys as applicable

IMAGE_TAG=latest
PORT=8080
```

Generate `APP_KEY` from a throwaway local run (don't run `key:generate`
against the server's real `.env` in place unless you script it carefully):

```bash
docker run --rm -v "$PWD":/app -w /app php:7.4-cli php artisan key:generate --show
```

### 5. Database starts empty

The production database is seeded fresh via `migrate --force` on first
deploy — there is no existing dataset to restore onto this server.

## GitHub Setup

### Repository secrets

Settings → Secrets and variables → Actions → New repository secret:

| Secret               | Value                                              |
| --------------------- | -------------------------------------------------- |
| `PRODUCTION_HOST`     | `<PRODUCTION_HOST — not yet provisioned>`                                    |
| `PRODUCTION_USER`     | `root`                                              |
| `PRODUCTION_SSH_KEY`  | contents of `~/.ssh/semporna_jetty_deploy` (private key, full `-----BEGIN...-----END-----` block) |
| `SLACK_WEBHOOK_URL`   | your Slack incoming webhook URL                     |

`GITHUB_TOKEN` needs no secret — Actions injects it automatically per job,
and it's what both the GHCR push (`cd-build.yml`) and the GHCR pull on the
server (`cd-deploy.yml`, piped over SSH) use. It's scoped by each
workflow's own `permissions:` block and expires when the job ends.

**On GHCR package visibility:** a package pushed via `GITHUB_TOKEN` from a
workflow in this repo is automatically linked to the repo, and other
workflows here inherit read access through that link (as long as they
declare `packages: read`, which `cd-deploy.yml` does) — you should **not**
need to manually flip the package to public. If `docker compose pull` on
the server fails after `docker login` reports success, check the package's
own Actions-access settings as a fallback, not visibility first.

### Workflow files

Already in the repo:

- `.github/workflows/ci.yml` — tests, unrelated to deploy.
- `.github/workflows/cd-build.yml` — build & push to GHCR.
- `.github/workflows/cd-deploy.yml` — SSH deploy.

## First Deployment

1. Merge the branch containing `Dockerfile.production`, `docker-compose.prod.yml`,
   `docker/entrypoint-prod.sh`, `.dockerignore`, and the two `cd-*.yml`
   workflows into `main`.
2. `cd-build.yml` runs automatically on that push — watch it in the Actions
   tab, confirm it pushes to `ghcr.io/arya020595/semporna-jetty`.
3. Rather than trusting the `workflow_run` chain on the very first run,
   trigger `cd-deploy.yml` manually: Actions → CD - Deploy →
   Run workflow → leave `image_tag` blank → Run.
4. Watch the run. On success, verify directly:
   ```bash
   ssh root@<PRODUCTION_HOST — not yet provisioned>
   cd /opt/st_semporna_jetty
   docker compose -f docker-compose.prod.yml ps      # 5 services, healthy
   curl -i http://localhost:8080/up                  # 200/204
   curl -i http://localhost:8080/                     # real page, confirms nginx can see public/
   ```
5. Confirm the Slack channel received the deploy notification.

**App URL:** `http://<PRODUCTION_HOST — not yet provisioned>:8080`

## Day-to-Day Deploys

Just merge to `main`. `cd-build.yml` → `cd-deploy.yml` run automatically in
sequence. Watch Slack for the result; check the Actions tab if it fails.

## Rollback

Run `cd-deploy.yml` manually with `image_tag` set to an older build, e.g.
`main-a1b2c3d` (find the exact tag in a previous `cd-build.yml` run's logs,
or in the GHCR package's version list). **Don't** try to roll back by
editing `IMAGE_TAG` in the server's `.env` — the workflow always computes
its own tag unless you explicitly override it via this input, so an
`.env` edit would just get ignored on the next normal deploy.

## Troubleshooting

**`docker compose pull` fails on the server despite `docker login` succeeding**
Check the GHCR package's Actions-access settings before touching
visibility — see the note under [GitHub Setup](#github-setup).

**Health check fails after a deploy**
```bash
ssh root@<PRODUCTION_HOST — not yet provisioned>
cd /opt/st_semporna_jetty
docker compose -f docker-compose.prod.yml logs --tail=100 app
docker compose -f docker-compose.prod.yml ps
```

**Migration fails**
```bash
docker compose -f docker-compose.prod.yml exec app php artisan migrate:status
docker compose -f docker-compose.prod.yml exec app php artisan db:show
```

**Queue or scheduler not running**
```bash
docker compose -f docker-compose.prod.yml logs queue
docker compose -f docker-compose.prod.yml logs scheduler
```
Both run as `www-data`, not root — if storage permissions look wrong,
check `chown -R www-data:www-data storage bootstrap/cache` in
`Dockerfile.production` ran as expected in the image.

**A deploy seems to have redeployed older code**
Check the run's "Guard against deploying a superseded commit" step in
`cd-deploy.yml` — it should have skipped instead. If it didn't skip and
older code shipped anyway, that's a bug in the guard, not something to
work around by hand — re-run `cd-deploy.yml` with no `image_tag` to
redeploy current `main`.

## Known Gaps (intentionally out of scope for now)

- **No TLS.** Serves plain HTTP on port 8080; add a reverse proxy +
  Certbot (or similar) once a domain exists.
- **Not zero-downtime.** Containers are recreated before migrations run;
  fine for a low-traffic single-target deploy, not blue/green safe.
- **`public_shared` volume grows slowly over time** — old hashed asset
  files from previous deploys aren't pruned (cosmetic disk usage only).
