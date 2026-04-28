FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install MySQL extensions for PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy the whole project into Apache's web root
COPY . /var/www/html/

# Make sure Apache can read the files
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80