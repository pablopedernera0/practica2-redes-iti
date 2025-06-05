FROM php:8.1-fpm

ENV http_proxy=http://10.10.254.219:3128
ENV https_proxy=http://10.10.254.219:3128
ENV no_proxy=localhost,127.0.0.1

ENV HTTP_PROXY=http://10.10.254.219:3128
ENV HTTPS_PROXY=http://10.10.254.219:3128
ENV NO_PROXY=localhost,127.0.0.1
# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Exponer puerto 9000
EXPOSE 9000