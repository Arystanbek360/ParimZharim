GIT_HASH=$(shell git rev-parse --short HEAD)
CI_REGISTRY_IMAGE := parim-zharim
APP_PROJECT := parim-zharim

build-base-octane-frankenphp-for-deploy:
	docker build --platform=linux/amd64 \
    -f build/production-octane-frankenphp/app/Base.dockerfile \
    -t parim-zharim/base:base \
    .

build-octane-frankenphp-for-deploy:
	cd build/production-octane-frankenphp && cp ../../auth.json ./auth.json && \
	CI_REGISTRY_IMAGE="${CI_REGISTRY_IMAGE}" TAG=${APP_PROJECT}-octane-frankenphp GIT_HASH=${GIT_HASH} docker compose -f docker-compose-build.yml build && \
	rm ./auth.json

test:
	./vendor/bin/sail php artisan test --colors=always

composer-install:
	composer install --no-interaction --no-progress

reload-octane:
	./vendor/bin/sail artisan octane:reload

run:
	./vendor/bin/sail up

stop:
	./vendor/bin/sail down

npm-build:
	npm i && npm run dev

migrate:
	./vendor/bin/sail artisan devcraft-web-platform:migrate

rollback:
	./vendor/bin/sail artisan devcraft-web-platform:rollback

create-all-permissions:
	./vendor/bin/sail artisan devcraft-web-platform:create-all-permissions

create-pg-trgm-extension:
	./vendor/bin/sail artisan devcraft-web-platform:create-gin-extension

create-test-database:
	./vendor/bin/sail artisan devcraft-web-platform:create-test-database
	./vendor/bin/sail artisan devcraft-web-platform:create-gin-extension --connection=pgsql_test

seed:
	./vendor/bin/sail artisan db:seed

publish:
	./vendor/bin/sail artisan vendor:publish --tag=web-assets

install-laravel-nova-npm:
	cd ./vendor/laravel/nova && npm install

clear-log:
	echo "" > storage/logs/laravel.log

clear-all:
	./vendor/bin/sail artisan optimize:clear
