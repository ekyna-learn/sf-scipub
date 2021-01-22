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

COPY docker/php/prod/cron-cmd.sh /cron-cmd.sh
COPY docker/php/prod/cron-entry.sh /cron-entry.sh
COPY docker/php/prod/crontab /etc/cron.d/www-data

COPY bin /var/www/bin
COPY config /var/www/config
COPY migrations /var/www/migrations
COPY public /var/www/public
COPY src /var/www/src
COPY templates /var/www/templates
COPY tests /var/www/tests
COPY translations /var/www/translations
COPY .env /var/www/.env
COPY composer.json /var/www/composer.json
COPY composer.lock /var/www/composer.lock
COPY symfony.lock /var/www/symfony.lock

RUN mkdir -p /etc/cron.d \
 && mkdir -p /var/www/var/cache \
 && mkdir -p /var/www/var/log \
 && mkdir -p /var/www/vendor \
 && chmod -R 744 /var/www \
 && chown -R www-data:www-data /var/www \
 && chown root:root /etc/cron.d \
 && chown root:root /etc/cron.d/www-data \
 && chmod 444 /etc/cron.d/www-data \
 && chmod 544 /cron-cmd.sh \
 && chmod 544 /cron-entry.sh

WORKDIR /var/www
