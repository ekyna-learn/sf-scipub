FROM php:7-fpm-alpine

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"; \
    php -r "if (trim(hash_file('SHA384', 'composer-setup.php')) !== trim(file_get_contents('https://composer.github.io/installer.sig'))) { echo 'Composer installer corrupt' . PHP_EOL; exit(1); }"; \
    php composer-setup.php --quiet --install-dir=/usr/bin --filename=composer; \
    php -r "unlink('composer-setup.php');"; \
    docker-php-ext-install pdo_mysql; \
    docker-php-ext-install opcache; \
    docker-php-source extract; \
    apk add --no-cache --virtual .phpize-deps-configure $PHPIZE_DEPS; \
    pecl install apcu; \
    docker-php-ext-enable apcu; \
    apk del .phpize-deps-configure; \
    docker-php-source delete; \
    rm -Rf /var/www/html

COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/php/php-fpm.conf /etc/php7/php-fpm.conf

WORKDIR /var/www
