FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system packages
RUN apt-get update && apt-get install -y --no-install-recommends \
    nano \
    curl \
    git \
    unzip \
    nodejs \
    npm \
    libicu-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    intl \
    mysqli \
    pdo \
    pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application source code
COPY . .

# Install PHP dependencies (creates vendor/)
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

# Install Node dependencies and build Tailwind CSS
RUN npm install
RUN npm run build:css

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/writable

EXPOSE 80

CMD ["apache2-foreground"]