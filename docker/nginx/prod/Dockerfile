FROM nginx:stable-alpine

# 82 is the standard uid/gid for "www-data" in Alpine
RUN if [[ "" == "$(cat /etc/group | grep www-data)" ]]; then \
        addgroup www-data -g 82; \
    fi; \
    if [[ "" == "$(cat /etc/passwd | grep www-data)" ]]; then \
        adduser -D -u 82 -h /var/www -s /bin/bash -G www-data www-data; \
    fi; \
    mkdir -m 0755 -p /var/www/public; \
    chown -Rf www-data:www-data /var/www

WORKDIR /var/www

COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY public /var/www/public

RUN mkdir -m 0755 -p /var/www/public/bundles \
 && chown -Rf www-data:www-data /var/www/public
