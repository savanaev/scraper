ARG DOCKER_PHP_VERSION

FROM php:${DOCKER_PHP_VERSION}-fpm

RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql

RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY ./php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /app
