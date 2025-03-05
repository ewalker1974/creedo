FROM php:8.3-fpm

RUN apt-get update && apt-get install -y libcurl4-openssl-dev libpq-dev libzip-dev unzip libxslt-dev libzip-dev zip \
&& docker-php-ext-install zip bcmath opcache xsl zip \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install mongodb  && docker-php-ext-enable mongodb

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

ADD php.ini /usr/local/etc/php/php.ini

WORKDIR /var/www/html


