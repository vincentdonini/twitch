_default:
    just --list


# ----------------------------------------------------------------------------------------------------------------------
# SETUP & LAUNCH
# ----------------------------------------------------------------------------------------------------------------------
setup-app:
    @just generate-ssl
    @just install-frontend-node-modules
    @just install-websocket-node-modules
    @just dev-up
    @just composer-install
    @grep -qs api.twitch.woder.local /etc/hosts || echo "127.0.0.1\tapi.twitch.woder.local" | sudo tee -a /etc/hosts
    @grep -qs front.twitch.woder.local /etc/hosts || echo "127.0.0.1\tfront.twitch.woder.local" | sudo tee -a /etc/hosts
    @grep -qs ws.twitch.woder.local /etc/hosts || echo "127.0.0.1\tws.twitch.woder.local" | sudo tee -a /etc/hosts
    @just load-fixtures
    @just encore-frontend-dev-watch
    @just encore-websocket-dev-watch

generate-ssl:
    chmod +x ./docker/ssl/openssl.cnf && chmod +x ./docker/ssl/generate-ssl.sh && ./docker/ssl/generate-ssl.sh

start:
    @just dev-up
    @just encore-frontend-dev-watch
    @just encore-websocket-dev-watch

dev-up:
    docker compose up -d --build

dev-down:
	docker compose down --remove-orphans


# ----------------------------------------------------------------------------------------------------------------------
# API
# ----------------------------------------------------------------------------------------------------------------------
shell-backend:
    docker compose exec api bash

cache-clear:
    docker compose exec api bash -c "php bin/console cache:clear --verbose"

composer-install:
    docker compose exec api composer install

reload-local-fixtures:
	@just db-restart
	@just load-fixtures

load-fixtures:
    docker compose exec api bash -c "php bin/console doctrine:fixtures:load --no-interaction"

db-restart:
    -@just db-drop
    @just db-create
    @just db-schema-update

db-drop:
    docker compose exec api bash -c 'php bin/console doctrine:database:drop --force --verbose --no-debug'

db-create:
    docker compose exec api bash -c 'php bin/console doctrine:database:create --verbose --no-debug'

db-schema-diff:
    docker compose exec api bash -c 'php bin/console doctrine:schema:update --dump-sql'

db-schema-update:
    docker compose exec api bash -c 'php bin/console doctrine:schema:update --force'


# ----------------------------------------------------------------------------------------------------------------------
# FRONT
# ----------------------------------------------------------------------------------------------------------------------
shell-frontend:
    docker compose exec front bash

install-frontend-node-modules:
    @cd front && yarn install

encore-frontend-dev-watch:
	@cd front && yarn dev

reset-frontend-node-modules:
    @cd front && rm -rf node_modules
    @just install-frontend-node-modules
    @just encore-frontend-dev-watch


# ----------------------------------------------------------------------------------------------------------------------
# WEBSOCKET
# ----------------------------------------------------------------------------------------------------------------------
shell-websocket:
    docker compose exec websocket bash

install-websocket-node-modules:
    @cd websocket && yarn install

encore-websocket-dev-watch:
	@cd websocket && yarn dev

reset-websocket-node-modules:
    @cd websocket && rm -rf node_modules
    @just install-websocket-node-modules
    @just encore-websocket-dev-watch


# ----------------------------------------------------------------------------------------------------------------------
# DOCKER
# ----------------------------------------------------------------------------------------------------------------------
reload-container:
    docker compose stop
    docker compose up -d

docker-logs CONTAINER LIMIT="100":
    docker compose logs {{CONTAINER}} -f --tail {{LIMIT}}

docker-restart CONTAINER:
    docker compose restart {{CONTAINER}}

docker-prune:
    docker image prune -a -f
    docker volume prune -a -f
    docker network prune -f
    docker system prune -a --volumes -f