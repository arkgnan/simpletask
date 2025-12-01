FROM dunglas/frankenphp:latest

# Set environment variables
ENV DEBIAN_FRONTEND=noninteractive

# Set working directory
WORKDIR /var/www/html

# Install system dependencies + Imagick dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev libzip-dev \
    libicu-dev supervisor gnupg ca-certificates \
    libmagickwand-dev libmagickcore-dev --no-install-recommends \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && echo "extension=intl.so" > /usr/local/etc/php/conf.d/docker-php-ext-intl.ini \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Add custom PHP upload limit settings
COPY php-uploads.ini /usr/local/etc/php/conf.d/uploads.ini

# Install Composer 2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Node.js (LTS 20.x)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

# Copy Laravel project files
COPY . .

# Install Laravel PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Ensure necessary folders and log file exist
RUN mkdir -p /var/www/html/storage/logs \
    && touch /var/www/html/storage/logs/laravel.log

# Set file permissions properly
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Artisan storage:link
RUN php artisan storage:link

# Install NPM dependencies and build frontend assets
RUN npm install && npm run build

# Copy Supervisor configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose ports for Nginx
EXPOSE 80

# Start Supervisor to manage Nginx and PHP-FPM
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
