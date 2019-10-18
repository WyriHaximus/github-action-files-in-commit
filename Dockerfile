FROM wyrihaximusnet/php:7.3-zts-alpine3.10-dev-root

RUN mkdir /workdir
COPY ./list-files.php /workdir
COPY ./composer.json /workdir
COPY ./composer.lock /workdir
WORKDIR /workdir

RUN composer install --ansi --no-progress --no-interaction --prefer-dist

ENTRYPOINT ["php", "/workdir/list-files.php"]
