FROM dunglas/frankenphp:php8.4

RUN apt-get update && apt-get install -y --no-install-recommends \
    && rm -rf /var/lib/apt/lists/* \
    && set -eux; \
    install-php-extensions \
    pdo_pgsql \
    xdebug \
    ;

COPY --link .docker/php/conf.d/app.dev.ini $PHP_INI_DIR/app.conf.d/

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile", "--watch"]
