FROM php:8.2-apache

WORKDIR /var/www/html/public

COPY . .

RUN apt-get clean && rm -rf /var/lib/apt/lists/* \
    && apt-get update \
    && apt-get install -y --fix-missing --no-install-recommends libicu-dev
  
RUN docker-php-ext-install \
    intl \
    mysqli \
    pdo \
    pdo_mysql

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodej

RUN npm install
RUN npm run build:css

RUN a2enmod rewrite

EXPOSE 80
