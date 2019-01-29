FROM php:7.0-fpm

COPY . /code

# Install libraries and PHP-Extensions that are not provided by the base image
RUN apt-get update \
    && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libsnmp-dev \
        snmp \
        unzip \
    && docker-php-ext-install -j$(nproc) mysqli snmp \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    # Development extensions
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo 'xdebug.remote_enable=1' >> /usr/local/etc/php/php.ini \
    && echo 'xdebug.remote_connect_back=1' >> /usr/local/etc/php/php.ini \
    # Cleanup
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# Composer setup starts here. The zip extension is required for that.
# See https://github.com/composer/docker/blob/8a2a40c3376bac96f8e3db2f129062173bff7734/1.6/Dockerfile
# and https://github.com/composer/docker/blob/942dac53831e7b4ca4419a135304e177b9d29dbd/1.8/Dockerfile
# or more recently https://getcomposer.org/download/
RUN docker-php-ext-install zip

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp
ENV COMPOSER_VERSION 1.8.0

RUN curl --silent --fail --location --retry 3 --output /tmp/installer.php --url https://raw.githubusercontent.com/composer/getcomposer.org/d3e09029468023aa4e9dcd165e9b6f43df0a9999/web/installer \
    && php -r " \
    \$signature = '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8'; \
    \$hash = hash_file('sha384', '/tmp/installer.php'); \
    if (!hash_equals(\$signature, \$hash)) { \
        unlink('/tmp/installer.php'); \
        echo 'Integrity check failed, installer is either corrupt or worse.' . PHP_EOL; \
        exit(1); \
    }" \
    && php /tmp/installer.php --no-ansi --install-dir=/usr/bin --filename=composer --version=${COMPOSER_VERSION} \
    && composer --ansi --version --no-interaction \
    && rm -rf /tmp/* /tmp/.htaccess

WORKDIR /code
RUN composer install
