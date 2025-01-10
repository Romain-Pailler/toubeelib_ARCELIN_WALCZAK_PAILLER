FROM php:8.3-cli

# Installer PostgreSQL PDO
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/php
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/php/public"]
