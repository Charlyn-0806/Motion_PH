FROM php:8.1-apache

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# HARD reset Apache MPM modules (important fix)
RUN a2dismod mpm_event || true
RUN a2dismod mpm_worker || true
RUN a2dismod mpm_prefork || true

# Enable ONLY prefork (required for PHP)
RUN a2enmod mpm_prefork

# Enable rewrite (optional but safe)
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html/

EXPOSE 80

CMD ["apache2-foreground"]