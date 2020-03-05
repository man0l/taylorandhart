FROM php:7.4-fpm

RUN docker-php-ext-install pdo_mysql
RUN pecl install apcu

RUN apt-get update && apt-get install -y zip libzip-dev wget \
  && rm -rf /var/lib/apt/lists/* \
  && docker-php-ext-configure zip \
  && docker-php-ext-install zip

RUN docker-php-ext-enable apcu
RUN wget -nv https://getcomposer.org/composer-stable.phar && \
    mv ./composer-stable.phar /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer

WORKDIR /app

RUN PATH=$PATH:/app
