#!/usr/bin/env bash
set -Eeuo pipefail

APP_DIR="${APP_DIR:-$PWD}"
COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.yml}"
PBX_COMPOSE_FILE="${PBX_COMPOSE_FILE:-docker-compose.pbx.yml}"

cd "$APP_DIR"

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

# Build the application image and install PHP/Node dependencies into the mounted project.
docker compose -f "$COMPOSE_FILE" build --pull
docker compose -f "$COMPOSE_FILE" run --rm app composer install --no-interaction --prefer-dist --optimize-autoloader
docker compose -f "$COMPOSE_FILE" run --rm app npm install

docker compose -f "$COMPOSE_FILE" up -d db redis rabbitmq minio
docker compose -f "$COMPOSE_FILE" run --rm app php artisan key:generate --force
docker compose -f "$COMPOSE_FILE" run --rm app php artisan migrate --force
docker compose -f "$COMPOSE_FILE" run --rm app php artisan optimize:clear
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
