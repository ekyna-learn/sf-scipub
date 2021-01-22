#!/bin/bash

if [[ $(uname -s) == MINGW* ]]; then export MSYS_NO_PATHCONV=1; fi

if ! [ -f .env.local ]; then
    printf "Please create .env.local file (based on .env)."
    exit 1
fi

source .env.local

COMPOSE_FILES="docker/compose-${APP_ENV}.yml"

# ----------------------------- NETWORK -----------------------------

NetworkExists() {
  if docker network ls --format '{{.Name}}' | grep -q "${1}\$"; then
    return 0
  fi
  return 1
}

NetworkCreate() {
  if ! NetworkExists "$1"; then
    if ! docker network create "$1"; then
      exit 1
    fi
  fi
}

# ----------------------------- VOLUME -----------------------------

VolumeExists() {
  if docker volume ls --format '{{.Name}}' | grep -q "${1}\$"; then
    return 0
  fi
  return 1
}

VolumeCreate() {
  if ! VolumeExists "${1}"; then
    if ! docker volume create --name "${1}"; then
      exit 1
    fi
  fi
}

VolumeRemove() {
  if VolumeExists "${1}"; then
    if ! docker volume rm "${1}"; then
      exit 1
    fi
  fi
}

Execute() {
  if [[ $(uname -s) == MINGW* ]]; then
    # shellcheck disable=SC2086
    winpty docker exec -u www-data -it "${PROJECT_NAME}_${1}" ${2}
  else
    # shellcheck disable=SC2086
    docker exec -u www-data -it "${PROJECT_NAME}_${1}" ${2}
  fi
}

SfCommand() {
  Execute php "php bin/console ${1}"
}

Composer() {
  Execute php "composer ${1}"
}

DockerCompose() {
  docker-compose --env-file=.env.local -f "${COMPOSE_FILES}" "$@"
}

case $1 in
build)
  if ! [[ ${2} =~ nginx|php ]]; then
      printf "Usage: ./manage build [php|nginx]"
      exit 1
  fi
  docker build -f "docker/${2}/${APP_ENV}/Dockerfile" -t "${COMPANY_NAME}/${PROJECT_NAME}-${2}-${APP_ENV}" .
  ;;
up)
  NetworkCreate "${NETWORK_NAME}"
  if [[ "${APP_ENV}" == "prod" ]]; then
    VolumeCreate "${PROJECT_NAME}_bundles"
  fi
  VolumeCreate "${PROJECT_NAME}_vendor"
  VolumeCreate "${PROJECT_NAME}_database"

  DockerCompose up -d

  if [[ "${APP_ENV}" == "prod" ]]; then
    Composer "install --prefer-dist --no-interaction --no-progress"
    SfCommand "doctrine:migrations:migrate -q --no-debug"
  fi
  ;;
down)
  DockerCompose down -v --remove-orphans
  ;;
clear)
  DockerCompose down -v --remove-orphans
  VolumeRemove "${PROJECT_NAME}_bundles"
  VolumeRemove "${PROJECT_NAME}_vendor"
  VolumeRemove "${PROJECT_NAME}_database"
  ;;
sf)
  SfCommand "${*:2}"
  ;;
composer)
  Composer "${*:2}"
  ;;
# ------------- HELP -------------
*)
  printf "Usage: ./manage.sh [args]
 - build [php|nginx]    Builds the service image.
 - up                   Starts the services.
 - down                 Stops the services.
 - clear                Stops the services and deletes the volumes.
 - sf [cmd]             Runs the symfony command.
 - composer [cmd]       Runs the composer command.
 "
  ;;
esac
