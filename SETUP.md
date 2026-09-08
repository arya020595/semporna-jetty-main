# Setup & Running the Project — Semporna Jetty

This guide has been tested end-to-end (image build, migrations, opening the login page) in a Linux/Docker environment. Follow the steps in order.

## Stack

- **Backend**: Laravel 8, PHP 7.4
- **Frontend**: Vue 3 (Composition API) + Inertia.js, bundled with Laravel Mix/Webpack
- **Database**: MySQL 8
- **Web server**: Nginx + PHP-FPM (via Docker)

## Prerequisites

- Docker + Docker Compose (check with `docker compose version`)
- Node.js 18/20 + npm (for building frontend assets — this runs on the host, not inside a container)
- Git

You don't need PHP/Composer/MySQL installed on your host — everything runs inside containers.

## 1. Clone & configure the environment

```bash
git clone <this-repo-url>
cd semporna-jetty-main
cp .env.example .env
```

Edit `.env` and make sure the database section looks like this. `mysql` must match the service name in `docker-compose.yml` — **not** `127.0.0.1` — because the app runs inside the Docker network:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=survey
DB_USERNAME=root
DB_PASSWORD=
```

> If you want to connect to that database from a host tool (TablePlus, DBeaver, etc.), the port is **3307** (see `docker-compose.yml`), not 3306 — it was remapped to avoid clashing with any MySQL already running on your machine.

## 2. Build & start the containers

```bash
docker compose build
docker compose up -d
```

This starts 3 services:
- `app` — PHP-FPM (custom image built from `Dockerfile`)
- `nginx` — web server, exposed at `http://localhost:8000`
- `mysql` — database

Confirm everything is `Up`:

```bash
docker compose ps
```

## 3. Install PHP dependencies

```bash
docker compose exec app composer install
```

> ⚠️ **Private package `easycode/autopull`**: `composer.json` requires this package from a private GitLab repo (`gitlab.com/easycode.id/framework/autodeploy`) over SSH. If you have SSH access to that GitLab, make sure your SSH agent is running on the host before `docker compose exec` (Docker Desktop usually forwards `SSH_AUTH_SOCK` automatically if configured; otherwise, run `composer install` directly on the host with PHP 7.4 + Composer installed, instead of inside the container). If you don't have access, this package can be skipped temporarily for local dev (remove the `"easycode/autopull": "^1.0"` line from `composer.json` and its block from `composer.lock`, then run `composer install` again) — it's just an auto-deploy helper, not core app logic. **Don't commit that change** if it's only for local use.
>
> CI (`.github/workflows/laravel-tests.yml`) hits this same problem — GitHub Actions has no access to that GitLab repo — and automates this exact workaround for its own ephemeral checkout.

## 4. Generate the app key & run migrations

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
```

If you want initial seed data:

```bash
docker compose exec app php artisan db:seed
```

## 5. Install & build frontend assets

Run on the host (not inside the container), using Node.js:

```bash
npm install
npm run production   # one-off build, to see the final result
# or for development with auto-rebuild:
npm run watch
```

## 6. Open the app

```
http://localhost:8000
```

If all the steps above succeeded, you'll see the role-selection page (Tour Operator / Authorities / Jetty Operator / Administrator), and `/login` will show the login form.

## Everyday commands

```bash
# Run any artisan command
docker compose exec app php artisan <command>

# Tail app (PHP-FPM) logs
docker compose logs -f app

# Tail nginx logs
docker compose logs -f nginx

# Shell into the app container
docker compose exec app bash

# Stop all containers
docker compose down

# Stop and wipe database data (careful, this deletes everything)
docker compose down -v
```

## Troubleshooting

**`could not find driver` during migrate**
The `pdo_mysql` extension was missing from the PHP image. This is already fixed in the `Dockerfile` (the `RUN docker-php-ext-install pdo_mysql mysqli` line) — if you still hit this, run `docker compose build --no-cache app` then `docker compose up -d` again.

**`ports are not available: ... 3306` / `... 8000` when running `docker compose up`**
Something else on your machine is already using that port (e.g. a local MySQL install, or another Docker project). Change the host-side port in `docker-compose.yml` (the left number under `ports:`, e.g. `"3307:3306"`) — leave the right-hand (container-side) port as is.

**`Permission denied (publickey)` during composer install**
That's the private package `easycode/autopull`, which needs SSH access to the work GitLab. See the note in step 3 above.

**Page loads but CSS/JS looks broken / 404 on `/js/app.js`**
Assets haven't been built yet. Run `npm install && npm run production` (step 5).

**PHP code changes don't show up**
The `app` container mounts the project folder (`./:/var/www`), so file changes take effect immediately — no image rebuild needed, just refresh the browser. You only need to re-run `docker compose build` / `composer install` / `npm install` when you change the `Dockerfile` or a composer/npm dependency.

## Native setup (without Docker) — alternative

If you'd rather not use Docker, install on your host: PHP 7.4 with the `pdo_mysql, mbstring, exif, pcntl, bcmath, zip, gd` extensions, Composer 2, MySQL 8, and Node 18/20. Then:

```bash
composer install
cp .env.example .env   # set DB_HOST=127.0.0.1 to match your local MySQL
php artisan key:generate
php artisan migrate --force
npm install && npm run production
php artisan serve
```
