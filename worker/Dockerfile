
FROM php:7.3.12-apache

# database requirements
RUN docker-php-ext-install pdo \
    && docker-php-ext-install pdo_mysql

# install Imagick
RUN apt-get update && apt-get install -y \
    libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
    && docker-php-ext-enable imagick