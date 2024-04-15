ARG DOCKER_PHP_VERSION

FROM php:${DOCKER_PHP_VERSION}-cli

RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

RUN apt-get update && apt-get install -y libpq-dev unzip cron \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql

RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY ./php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY ./php/php.ini /usr/local/etc/php/conf.d/php.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --quiet

ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /app

COPY ./crontabs/crontab /etc/cron.d/

RUN chmod 644 /etc/cron.d/*

