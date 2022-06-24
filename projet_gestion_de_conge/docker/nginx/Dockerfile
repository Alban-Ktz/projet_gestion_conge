ARG FROM_IMAGE=nginx
ARG FROM_TAG=alpine

FROM ${FROM_IMAGE}:${FROM_TAG}

ARG WORKING_DIR='/var/www/application'

# Install additional tools
RUN apk update \
  && apk upgrade \
  && apk add --update \
    acl \
    bash \
    ca-certificates \
    curl \
    coreutils \
    git \
    htop \
    net-tools \
    nginx-mod-http-perl \
    openssl \
    openssh \
    shadow \
    vim \
    wget

COPY files/docker-defaults.sh /
RUN chmod +x /docker-defaults.sh

VOLUME [ "/etc/nginx/templates/" ]

WORKDIR ${WORKING_DIR}

ENTRYPOINT [ "/docker-defaults.sh" ]

CMD ["nginx", "-g", "daemon off;"]

EXPOSE 80/TCP
EXPOSE 8080/TCP
EXPOSE 443/TCP
