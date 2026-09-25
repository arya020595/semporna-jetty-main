# Staging Deployment Guide (GHCR)

How this Laravel app deploys to the **staging** server (`217.217.252.45`)
via GitHub Container Registry (GHCR) and GitHub Actions. This is the
parallel counterpart to [`PRODUCTION_DEPLOYMENT_GUIDE.md`](PRODUCTION_DEPLOYMENT_GUIDE.md)
— same mechanism, different branch/tags/host/secrets, kept as a fully
separate pipeline so a future real production deploy (triggered from
`main`) can never be affected by staging traffic. For local Docker dev
setup, see [`SETUP.md`](SETUP.md) instead.

## Table of Contents

- [Architecture](#architecture)
- [One-Time Server Setup](#one-time-server-setup)
- [GitHub Setup](#github-setup)
- [First Deployment](#first-deployment)
- [Day-to-Day Deploys](#day-to-day-deploys)
- [Rollback](#rollback)
- [Troubleshooting](#troubleshooting)
- [Security Checklist](#security-checklist)
- [Common Commands](#common-commands)
- [Known Gaps](#known-gaps-intentionally-out-of-scope-for-now)

---

## Architecture

```
push to develop
     │
     ▼
cd-build-staging.yml
 ├── strip easycode/autopull (unreachable from CI, see CLAUDE.md)
 ├── build Dockerfile.production
 └── push ghcr.io/arya020595/semporna-jetty:staging-latest, :staging-<sha>
     │
     ▼ (workflow_run, on success)
cd-deploy-staging.yml
 ├── Slack: deployment started (block-kit)
 ├── skip everything below if develop has since moved on (stale-build guard)
 ├── scp docker-compose.staging.yml + nginx conf to the server
 ├── ssh: mv docker-compose.staging.yml → docker-compose.yml (so ad-hoc
 │        commands on the server are just `docker compose ...`, no `-f`)
 ├── ssh: docker login (temp credential, deleted after use) → pull → up -d
 ├── ssh: php artisan migrate --force
 ├── ssh: poll `docker inspect` health status on nginx, then a final curl sanity check
 ├── ssh: append a line to .deploy_history (for cd-rollback-staging.yml's operator to pick a target from)
 └── Slack: deployment result, prefixed [STAGING] (block-kit, success or failure)
     │
     ▼
staging server (217.217.252.45:/opt/st_semporna_jetty)
 ├── nginx        — serves public/ via a shared volume (see below), proxies PHP to app:9000
 ├── app          — php-fpm, code baked into the image, no bind mount
 ├── db           — MySQL 8, real credentials, healthchecked
 ├── queue        — `artisan queue:work`, runs as www-data
 └── scheduler    — `artisan schedule:run` loop, runs as www-data
```

`ci.yml` (tests) is unrelated to this pipeline and unaffected by it.

**Isolation from the production pipeline:** `cd-build-staging.yml` and
`cd-deploy-staging.yml` are separate workflow files with distinct
`workflow_run` names, so they can never cross-trigger `cd-build.yml`/
`cd-deploy.yml` (which stay wired to `main`, for a production host that
doesn't exist yet). They also use a distinct concurrency group
(`cd-deploy-staging` vs. `cd-deploy-production`), so a staging deploy and
a future production deploy can never queue behind or cancel each other.
Image tags are prefixed `staging-` so a `develop` build can never
overwrite the `latest`/`main-<sha>` tags a production build would own.

**Why nginx needs a shared volume:** in local dev, `docker-compose.yml`
bind-mounts the whole repo into both `app` and `nginx`, so nginx can read
`public/` directly. Here, `app`'s image has the code baked in — nginx has
no other way to see `public/`. `app`'s
[`docker/entrypoint-prod.sh`](docker/entrypoint-prod.sh) copies `public/`
into a `public_shared` named volume on every container start; `nginx`
mounts that same volume read-only. `docker-compose/nginx/default.conf`
itself needed no changes for this — it's environment-agnostic.

## One-Time Server Setup

The server already exists and already has Docker installed and a
`.env`-style config file at `/opt/st_semporna_jetty/.env` — there is no
`docker-compose.staging.yml`, no `docker-compose/nginx/` config, and
nothing deployed there yet. So this section is a **verification** pass,
not a fresh install.

### 1. Verify Docker

```bash
ssh root@217.217.252.45
docker --version
docker compose version   # verify the compose plugin is present
```

### 2. Confirm the deploy directory

```bash
ls -la /opt/st_semporna_jetty   # should already exist
```

### 3. Generate a dedicated deploy SSH key (on your local machine, not the server)

```bash
ssh-keygen -t ed25519 -f ~/.ssh/semporna_jetty_staging_deploy -C "cd-deploy-staging@semporna-jetty" -N ""
cat ~/.ssh/semporna_jetty_staging_deploy.pub
```

Use a **dedicated** key for this — distinct from any key eventually used
for production. Append the public key to the server:

```bash
# on the server
nano ~/.ssh/authorized_keys   # paste the public key, save
chmod 600 ~/.ssh/authorized_keys
chmod 700 ~/.ssh
```

Confirm it works: `ssh -i ~/.ssh/semporna_jetty_staging_deploy root@217.217.252.45`.

### 4. Audit the existing `.env` on the server

`/opt/st_semporna_jetty/.env` — **never commit this file**. It's read
both by Laravel (via `env_file:` in `docker-compose.staging.yml`) and by
Docker Compose itself (for `${DB_PASSWORD}`-style interpolation), since
Compose automatically loads a co-located `.env`.

Since this box may have been drafted under an earlier "this is
production" assumption, explicitly check `APP_ENV` and `IMAGE_TAG` —
they're the two values most likely to be wrong if so:

```env
APP_ENV=staging                  # NOT production
APP_DEBUG=false
APP_URL=http://217.217.252.45:8080
APP_KEY=                          # see below

DB_HOST=db
DB_DATABASE=survey
DB_USERNAME=<real-non-root-user>
DB_PASSWORD=<real-password>
DB_ROOT_PASSWORD=<real-root-password>   # used by docker-compose.staging.yml's db service only

CACHE_DRIVER=file
QUEUE_CONNECTION=database
SESSION_DRIVER=database

EXTERNAL_API_TOKEN=<rotate away from the .env.example placeholder>
# ...plus real SenangPay / OneSignal / reCAPTCHA keys as applicable

IMAGE_TAG=staging-latest         # NOT latest
PORT=8080
```

Generate `APP_KEY` from a throwaway local run (don't run `key:generate`
against the server's real `.env` in place unless you script it carefully):

```bash
docker run --rm -v "$PWD":/app -w /app php:7.4-cli php artisan key:generate --show
```

### 5. Database starts empty

The staging database is seeded fresh via `migrate --force` on first
deploy — there is no existing dataset to restore onto this server.

## GitHub Setup

### Repository secrets

Settings → Secrets and variables → Actions → New repository secret:

| Secret               | Value                                              |
| --------------------- | -------------------------------------------------- |
| `STAGING_HOST`        | `217.217.252.45`                                    |
| `STAGING_USER`        | `root`                                              |
| `STAGING_SSH_KEY`     | contents of `~/.ssh/semporna_jetty_staging_deploy` (private key, full `-----BEGIN...-----END-----` block) |

`SLACK_WEBHOOK_URL` is reused as-is from the production setup (same
Slack channel) — the `[STAGING]` prefix on notification text is what
distinguishes them. Add a dedicated `STAGING_SLACK_WEBHOOK_URL` later if
staging noise should go to a separate channel; not required to stand
this up.

Kept fully separate from any future `PRODUCTION_HOST`/`PRODUCTION_USER`/
`PRODUCTION_SSH_KEY` secrets — `docker-compose.prod.yml` and
`docker-compose.staging.yml` use identical service/volume/network names,
and Compose derives its project name from the `/opt/st_semporna_jetty`
directory rather than the `-f` filename, so sharing secrets between the
two pipelines would let a `main` push's `cd-deploy.yml` collide with
this staging stack. Dedicated secrets avoid that entirely — no need to
disable `cd-build.yml`/`cd-deploy.yml` because of this pipeline.

`GITHUB_TOKEN` needs no secret — Actions injects it automatically per
job, and it's what both the GHCR push (`cd-build-staging.yml`) and the
GHCR pull on the server (`cd-deploy-staging.yml`, piped over SSH) use.
It's scoped by each workflow's own `permissions:` block and expires when
the job ends.

**On GHCR package visibility:** a package pushed via `GITHUB_TOKEN` from
a workflow in this repo is automatically linked to the repo, and other
workflows here inherit read access through that link (as long as they
declare `packages: read`, which `cd-deploy-staging.yml` does) — you
should **not** need to manually flip the package to public. If
`docker compose pull` on the server fails after `docker login` reports
success, check the package's own Actions-access settings as a fallback,
not visibility first.

### Workflow files

Already in the repo:

- `.github/workflows/ci.yml` — tests, unrelated to deploy.
- `.github/workflows/cd-build-staging.yml` — build & push to GHCR, triggered from `develop`.
- `.github/workflows/cd-deploy-staging.yml` — SSH deploy of the tag matching whatever triggered it.
- `.github/workflows/cd-rollback-staging.yml` — manual-only, deploys an explicit older tag (see [Rollback](#rollback)).

## First Deployment

1. Merge the branch containing `docker-compose.staging.yml` and the two
   `cd-*-staging.yml` workflows (plus `Dockerfile.production`,
   `docker/entrypoint-prod.sh`, `.dockerignore`, which are shared with
   production) into `develop`.
2. `cd-build-staging.yml` runs automatically on that push — watch it in
   the Actions tab, confirm it pushes `staging-latest`/`staging-<sha>` to
   `ghcr.io/arya020595/semporna-jetty` (and does **not** touch
   `latest`/`main-<sha>`).
3. Rather than trusting the `workflow_run` chain on the very first run,
   trigger `cd-deploy-staging.yml` manually: Actions → CD - Deploy
   (Staging) → Run workflow. **GitHub's "Run workflow" dropdown defaults
   to the repo's default branch (`main`), not `develop`** — explicitly
   select `develop` from the branch dropdown before running, otherwise
   `github.sha` resolves against the wrong branch's tip and computes a
   `staging-<sha>` tag that was never built.
4. Watch the run. On success, verify directly:
   ```bash
   ssh root@217.217.252.45
   cd /opt/st_semporna_jetty
   docker compose ps      # 5 services, healthy
   curl -i http://localhost:8080/up                     # 200/204
   curl -i http://localhost:8080/                        # real page, confirms nginx can see public/
   docker compose images app  # confirm the running tag is staging-*
   ```
5. From your own machine, confirm the port is reachable externally, not
   just via localhost on the box: `curl -i http://217.217.252.45:8080/up`.
6. Confirm the Slack channel received a `[STAGING]`-prefixed deploy
   notification.

**App URL:** `http://217.217.252.45:8080`

## Day-to-Day Deploys

Just merge to `develop`. `cd-build-staging.yml` → `cd-deploy-staging.yml`
run automatically in sequence. Watch Slack for the `[STAGING]` result;
check the Actions tab if it fails.

## Rollback

Run `cd-rollback-staging.yml` manually: Actions → CD - Rollback (Staging)
→ Run workflow. Inputs:

- `image_tag` (required) — the exact tag to roll back to, e.g.
  `staging-a1b2c3d`. Find it in a previous `cd-build-staging.yml` run's
  logs, the GHCR package's version list, or `/opt/st_semporna_jetty/.deploy_history`
  on the server (one `timestamp|tag|previous_tag` line per past deploy).
- `rollback_migrations` (optional, default `0`) — number of migration
  steps to roll back via `php artisan migrate:rollback --step=N`, run
  *before* the containers are restarted with the older image. Leave at
  `0` if the rollback is purely a code revert with no schema change to
  undo.
- `skip_health_check` (optional, default `false`) — for emergency
  rollbacks where you don't want to wait on the health-check loop.

This workflow shares the `cd-deploy-staging` concurrency group with
`cd-deploy-staging.yml`, so a rollback and a normal deploy can never run
against the server at the same time. It also updates `IMAGE_TAG` in the
server's `.env` to match — unlike the old design, this is now the
source of truth for "what's currently deployed" until the next normal
`develop` push overwrites it again.

## Troubleshooting

**`docker compose pull` fails on the server despite `docker login` succeeding**
Check the GHCR package's Actions-access settings before touching
visibility — see the note under [GitHub Setup](#github-setup).

**Health check fails after a deploy**
```bash
ssh root@217.217.252.45
cd /opt/st_semporna_jetty
docker compose logs --tail=100 app nginx
docker compose ps
docker inspect --format '{{json .State.Health}}' $(docker compose ps -q nginx) | jq
```

**Migration fails**
```bash
docker compose exec app php artisan migrate:status
docker compose exec app php artisan db:show
```

**Queue or scheduler not running**
```bash
docker compose logs queue
docker compose logs scheduler
```
Both run as `www-data`, not root — if storage permissions look wrong,
check `chown -R www-data:www-data storage bootstrap/cache` in
`Dockerfile.production` ran as expected in the image.

**A deploy seems to have redeployed older code**
Check the run's "Guard against deploying a superseded commit" step in
`cd-deploy-staging.yml` — it should have skipped instead. If it didn't
skip and older code shipped anyway, that's a bug in the guard, not
something to work around by hand — re-run `cd-deploy-staging.yml` (branch
`develop`, no `image_tag`) to redeploy current `develop`.

**A `develop` push didn't trigger a staging deploy, or a `main` push
triggered one by mistake**
Check the exact `name:` fields in `cd-build-staging.yml`/
`cd-deploy-staging.yml` haven't drifted from what `workflow_run` expects
(`"CD - Build & Push (Staging)"`), and that `cd-deploy-staging.yml`'s
`if:` condition still checks `head_branch == 'develop'`.

## Security Checklist

- [ ] `DB_PASSWORD` / `DB_ROOT_PASSWORD` in the server's `.env` are strong,
      not left as defaults or empty (unlike local dev's empty-password MySQL).
- [ ] `EXTERNAL_API_TOKEN` is a freshly generated value
      (`openssl rand -hex 32`), not the `.env.example` placeholder.
- [ ] `APP_DEBUG=false` and `APP_ENV=staging` (not `local`/`production`).
- [ ] `STAGING_SSH_KEY` is a dedicated key, not a personal one, and not
      reused for any future production secret.
- [ ] Port `8080` is only reachable as intended (firewall rules on the
      server, if any, allow the ports you expect and nothing else).
- [ ] `.env` on the server is never committed to git (it isn't part of
      the image or this repo).

## Common Commands

```bash
# Follow app logs
docker compose logs -f app

# Migration status
docker compose exec app php artisan migrate:status

# Tinker (Laravel's REPL, equivalent to `rails console`)
docker compose exec app php artisan tinker

# Restart a single service
docker compose restart app

# Full stop/start
docker compose down
docker compose up -d

# Database backup / restore (MySQL equivalent of pg_dump/psql)
docker compose exec db \
  sh -c 'mysqldump -u root -p"$MYSQL_ROOT_PASSWORD" survey' > staging-backup.sql
cat staging-backup.sql | docker compose exec -T db \
  sh -c 'mysql -u root -p"$MYSQL_ROOT_PASSWORD" survey'
```

## Known Gaps (intentionally out of scope for now)

- **No TLS.** Serves plain HTTP on port 8080; not a concern for staging,
  but production will need a reverse proxy + Certbot (or similar) once a
  domain exists.
- **Not zero-downtime.** Containers are recreated before migrations run;
  fine for a low-traffic staging target, not blue/green safe.
- **`public_shared` volume grows slowly over time** — old hashed asset
  files from previous deploys aren't pruned (cosmetic disk usage only).
