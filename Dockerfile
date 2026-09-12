FROM php:8.2-apache
RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite
COPY . /var/www/html/micro-group/
COPY . /var/www/html/
EXPOSE 80 8080
CMD ["apache2-foreground"]