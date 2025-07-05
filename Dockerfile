FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev unzip curl git zip \
    && docker-php-ext-install pdo pdo_mysql zip

COPY . /var/www/html
WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev
RUN chmod -R 755 /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
