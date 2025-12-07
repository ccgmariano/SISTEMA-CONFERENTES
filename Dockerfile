FROM php:8.2-apache

# Enable Apache mod_rewrite (useful later if we add routes)
RUN a2enmod rewrite

# Copy application code
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
