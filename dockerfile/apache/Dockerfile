FROM php:8.1-apache

ENV APACHE_DOCUMENT_ROOT /var/www/ketcau/html

RUN a2enmod deflate expires rewrite

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

RUN install-php-extensions opcache
RUN install-php-extensions intl
RUN install-php-extensions gd
RUN install-php-extensions exif
RUN install-php-extensions imagick
RUN install-php-extensions mysqli
RUN install-php-extensions pdo_mysql
RUN install-php-extensions xmlrpc
RUN install-php-extensions redis
RUN install-php-extensions xdebug
RUN install-php-extensions DOM

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

