FROM dunglas/frankenphp:1.5-php8.3.21-alpine

ARG GIT_HASH
ENV GIT_HASH=${GIT_HASH}

WORKDIR /app

# Установка зависимостей
RUN apk update && \
    apk add --no-cache \
        openssh-client \
        git \
        sudo \
        busybox \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        postgresql-dev \
        imagemagick-dev \
        oniguruma-dev \
        curl \
        ca-certificates \
        zip \
        unzip \
        nodejs \
        npm \
        yarn \
        autoconf \
        g++ \
        make \
        icu-dev \
        gmp-dev

# Установка расширений PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo_pgsql opcache exif mbstring zip intl pcntl gmp && \
    docker-php-ext-enable gmp

# Установка Redis через PECL
RUN pecl update-channels && \
    pecl install redis && \
    pecl install excimer && \
    docker-php-ext-enable redis && \
    docker-php-ext-enable excimer

# Удаление ненужных файлов и кэша
RUN rm -rf /var/cache/apk/* /tmp/* /var/tmp/*
