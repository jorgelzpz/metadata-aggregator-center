FROM php:7.0-apache

MAINTAINER Jorge López

COPY docker/000-default.conf /etc/apache2/sites-available
COPY docker/php.ini /usr/local/etc/php

RUN apt-get update && apt-get install -y \
    libmcrypt-dev \
    sqlite3 \
    libsqlite3-dev \
    python-dev \
    build-essential \
    libxml2-dev \
    libxslt-dev \
    python-virtualenv \
    libz-dev \
    libyaml-dev
RUN docker-php-ext-install mcrypt
RUN a2enmod rewrite
RUN mkdir -p /opt/pyff
RUN virtualenv /opt/pyff && . /opt/pyff/bin/activate && pip install pyff


WORKDIR /app
EXPOSE 80
CMD ["apache2-foreground"]
