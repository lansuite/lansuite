# Get composer
FROM composer:2.5.8 as composer

FROM php:8.0.29-fpm-bullseye

COPY . /code

# Install libraries and PHP-Extensions that are not provided by the base image
RUN apt-get update \
    && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libsnmp-dev \
        snmp \
        unzip \
        libzip-dev \
    && docker-php-ext-install -j$(nproc) mysqli snmp \
    && docker-php-ext-configure gd --enable-gd --prefix=/usr --with-jpeg --with-freetype \
    && docker-php-ext-install -j$(nproc) gd \
    # Development extensions
    && pecl install xdebug-3.2.1 \
    && docker-php-ext-enable xdebug \
    && echo 'xdebug.mode=debug,develop' >> /usr/local/etc/php/php.ini \
    && echo 'xdebug.discover_client_host=1' >> /usr/local/etc/php/php.ini \
    # Cleanup
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# Composer setup starts here. The zip extension is required for that.
RUN docker-php-ext-install zip
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /code

RUN composer install
