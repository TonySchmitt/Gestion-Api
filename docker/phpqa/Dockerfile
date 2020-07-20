FROM php:7.4-apache

RUN apt-get update && \
    apt-get install -y --no-install-recommends git zip libxslt-dev libzip-dev unzip libpq-dev apt-utils

RUN a2enmod rewrite
RUN docker-php-ext-install xsl
RUN docker-php-ext-install zip
RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

RUN php composer-setup.php

RUN php -r "unlink('composer-setup.php');"

RUN mv composer.phar /usr/local/bin/composer

RUN composer config -g -- disable-tls true

RUN composer global require 'edgedesign/phpqa:1.23.3' 'friendsofphp/php-cs-fixer' 'jakub-onderka/php-parallel-lint' 'phpstan/phpstan' 'nette/neon' --ignore-platform-reqs --no-scripts

ENV PATH /root/.composer/vendor/bin:$PATH

RUN docker-php-ext-install xsl

# ENTRYPOINT ["/root/.composer/vendor/bin/phpqa"]
# CMD ["phpa"]
CMD ["apache2-foreground"]