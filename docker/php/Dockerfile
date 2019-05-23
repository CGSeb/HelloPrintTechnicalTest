FROM php:7.2-apache
RUN a2enmod rewrite
RUN apt-get update && apt-get install -y mysql-client ssmtp\
&& docker-php-ext-install pdo_mysql mysqli sockets bcmath

RUN echo 'sendmail_path = /usr/sbin/ssmtp -t' >> /usr/local/etc/php/php.ini
COPY ssmtp.conf /etc/ssmtp/ssmtp.conf
RUN chmod 640 /etc/ssmtp/ssmtp.conf\
&& chown root:mail /etc/ssmtp/ssmtp.conf

EXPOSE 80
EXPOSE 3306
EXPOSE 25