FROM richarvey/nginx-php-fpm:3.1.6

COPY . .

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# PHP limits (uploads for produk foto etc.)
ENV PHP_UPLOAD_MAX_FILESIZE 25
ENV PHP_POST_MAX_SIZE 26
ENV PHP_MEM_LIMIT 256

# Install production dependencies at build time for faster cold starts
RUN composer install --no-dev --optimize-autoloader --working-dir=/var/www/html

CMD ["/start.sh"]