FROM php:8.4-apache


# Instalar extensiones necesarias de PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar mod_rewrite para URLs limpias
RUN a2enmod rewrite

# Copiar tu código a Apache
COPY public/ /var/www/html/

# Dar permisos correctos
RUN chown -R www-data:www-data /var/www/html