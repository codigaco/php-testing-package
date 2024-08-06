FROM php:8.2-fpm-alpine as system
RUN apk update && apk add linux-headers autoconf make g++

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

FROM system as base
WORKDIR /app
RUN --mount=type=bind,source=composer.json,target=./composer.json \
    --mount=type=cache,target=/root/.composer \
    composer install --no-dev --prefer-dist --no-suggest --no-interaction --no-progress --no-scripts

FROM base as final
ARG VERSION
ARG DEV_MODE=0

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="${DEV_MODE}"

RUN if [[ "${DEV_MODE}" == "1" ]] ; then  \
        apk update && apk add nano git \
        && pecl install xdebug \
        && docker-php-ext-enable xdebug \
    ; fi

COPY ./ /app/

RUN composer install `"${DEV_MODE}" == "1" ? "" : "--no-dev"` --prefer-dist --no-suggest --no-interaction --no-progress --no-scripts \
    && rm -rf /tmp/* \
    && rm -rf /var/cache/apk/* \
    && rm -rf /var/tmp/* \
    && rm -rf /var/log/*


RUN if [[ "${DEV_MODE}" == "0" ]] ; then  \
    rm -rf /usr/bin/composer \
    && rm -rf tests \
    && rm -rf iac \
    ; fi
