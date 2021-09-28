FROM php:8-fpm
WORKDIR "/application"

RUN apt-get update && apt-get install -y libpq-dev zip unzip git && docker-php-ext-install pgsql pdo_pgsql \
    && php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \

