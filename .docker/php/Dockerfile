FROM dunglas/frankenphp:php8.4

RUN apt-get update && apt-get install -y --no-install-recommends \
    && rm -rf /var/lib/apt/lists/* \
    && set -eux; \
    install-php-extensions \
    pdo_pgsql \
    xdebug \
    @composer \
    ;

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY --link .docker/php/conf.d/app.dev.ini $PHP_INI_DIR/conf.d/

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile", "--watch"]
