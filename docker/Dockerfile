FROM php:8.3-apache-bookworm

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV TZ=America/Managua

RUN apt-get update && apt-get install -y --no-install-recommends \
    cron vim unzip zip wget gnupg ca-certificates \
    libpq-dev libxml2-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && echo "deb http://apt.postgresql.org/pub/repos/apt bookworm-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && wget -qO- https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor > /etc/apt/trusted.gpg.d/postgresql.gpg \
    && apt-get update && apt-get install -y --no-install-recommends postgresql-client-18 \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql soap gd xml zip bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

CMD service cron start && apache2-foreground
