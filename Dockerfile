FROM php:8.2-apache

# Instalar dependências do SQLite
RUN apt-get update \
    && apt-get install -y sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html
WORKDIR /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
