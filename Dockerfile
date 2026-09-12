FROM php:8.2-apache
RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite
COPY . /var/www/html/micro-group/
COPY . /var/www/html/
RUN mkdir -p /var/www/html/config /var/www/html/assets/uploads/notes /var/www/html/assets/uploads/students \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 777 /var/www/html/config \
    && chmod -R 777 /var/www/html/assets/uploads
EXPOSE 80 8080
CMD ["apache2-foreground"]