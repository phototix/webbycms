# syntax=docker/dockerfile:1

# ---------------------------------------------------------------- vendor stage
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock* ./

# Run with --no-scripts so post-install hooks are skipped during the build.
RUN composer install --no-dev --no-scripts --prefer-dist --no-interaction --no-progress

# ---------------------------------------------------------------------- runtime
FROM php:8.3-apache

# Enable URL rewriting for the front controller.
RUN a2enmod rewrite

# Install the extensions used by WebbyCMS.
RUN apt-get update \
    && apt-get install -y --no-install-recommends git \
    && docker-php-ext-install mysqli pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Production-grade OPcache settings.
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=16'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.validate_timestamps=0'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/html

COPY . .

# Bring in the production dependencies built in the vendor stage.
COPY --from=vendor /app/vendor ./vendor

# Apply the .htaccess rewrite rules.
RUN { \
        echo '<Directory /var/www/html>'; \
        echo '    AllowOverride All'; \
        echo '</Directory>'; \
    } >> /etc/apache2/apache2.conf

# The runtime must be able to write sessions, logs and the page cache.
RUN mkdir -p storage/logs storage/cache storage/sessions \
    && chown -R www-data:www-data storage \
    && chmod -R 775 storage

EXPOSE 80

CMD ["apache2-foreground"]
