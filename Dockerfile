FROM php:7.0-fpm

# Install PHP-Extensions that are not provided by the base image
RUN apt-get update \
    && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libsnmp-dev \
        snmp \
    && docker-php-ext-install -j$(nproc) mysqli snmp \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    # Development extensions
    && pecl install xdebug-2.5.5 \
    && docker-php-ext-enable xdebug
