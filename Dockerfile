FROM php:8.2-apache

# Apache como root (permite escrita no Persistent Disk)
ENV APACHE_RUN_USER=root
ENV APACHE_RUN_GROUP=root

RUN a2enmod rewrite

COPY . /var/www/html
WORKDIR /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
