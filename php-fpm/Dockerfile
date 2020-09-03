FROM php:fpm-alpine
COPY wait-for-it.sh /usr/bin/wait-for-it
RUN chmod +x /usr/bin/wait-for-it
RUN apk --update --no-cache add git
RUN apk add bash
RUN docker-php-ext-install pdo_mysql
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
CMD composer install ; wait-for-it promobit_database_1:3306 -- bin/console doctrine:migrations:migrate --allow-no-migration --no-interaction ;  php-fpm 
EXPOSE 9000
