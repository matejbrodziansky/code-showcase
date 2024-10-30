# PHP BASE --------------------------------------
FROM debian:bookworm-slim as php-base

WORKDIR /var/www/html

RUN apt-get -y update
RUN apt-get -y install apt-transport-https lsb-release ca-certificates curl sudo
RUN curl -sSLo /usr/share/keyrings/deb.sury.org-php.gpg https://packages.sury.org/php/apt.gpg
RUN sh -c 'echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" >> /etc/apt/sources.list.d/php.list'
RUN apt-get -y update
RUN apt-get -y install \
    nginx \
    vim \
    supervisor \
    zip \
    cron \
    xvfb \
    php8.2-cli \
    php8.2-xml \
    php8.2-pgsql \
    php8.2-gd \
    php8.2-intl \
    php8.2-zip \
    php8.2-fpm \
    php8.2-curl \
        nodejs \
        npm \
        yarnpkg


COPY --from=composer:2.6 /usr/bin/composer /usr/local/bin/composer

#
COPY .docker/entrypoint.sh /etc/entrypoint.sh
COPY .docker/setup.sh /etc/setup.sh
COPY .docker/php-base.ini /etc/php/8.2/cli/conf.d/php-base.ini
COPY .docker/php-base.ini /etc/php/8.2/fpm/conf.d/php-base.ini
COPY .docker/php-logging.conf /etc/php/8.2/fpm/pool.d/php-logging.conf
COPY .docker/cron/crontab /etc/cron.d/m365-admin
COPY .docker/nginx-site.conf /etc/nginx/sites-available/default
COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

ARG APP_ENV=prod
ENV APP_ENV=${APP_ENV}
ENV APP_DEBUG=false
ENV TZ=Europe/Prague

COPY . /var/www/html

RUN ln -s /usr/bin/yarnpkg /usr/bin/yarn
RUN yarn install
RUN composer install --optimize-autoloader --no-interaction

EXPOSE 8080

RUN chmod +x /etc/entrypoint.sh
RUN chmod +x /etc/setup.sh
CMD ["/etc/entrypoint.sh"]
