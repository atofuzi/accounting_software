##php Dockerfile
FROM php:7.4-fpm-buster
LABEL version="1.0"
SHELL ["/bin/bash", "-oeux", "pipefail", "-c"]

ENV TZ Asia/Tokyo
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/composer

COPY --from=composer:1.9.1 /usr/bin/composer /usr/bin/composer

RUN set -ex apk --no-cache add postgresql-dev libpq-dev
RUN apt-get update && \
    apt-get -y install git unzip libzip-dev libicu-dev libonig-dev libpq-dev && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-install intl pdo_pgsql pgsql zip bcmath  && \
    rm -rf /var/www/html

WORKDIR /var/www/
