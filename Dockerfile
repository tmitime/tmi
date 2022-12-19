##
## TMI Docker image
## Build the TMI Docker image. Uses a multi-step approach
##


## Build step
FROM klinktech/k-box-ci-pipeline-php:8.2 AS builder

USER root
RUN \
    rm -f /usr/local/etc/php/conf.d/docker-php-ext-pcov.ini
USER $IMAGE_USER
ENV APP_ENV production
WORKDIR /var/www

COPY --chown=php:php . /var/www
RUN \
    mkdir bin &&\
    mkdir -p "storage/app/projects/avatars" &&\
    mkdir -p "storage/documents" &&\
    mkdir -p "storage/framework/cache" &&\
    mkdir -p "storage/framework/cache/data" &&\
    mkdir -p "storage/framework/sessions" &&\
    mkdir -p "storage/framework/views" &&\
    mkdir -p "storage/logs" &&\
    composer install --no-dev --prefer-dist
RUN \
    yarn config set cache-folder .yarn && \
    yarn install --link-duplicates && \
    yarn run production

## Real image build
FROM php:8.1.13-fpm AS php

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
        imagemagick  \
        libfreetype6-dev \
        libjpeg-dev \
        libpng-dev \
        libbz2-dev \
        libzip-dev \
        gettext \
        supervisor \
        cron \
        # gdal-bin \ 
        ## todo: remove gdal
        ## todo: remove ghostscript and imagemagick as not required
        # ghostscript \
        # libmagickwand-dev \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install bz2 zip exif pdo_mysql bcmath pcntl opcache \
    ## todo: remove ghostscript and imagemagick as not required
    # && pecl channel-update pecl.php.net \
    # && pecl install imagick \
    # && docker-php-ext-enable imagick \
    # # Ensure PDF support is enabled in Image Magick
    # && sed -i -e '/rights="none" pattern="{PS,PDF,XPS}"/ s#<!--##g;s#-->##g;' /etc/ImageMagick-6/policy.xml \
    # && sed -i -e 's/rights="none" pattern="{PS,PDF,XPS}"/rights="read|write" pattern="PDF"/' /etc/ImageMagick-6/policy.xml \
    && docker-php-source delete \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

## Forces the locale to UTF-8, suggestion from Marco Zanoni
RUN locale-gen "en_US.UTF-8" \
    && DEBIAN_FRONTEND=noninteractive dpkg-reconfigure locales \
 	&& locale-gen "C.UTF-8" \
 	&& DEBIAN_FRONTEND=noninteractive dpkg-reconfigure locales \
 	&& /usr/sbin/update-locale LANG="C.UTF-8"

## NGINX installation
### The installation procedure is heavily inspired from https://github.com/nginxinc/docker-nginx
RUN set -e; \
	NGINX_GPGKEY=573BFD6B3D8FBC641079A6ABABF5BD827BD9BF62; \
	NGINX_VERSION=1.18.0-1~buster; \
	found=''; \
	apt-get update; \
	apt-get install --no-install-recommends --no-install-suggests -y gnupg1 apt-transport-https ca-certificates; \
	for server in \
		ha.pool.sks-keyservers.net \
		hkp://keyserver.ubuntu.com:80 \
		hkp://p80.pool.sks-keyservers.net:80 \
		pgp.mit.edu \
	; do \
		echo "Fetching GPG key $NGINX_GPGKEY from $server"; \
		apt-key adv --keyserver "$server" --keyserver-options timeout=10 --recv-keys "$NGINX_GPGKEY" && found=yes && break; \
	done; \
	test -z "$found" && echo >&2 "error: failed to fetch GPG key $NGINX_GPGKEY" && exit 1; \
    echo "deb http://nginx.org/packages/debian/ buster nginx" >> /etc/apt/sources.list \
	&& apt-get update \
	&& apt-get install --no-install-recommends --no-install-suggests -y \
						ca-certificates \
						nginx=${NGINX_VERSION} \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

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

## Copy the application code
COPY \
    --chown=www-data:www-data \
    . /var/www/

## Copy in the dependencies from the previous buildstep
COPY \
    --from=builder \
    --chown=www-data:www-data \
    /var/www/vendor/ \
    /var/www/vendor/

COPY \
    --from=builder \
    --chown=www-data:www-data \
    /var/www/public/ \
    /var/www/public/

COPY \
    --from=builder \
    --chown=www-data:www-data \
    /var/www/bootstrap/cache \
    /var/www/bootstrap/cache

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

