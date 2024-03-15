FROM php:8.2-fpm

#RUN sed -i 's/9000/9001/' /usr/local/etc/php-fpm.d/zz-docker.conf
#RUN sed -i 's/127.0.0.1:9000/0.0.0.0:9000/g' /usr/local/etc/php-fpm.d/www.conf

# RUN apt-get update -yqq 
# RUN apt-get upgrade -yqq 
# RUN apt-get -y autoremove 
# RUN apt-get clean 
# RUN apt-get install git nodejs npm zip unzip vim zlib1g-dev libzip-dev openssl libcurl4-openssl-dev libssl-dev doxygen libnl-utils cmake libpng-dev wget coreutils -yqq \
# 	&& docker-php-ext-install pdo_mysql mysqli opcache zip pcntl sockets gd \
#     && pecl install redis-4.0.1 \
# 	&& pecl install mongodb \
# 	&& docker-php-ext-enable redis zip pcntl mongodb gd

# install all the dependencies and enable PHP modules
RUN apt-get update && apt-get upgrade -y && apt-get install -y \
      procps \
      nano \
      git \
			nodejs \
			npm \
      unzip \
      libicu-dev \
      zlib1g-dev \
      libxml2 \
      libxml2-dev \
      libreadline-dev \
      supervisor \
      cron \
      sudo \
      libzip-dev \
    && pecl install redis \
    && pecl install mongodb \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
      pdo_mysql \
      sockets \
      intl \
      opcache \
      zip \
    && docker-php-ext-enable redis mongodb \
    && rm -rf /tmp/* \
    && rm -rf /var/list/apt/* \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# put php config for Laravel
COPY ./conf/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./conf/php.ini /usr/local/etc/php/php.ini
	
RUN rm -f /etc/localtime \
&& ln -sv /usr/share/zoneinfo/Asia/Taipei /etc/localtime \
&& echo "Asia/Taipei" > /etc/timezone
	
RUN printf '[PHP] \
\ndate.timezone = "Asia/Taipei" \
\n' > /usr/local/etc/php/conf.d/tzone.ini

WORKDIR /var/www/html
  
# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN chmod +x /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1
# RUN curl -sS https://getcomposer.org/installer | php
# RUN mv composer.phar /usr/local/bin/composer

RUN /bin/sh -c ". ~/.bashrc"
RUN echo 'alias ls="ls $LS_OPTIONS"' >> ~/.bashrc
RUN echo 'alias ll="ls $LS_OPTIONS -l"' >> ~/.bashrc
RUN echo 'alias l="ls $LS_OPTIONS -lA"' >> ~/.bashrc

# set working directory
WORKDIR /var/www/html

# copy source files and config file
COPY --chown=www-data:www-data ./laravel /var/www/html/

RUN chmod -R 777 storage \
  && chown -R www-data:www-data storage 

RUN chmod -R 777 bootstrap/cache \
  && chown -R www-data:www-data bootstrap

USER www-data

# install all PHP dependencies
# RUN COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-interaction --no-progress; 

USER root
