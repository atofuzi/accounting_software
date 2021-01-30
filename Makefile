API_NAME=moma-api
DB_NAME=moma-db

build:
	docker-compose build 

start:
	docker-compose up -d

stop:
	docker-compose stop

restart:
	docker-compose stop; \
	docker-compose up -d;

down:
	docker-compose down

sh:
	docker exec -it $(API_NAME) /bin/bash

install:
	docker-compose up -d --build; \
	docker-compose exec $(API_NAME)  composer install; \
	docker-compose exec $(API_NAME)  php artisan key:generate

migrate:
	docker-compose exec $(API_NAME) php artisan migrate

dbseed:
	docker-compose exec $(API_NAME) php artisan db:seed

log:
	docker logs -f ${API_NAME}

log_db:
	docker-compose logs -f ${DB_NAME}

ps:
	docker-compose ps