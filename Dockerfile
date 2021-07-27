FROM alpine:3.13

# Install selected extensions and other stuff. git is sometimes used by composer to install dev-master branches
RUN apk add --no-cache \
    git \
    wget curl \
    php8 php8-fpm php8-common php8-pgsql php8-bcmath php8-intl php8-sqlite3 \
    php8-openssl php8-iconv php8-phar php8-dom php8-pdo php8-tokenizer \
    php8-mbstring php8-xml php8-pdo_pgsql php8-pdo_sqlite php8-curl php8-sodium \
    php8-fileinfo php8-xmlwriter php8-simplexml php8-pecl-redis \
    && \
    # this just makes sure that scripts getting executed by "php" will still work
    ln -s /usr/bin/php8 /usr/bin/php

RUN apk add --no-cache -X http://dl-cdn.alpinelinux.org/alpine/edge/testing php8-pecl-pcov
# get composer 2.0
COPY --from=composer:2.1.3 /usr/bin/composer /usr/bin/composer

RUN \
    touch /etc/php8/conf.d/custom.ini && \
    echo "pcov.enabled = 1" >> /etc/php8/conf.d/custom.ini && \
    echo "pcov.directory = /code/src" >> /etc/php8/conf.d/custom.ini && \
    echo "memory_limit = 2G" >> /etc/php8/conf.d/custom.ini

WORKDIR /code
