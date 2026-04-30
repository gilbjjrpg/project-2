FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install database tools and PHP PDO extensions for MySQL and SQLite
RUN apt-get update \
    && apt-get install -y libsqlite3-dev sqlite3 \
    && docker-php-ext-install pdo_mysql pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

# Copy the whole project into Apache's web root
COPY . /var/www/html/

# Make sure Apache can read the files
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
