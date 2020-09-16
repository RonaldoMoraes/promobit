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

ADD ./src /app

# HACK for 'symfonycasts/reset-password-bundle'
ENV DATABASE_URL=none

RUN set -x \
    # App libs
    && composer install
