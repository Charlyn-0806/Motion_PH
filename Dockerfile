FROM php:8.1-apache

# Install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Disable conflicting MPM modules
RUN a2dismod mpm_event mpm_worker || true

# Enable correct MPM for PHP
RUN a2enmod mpm_prefork

# Enable rewrite (optional but common for PHP apps)
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html/

EXPOSE 80

CMD ["apache2-foreground"]