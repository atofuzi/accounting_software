API_NAME=moma-api
DB_NAME=moma-db

build:
	docker-compose build 

restart:
	stop start

start:
	docker-compose up -d

stop:
	docker-compose down

sh:
	docker exec -it $(API_NAME) /bin/bash

install:
	cd application; composer install;