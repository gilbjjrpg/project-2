FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy all project files into Apache web root
COPY . /var/www/html/

# Optional: if your main entry file is in /pages instead of repo root,
# change Apache document root to /var/www/html/pages
RUN sed -ri -e 's!/var/www/html!/var/www/html/pages!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/pages!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80