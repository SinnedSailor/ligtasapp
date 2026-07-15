FROM php:8.2-apache

WORKDIR /var/www/html

COPY . .

RUN apt-get clean && rm -rf /var/lib/apt/lists/* \
    && apt-get update \
    && apt-get install -y --fix-missing --no-install-recommends libicu-dev
  
RUN docker-php-ext-install \
    intl \
    mysqli \
    pdo \
    pdo_mysql

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/writable

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

RUN npm install
RUN npm run build:css

RUN a2enmod rewrite

EXPOSE 80
