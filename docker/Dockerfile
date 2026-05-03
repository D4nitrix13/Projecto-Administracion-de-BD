FROM php:8.3-apache-bookworm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    cron \
    vim \
    unzip \
    zip \
    wget \
    gnupg \
    libpq-dev \
    libxml2-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && apt-get install -y wget gnupg lsb-release ca-certificates \
    && echo "deb http://apt.postgresql.org/pub/repos/apt $(. /etc/os-release && echo $VERSION_CODENAME)-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && wget -qO- https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor > /etc/apt/trusted.gpg.d/postgresql.gpg \
    && apt-get update \
    && apt-get install -y postgresql-client-18 \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    soap \
    gd \
    xml \
    zip \
    bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN echo "0 0 * * * /usr/local/bin/php /var/www/html/utils/cron_backup.php >> /var/www/html/backups/backup.log 2>&1" > /etc/cron.d/backup-cron \
    && chmod 0644 /etc/cron.d/backup-cron \
    && crontab /etc/cron.d/backup-cron

CMD service cron start && apache2-foreground