##
## TMI Docker image
## Build the TMI Docker image. Uses a multi-step approach
##


## Build step
FROM klinktechnology/k-box-ci-pipeline-php:8.3 AS builder

USER root
RUN \
    rm -f /usr/local/etc/php/conf.d/docker-php-ext-pcov.ini
USER $IMAGE_USER
ENV APP_ENV production
WORKDIR /var/www/html

COPY --chown=php:php . /var/www/html
RUN \
    mkdir -p "storage/documents" &&\
    mkdir -p "storage/framework/cache" &&\
    mkdir -p "storage/framework/cache/data" &&\
    mkdir -p "storage/framework/sessions" &&\
    mkdir -p "storage/framework/views" &&\
    mkdir -p "storage/logs" &&\
    mkdir -p "tests" &&\
    composer install --no-dev --prefer-dist --optimize-autoloader
RUN \
    yarn && \
    yarn build && \
    rm -rf node_modules
RUN \
    rm -rf "docker" && \
    rm -rf "storage" && \
    rm -rf "resources/css" && \
    rm -rf "resources/js" && \
    rm -rf .env* && \
    rm -rf yarn.lock && \
    rm -rf *.js && \
    rm -rf "test"

## Real image build
FROM php:8.3.24-fpm-bullseye AS php

LABEL maintainer="Alessio <alessio@avsoft.it>" \
  org.label-schema.name="tmitime/time" \
  org.label-schema.description="Docker image for TMI, a web-based time tracker." \
  org.label-schema.schema-version="1.0" \
  org.label-schema.vcs-url="https://github.com/tmitime/tmi"

## Default environment variables
ENV PHP_MAX_EXECUTION_TIME 120
ENV PHP_MAX_INPUT_TIME 120
ENV PHP_MEMORY_LIMIT 500M
ENV APP_ENV production
ENV DIR /var/www

## Install libraries, envsubst, supervisor and php modules
RUN apt-get update -yqq && \
    apt-get install -yqq --no-install-recommends \ 
        locales \
        supervisor \
        cron \
        ca-certificates \
    && curl -sSLf \
        -o /usr/local/bin/install-php-extensions \
        https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions && \
    chmod +x /usr/local/bin/install-php-extensions && \
    IPE_GD_WITHOUTAVIF=1 install-php-extensions \
        bcmath \
        bz2 \
        exif \
        gd \
        intl \
        pcntl \
        pdo_mysql \
        opcache \
        redis \
        zip \
    && docker-php-source delete \
    && apt-get autoremove -yq --purge \
    && apt-get autoclean -yq \
    && apt-get clean \
    && rm -rf /var/cache/apt/ /var/lib/apt/lists/* /var/log/* /tmp/* /var/tmp/* /usr/share/doc /usr/share/doc-base /usr/share/groff/* /usr/share/info/* /usr/share/linda/* /usr/share/lintian/overrides/* /usr/share/locale/* /usr/share/man/* /usr/share/locale/* /usr/share/gnome/help/*/* /usr/share/doc/kde/HTML/*/* /usr/share/omf/*/*-*.emf

## Forces the locale to UTF-8
RUN locale-gen "en_US.UTF-8" \
    && DEBIAN_FRONTEND=noninteractive dpkg-reconfigure locales \
    && locale-gen "C.UTF-8" \
    && DEBIAN_FRONTEND=noninteractive dpkg-reconfigure locales \
    && /usr/sbin/update-locale LANG="C.UTF-8"

## NGINX installation
### The installation procedure is heavily inspired from https://github.com/nginxinc/docker-nginx
ENV NGINX_VERSION "1.29.0-1~bullseye"
RUN set -ex \
    && apt-get update \
    && apt-get install --no-install-recommends --no-install-suggests -y gnupg1 \
    && \
    NGINX_GPGKEYS=" 8540A6F18833A80E9C1653A42FD21310B49F6B46"; \
    NGINX_GPGKEY_PATH=/usr/share/keyrings/nginx-archive-keyring.gpg; \
    export GNUPGHOME="$(mktemp -d)"; \
    found=''; \
    for NGINX_GPGKEY in $NGINX_GPGKEYS; do \
    for server in \
        hkp://keyserver.ubuntu.com:80 \
        pgp.mit.edu \
    ; do \
        echo "Fetching GPG key $NGINX_GPGKEY from $server"; \
        gpg1 --keyserver "$server" --keyserver-options timeout=10 --recv-keys "$NGINX_GPGKEY" && found=yes && break; \
    done; \
    test -z "$found" && echo >&2 "error: failed to fetch GPG key $NGINX_GPGKEY" && exit 1; \
    done; \
    gpg1 --export "$NGINX_GPGKEYS" > "$NGINX_GPGKEY_PATH" ; \
    rm -rf "$GNUPGHOME"; \
    apt-get remove --purge --auto-remove -y gnupg1 && rm -rf /var/lib/apt/lists/* \
    && echo "deb [signed-by=$NGINX_GPGKEY_PATH] https://nginx.org/packages/mainline/debian/ bullseye nginx" >> /etc/apt/sources.list.d/nginx.list \
	&& apt-get update \
	&& apt-get install --no-install-recommends --no-install-suggests -y nginx=${NGINX_VERSION} \
    && apt-get remove --purge --auto-remove -y && rm -rf /var/lib/apt/lists/* /etc/apt/sources.list.d/nginx.list

## Configure cron to run Laravel scheduler
RUN echo '* * * * * php /var/www/artisan schedule:run >> /dev/null 2>&1' | crontab -

## Copy NGINX default configuration
COPY docker/nginx-default.conf /etc/nginx/conf.d/default.conf

## Copy additional PHP configuration files
COPY docker/php/php-*.ini /usr/local/etc/php/conf.d/

## Override the php-fpm additional configuration added by the base php-fpm image
COPY docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/

## Copy supervisor configuration
COPY docker/supervisor/supervisor.conf /etc/supervisor/conf.d/

## Copying custom startup scripts
COPY docker/configure.sh /usr/local/bin/configure.sh
COPY docker/start.sh /usr/local/bin/start.sh
COPY docker/db-connect-test.php /usr/local/bin/db-connect-test.php

RUN chmod +x /usr/local/bin/configure.sh && \
    chmod +x /usr/local/bin/start.sh

## Copy code and dependencies from builder
COPY \
    --from=builder \
    --chown=www-data:www-data \
    /var/www/html/ \
    /var/www/

ENV APP_STORAGE_FOLDER "/var/www/storage"

WORKDIR /var/www

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/start.sh"]

ARG BUILD_DATE
ARG BUILD_VERSION
ARG BUILD_COMMIT

LABEL version=$BUILD_VERSION \
  org.label-schema.build-date=$BUILD_DATE \
  org.label-schema.vcs-ref=$BUILD_COMMIT

