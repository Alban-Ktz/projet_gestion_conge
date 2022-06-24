ARG FROM_IMAGE=mariadb
ARG FROM_TAG=latest

FROM ${FROM_IMAGE}:${FROM_TAG}

VOLUME [ "/docker-entrypoint-initdb.d", "/var/lib/mysql" ]

EXPOSE 3306

CMD ["mysqld"]
