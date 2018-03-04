FROM php:7.0-apache
MAINTAINER cryptcoin.junkey@gmail.com

RUN a2enmod headers

COPY . /var/www/html/

