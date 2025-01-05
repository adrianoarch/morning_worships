FROM phpdockerio/php:8.3-fpm
WORKDIR "/app"

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        git \
        php8.3-bcmath \
        php8.3-bz2 \
        php8.3-cgi \
        php8.3-dba \
        php8.3-gd \
        php8.3-http \
        php8.3-imagick \
        php8.3-mcrypt \
        php8.3-memcached \
        php8.3-mysql \
        php8.3-redis \
        php8.3-uuid \
        php8.3-xdebug \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
