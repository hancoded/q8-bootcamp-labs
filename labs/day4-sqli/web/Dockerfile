FROM php:8.2-apache

# Install mysqli for DB access
RUN docker-php-ext-install mysqli

# Enable Apache mod_rewrite (not strictly required but standard)
RUN a2enmod rewrite

# Copy the vulnerable PHP app
COPY public/ /var/www/html/

# Make sure Apache can read everything
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
