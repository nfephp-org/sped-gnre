FROM php:7.3

WORKDIR /var/www

COPY . .

RUN apt-get update && \
    apt-get install libzip-dev libxml2-dev libpng-dev libgraphicsmagick1-dev -y && \
    docker-php-ext-install -j$(nproc) zip soap gd && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install xdebug \
    pecl install gmagick \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_host = host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN composer install
