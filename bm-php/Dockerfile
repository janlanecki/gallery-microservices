FROM php:7.0-fpm

RUN apt-get update && apt-get install libssl-dev libcurl4-openssl-dev pkg-config

RUN docker-php-ext-install json
RUN docker-php-ext-install mbstring
RUN pecl install redis && docker-php-ext-enable redis
RUN docker-php-ext-install curl
RUN docker-php-ext-install bcmath

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/bin
RUN php -r "unlink('composer-setup.php');"

RUN apt-get -y install  git 

RUN /bin/composer.phar require php-amqplib/php-amqplib

EXPOSE 9000/tcp

ADD ./php-fpm.conf /etc/php/7.0/fpm/php-fpm.conf
ADD ./pool.conf /etc/php/7.0/fpm/pool.d/www.conf
ADD ./web.ini /etc/php/7.0/fpm/conf.d/99-web.ini
ADD ./php.ini /etc/php/7.0/fpm/php.ini


