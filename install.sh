#!/usr/bin/env bash
set -Eeuo pipefail

APP_DIR="${APP_DIR:-$PWD}"
COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.yml}"
PBX_COMPOSE_FILE="${PBX_COMPOSE_FILE:-docker-compose.pbx.yml}"

cd "$APP_DIR"

# Laravel's Composer post-autoload scripts boot the framework and require
# these writable cache directories to exist in a fresh clone.
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true

command -v docker >/dev/null 2>&1 || { echo "Docker is required. Install Docker Engine and Compose v2 first." >&2; exit 1; }
docker compose version >/dev/null 2>&1 || { echo "Docker Compose v2 is required." >&2; exit 1; }

if [[ ! -f .env ]]; then
  cp .env.example .env
  sed -i \
    -e 's/^APP_ENV=.*/APP_ENV=production/' \
    -e 's/^APP_DEBUG=.*/APP_DEBUG=false/' \
    -e 's/^DB_HOST=.*/DB_HOST=db/' \
    -e 's/^REDIS_HOST=.*/REDIS_HOST=redis/' \
    -e 's/^RABBITMQ_HOST=.*/RABBITMQ_HOST=rabbitmq/' \
    -e 's/^FREESWITCH_EVENT_SOCKET_HOST=.*/FREESWITCH_EVENT_SOCKET_HOST=fusionpbx/' \
    .env
  echo "Created .env from .env.example. Review credentials before production use."
fi

# The MySQL image rejects MYSQL_USER=root. Repair older .env files created
# from the previous template before starting the database container.
if grep -q '^DB_USERNAME=root[[:space:]]*$' .env; then
  sed -i 's/^DB_USERNAME=root[[:space:]]*$/DB_USERNAME=voice/' .env
  echo "Changed DB_USERNAME from root to the application user voice."
fi

if grep -q '^DB_PASSWORD[[:space:]]*=$' .env; then
  DB_PASSWORD_GENERATED="$(openssl rand -hex 24 2>/dev/null || head -c 24 /dev/urandom | base64 | tr -dc 'A-Za-z0-9' | head -c 32)"
  sed -i "s/^DB_PASSWORD[[:space:]]*=.*/DB_PASSWORD=${DB_PASSWORD_GENERATED}/" .env
  echo "Generated a database password in .env."
fi

# Build the application image and install PHP/Node dependencies into the mounted project.
docker compose -f "$COMPOSE_FILE" build --pull
# GitHub may temporarily rate-limit dist ZIP downloads. Retry from source with
# one HTTP worker so the install can continue without changing dependencies.
if ! COMPOSER_MAX_PARALLEL_HTTP=1 docker compose -f "$COMPOSE_FILE" run --rm app composer install --no-interaction --prefer-dist --optimize-autoloader; then
  COMPOSER_MAX_PARALLEL_HTTP=1 docker compose -f "$COMPOSE_FILE" run --rm app composer install --no-interaction --prefer-source --optimize-autoloader
fi
docker compose -f "$COMPOSE_FILE" run --rm app npm install

docker compose -f "$COMPOSE_FILE" up -d db redis rabbitmq minio

# Wait for MySQL to accept authenticated connections before running migrations.
for attempt in {1..30}; do
  if docker compose -f "$COMPOSE_FILE" exec -T db sh -c 'mysqladmin ping -h 127.0.0.1 -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" --silent' >/dev/null 2>&1; then
    break
  fi
  if [[ "$attempt" == 30 ]]; then
    echo "MySQL did not become ready after 60 seconds." >&2
    docker compose -f "$COMPOSE_FILE" logs --tail=100 db >&2
    exit 1
  fi
  sleep 2
done

docker compose -f "$COMPOSE_FILE" run --rm app php artisan key:generate --force
docker compose -f "$COMPOSE_FILE" run --rm app php artisan migrate --force
docker compose -f "$COMPOSE_FILE" run --rm app php artisan optimize:clear

# Avoid failing when an existing host web server already owns port 80.
if [[ "${APP_PORT:-80}" == "80" ]] && command -v ss >/dev/null 2>&1 && ss -ltn | awk '$4 ~ /:80$/ { found=1 } END { exit !found }'; then
  for candidate_port in 8080 8081 8082 8083 8084; do
    if ! ss -ltn | awk -v port=":${candidate_port}" '$4 ~ (port "$") { found=1 } END { exit found }'; then
      if grep -q '^APP_PORT=' .env; then
        sed -i "s/^APP_PORT=.*/APP_PORT=${candidate_port}/" .env
      else
        printf '\nAPP_PORT=%s\n' "$candidate_port" >> .env
      fi
      echo "Port 80 is already in use; using APP_PORT=${candidate_port}."
      break
    fi
  done
fi

docker compose -f "$COMPOSE_FILE" up -d app web worker

if [[ -f "$PBX_COMPOSE_FILE" && -f docker/pbx/Dockerfile ]]; then
  docker compose -f "$PBX_COMPOSE_FILE" build --pull
  docker compose -f "$PBX_COMPOSE_FILE" up -d
else
  echo "Application stack is running. PBX stack is not started because an audited docker/pbx/Dockerfile is not present."
  echo "Build and audit the version-pinned FusionPBX/FreeSWITCH image, then run:"
  echo "docker compose -f $PBX_COMPOSE_FILE build --pull && docker compose -f $PBX_COMPOSE_FILE up -d"
fi

docker compose -f "$COMPOSE_FILE" ps
