FROM richarvey/nginx-php-fpm:latest

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Set Nginx root directory to public
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1

# Install composer dependencies during image build
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set correct permissions for Laravel storage & cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
