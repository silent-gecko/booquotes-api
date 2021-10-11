FROM php:8-fpm
WORKDIR "/application"

RUN apt-get update && apt-get install -y  \
    libpq-dev \
    zip \
    unzip \
    git \
    libxrender1 \
    libfontconfig1 \
    libx11-dev \
    libjpeg62 \
    libxtst6 \
    wget \
    && wget https://github.com/h4cc/wkhtmltoimage-amd64/blob/master/bin/wkhtmltoimage-amd64?raw=true -O /usr/local/bin/wkhtmltoimage \
    && chmod +x /usr/local/bin/wkhtmltoimage \
    && docker-php-ext-install pgsql pdo_pgsql \
    && php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_host = host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \

