FROM php:7.2

WORKDIR /app

RUN set -x \
    # Other libs
    && apt-get update && apt-get install -y \
        git \
        libxml2-dev \
        build-essential \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libxslt-dev \
        libbz2-dev \
        unzip \
        supervisor \
    && docker-php-ext-install -j$(nproc) pcntl exif soap gd xsl mbstring pdo pdo_mysql zip bz2 bcmath \
    # Install Composer
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === '795f976fe0ebd8b75f26a6dd68f78fd3453ce79f32ecb33e7fd087d39bfeb978342fb73ac986cd4f54edd0dc902601dc') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv ./composer.phar /bin/composer \
    # MySQL libs
    && docker-php-ext-install pdo_mysql \
    # RabbitMQ libs
    && apt-get install -y \
        librabbitmq-dev \
        libssh-dev \
    && docker-php-ext-install \
        bcmath \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    # Redis libs
    && pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis \
    # MongoDB libs
    && pecl install mongodb xdebug \
    && docker-php-ext-enable mongodb xdebug

# Copy project files
ADD ./src /app


# HACK for 'symfonycasts/reset-password-bundle'
ENV DATABASE_URL=none

RUN set -x \
    # App libs
    && composer install --no-scripts

ENV PORT=80

WORKDIR /app

# Setting Supervisor config
RUN mkdir -p /var/log/supervisor \
  && mkdir -p /etc/supervisor/conf.d

ADD supervisor.conf /etc/supervisor.conf

# Setting entrypoint
RUN set -x \
    && echo '' > /app/.env \
    && echo '#!/bin/bash' > /entrypoint.sh \
    && echo 'composer install' >> /entrypoint.sh \
    && echo 'php bin/console doctrine:migrations:migrate --allow-no-migration --no-interaction' >> /entrypoint.sh \
    && echo 'supervisord -c /etc/supervisor.conf &' >> /entrypoint.sh \
    && echo 'cd public/' >> /entrypoint.sh \
    && echo 'php -S 0.0.0.0:$PORT' >> /entrypoint.sh \
    && chmod +x /entrypoint.sh

CMD /entrypoint.sh