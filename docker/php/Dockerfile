FROM php:7.3-fpm

# Install modules
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libicu-dev \
        wget \
            --no-install-recommends


RUN apt-get update && apt-get install -y \
		openssl

RUN docker-php-ext-install intl mbstring pdo_mysql exif

RUN apt-get install -y zip libzip-dev \
  && docker-php-ext-configure zip --with-libzip \
  && docker-php-ext-install zip


# -------------------- Installing PHP Extension: gmp --------------------
RUN apt-get update -y
RUN apt-get install -y libgmp-dev re2c libmhash-dev libmcrypt-dev file
RUN ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/local/include/
RUN docker-php-ext-configure gmp
RUN docker-php-ext-install gmp


#RUN pecl install -o -f xdebug \
#    && rm -rf /tmp/pear

RUN apt-get install -y ffmpeg

RUN export CFLAGS="$PHP_CFLAGS" CPPFLAGS="$PHP_CPPFLAGS" LDFLAGS="$PHP_LDFLAGS" \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        libmagickwand-dev \
    && pecl install imagick-3.4.3 \
    && docker-php-ext-enable imagick

COPY ./docker/php/install-composer.sh /
COPY ./docker/php/php.ini /usr/local/etc/php/
COPY ./docker/php/www.conf /usr/local/etc/php/


RUN apt-get purge -y g++ \
    && apt-get autoremove -y \
    && rm -r /var/lib/apt/lists/* \
    && rm -rf /tmp/* \
    && sh /install-composer.sh \
    && rm /install-composer.sh

RUN usermod -u 1000 www-data

VOLUME /root/.composer
WORKDIR /app

EXPOSE 9000
CMD ["php-fpm"]
