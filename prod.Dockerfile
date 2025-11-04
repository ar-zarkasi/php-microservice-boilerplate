FROM docker.io/zarkasi/php-boilerplate:8.3

RUN apt update && apt upgrade -y
RUN apt install -y zip
RUN install-php-extensions pcntl

COPY ./build/config.ini /usr/local/etc/php/conf.d/swoole-config.ini
COPY ./build/app.ini /etc/supervisor.d/app.conf

ARG TZ=Asia/Jakarta
ARG user=user
ARG uid=1000

RUN echo "$user ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

RUN chown -R $user:www-data /var/www/html /var/www/logs
RUN chown -R $user:www-data /run/supervisor

USER $user

WORKDIR /var/www/html
COPY . .

RUN composer install

EXPOSE ${PORT:-9501}

