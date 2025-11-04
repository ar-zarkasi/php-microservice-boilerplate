FROM docker.io/zarkasi/php-boilerplate:8.3

RUN apt update && apt upgrade -y
RUN apt install -y zip
RUN install-php-extensions pcntl

COPY ./build/config.ini /usr/local/etc/php/conf.d/swoole-config.ini
COPY ./build/dev.ini /etc/supervisor.d/app.conf

RUN echo "alias hyperf='php bin/hyperf.php'" >> /root/.bashrc

WORKDIR /var/www/html


