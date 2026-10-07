FROM richarvey/nginx-php-fpm:php8.3

# Copy application files
COPY . /var/www/html

# Install dependencies during build
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set Webroot to public directory
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1

# Fix permissions for Laravel storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
