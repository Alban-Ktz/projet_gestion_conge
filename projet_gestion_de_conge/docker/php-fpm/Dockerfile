FROM php:8.1-fpm

ARG EXPOSED_PORT=9000
ARG WORKING_DIR='/var/www/application'
ARG USER_ID='1000'
ARG GROUP_ID='1000'

RUN curl -sL https://deb.nodesource.com/setup_16.x | bash

# Install additional tools
RUN apt-get update && apt-get install -y --no-install-recommends \
    vim curl ssh software-properties-common libcurl4-gnutls-dev git locales \
    libmcrypt-dev libicu-dev libzip-dev zlib1g-dev libpng-dev libxml2-dev nodejs

RUN docker-php-ext-install ctype intl iconv pdo_mysql zip curl
RUN docker-php-ext-configure intl


# PHP configs
COPY files/php.ini /usr/local/etc/php/php.ini

# NPM (standard-version)
RUN npm install --global standard-version

# SSH configurations
RUN mkdir -p ${WORKING_DIR}
RUN mkdir -p /var/www/.ssh
COPY ssh/id_rsa /var/www/.ssh/id_rsa
RUN ssh-keyscan -t rsa gitlab.axiocode.net > /var/www/.ssh/known_hosts
RUN chown -R www-data:www-data /var/www

# Composer installation
RUN curl https://getcomposer.org/composer.phar -o composer.phar && \
   mv composer.phar /usr/local/bin/composer && chmod +x /usr/local/bin/composer

RUN groupmod -g ${GROUP_ID} www-data \
  && usermod -u ${USER_ID} www-data \
  && usermod -g ${GROUP_ID} www-data \
  && chown -R www-data:www-data ${WORKING_DIR}

WORKDIR ${WORKING_DIR}

EXPOSE ${EXPOSED_PORT}
