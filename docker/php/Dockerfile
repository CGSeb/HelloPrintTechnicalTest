FROM php:7.2-apache
RUN a2enmod rewrite
RUN apt-get update && apt-get install -y mysql-client \
&& docker-php-ext-install pdo_mysql mysqli

EXPOSE 80
EXPOSE 3306