FROM mysql:5.6

ADD ./helloprint.sql /docker-entrypoint-initdb.d

ENV MYSQL_DATABASE=helloprint \
    MYSQL_ROOT_PASSWORD=roothello

EXPOSE 3306