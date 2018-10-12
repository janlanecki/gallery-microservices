FROM debian:stretch-slim

RUN apt-get update && \
    apt-get install -y curl && \
    apt-get install -y \
        php7.0-fpm php7.0-json php7.0-mbstring php7.0-redis php7.0-curl && \
    rm -rf /var/lib/apt/lists/*

EXPOSE 9000/tcp
CMD ["php-fpm7.0", "-F", "-y", "/etc/php/7.0/fpm/php-fpm.conf"]

ADD ./php-fpm.conf /etc/php/7.0/fpm/php-fpm.conf
ADD ./pool.conf /etc/php/7.0/fpm/pool.d/www.conf
ADD ./web.ini /etc/php/7.0/fpm/conf.d/99-web.ini
ADD ./php.ini /etc/php/7.0/fpm/php.ini
