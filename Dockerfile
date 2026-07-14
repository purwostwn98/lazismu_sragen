# ─────────────────────────────────────────────────────────────────────────────
# DKPTI SMHWS — runtime image (PHP 8.2 + Apache + CodeIgniter 4 stack)
#
# Image ini SENGAJA tidak meng-COPY source code. Strategi:
#   - Local dev (docker-compose.yml) : source code di-mount via bind ./src
#   - Production (k8s)               : source code di-mount via NFS PVC
#                                      dari TrueNAS 10.3.11.52:/mnt/nfs/dkpti_lifeskills
# Akibatnya rebuild image hanya perlu kalau ada perubahan extension PHP /
# konfigurasi Apache. Update kode harian = git pull di NFS + rollout restart.
#
# Build & push:
#   docker build -t haunans/dkpti-lifeskills-runtime:latest .
#   docker push  haunans/dkpti-lifeskills-runtime:latest
# ─────────────────────────────────────────────────────────────────────────────
FROM php:8.3-apache

ENV ACCEPT_EULA=Y
ENV DEBIAN_FRONTEND=noninteractive

# ── PHP extensions yang dibutuhkan CI4  ──
RUN apt-get update && apt-get install -y \
        gnupg2 unixodbc-dev libltdl-dev ca-certificates apt-transport-https \
        unzip curl git lsb-release \
        libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libicu-dev \
        libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql zip intl pgsql pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# ── Microsoft ODBC Driver 17 untuk SQL Server (dipakai koneksi `cas` ke
#    casakademik @ 10.3.11.70, untuk verifikasi tiket SSO dari myakademik) ──
# RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc \
#         | gpg --dearmor \
#         | tee /usr/share/keyrings/microsoft-prod.gpg > /dev/null \
#     && echo "deb [signed-by=/usr/share/keyrings/microsoft-prod.gpg] https://packages.microsoft.com/ubuntu/20.04/prod focal main" \
#         > /etc/apt/sources.list.d/mssql-release.list \
#     && apt-get update \
#     && ACCEPT_EULA=Y apt-get install -y msodbcsql17 \
#     && rm -rf /var/lib/apt/lists/*

# ── PHP SQLSRV (PECL) ──
# RUN pecl install sqlsrv pdo_sqlsrv \
#     && docker-php-ext-enable sqlsrv pdo_sqlsrv

# ── Composer (dipakai di NFS jika perlu install dep) ──
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ── Apache: enable rewrite + virtualhost (DocumentRoot = public/) ──
RUN a2enmod rewrite
RUN { \
    echo '<VirtualHost *:80>'; \
    echo '  DocumentRoot /var/www/html/public'; \
    echo '  <Directory /var/www/html/public>'; \
    echo '    Options Indexes FollowSymLinks'; \
    echo '    AllowOverride All'; \
    echo '    Require all granted'; \
    echo '  </Directory>'; \
    echo '  ErrorLog ${APACHE_LOG_DIR}/error.log'; \
    echo '  CustomLog ${APACHE_LOG_DIR}/access.log combined'; \
    echo '</VirtualHost>'; \
    } > /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html/public

EXPOSE 80
CMD ["apache2-foreground"]
