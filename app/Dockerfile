FROM php:7.3.12-apache

# url rewriting and database requirements
RUN a2enmod rewrite \
    && a2enmod headers\
    && docker-php-ext-install pdo \
    && docker-php-ext-install pdo_mysql