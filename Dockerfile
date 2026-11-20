FROM php:8.2-apache

# Instalar extensiones requeridas de PHP
RUN docker-php-ext-install pdo pdo_mysql

# Activar mod_rewrite y mod_headers en Apache
RUN a2enmod rewrite headers

# Configurar VirtualHost apuntando a /public con AllowOverride All
RUN printf '<VirtualHost *:80>\n\
    ServerName localhost\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        Options FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>\n' > /etc/apache2/sites-available/000-default.conf

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar el código del proyecto
COPY . /var/www/html/

# Asignar permisos al usuario web de Apache
RUN mkdir -p /var/www/html/data && chown -R www-data:www-data /var/www/html/data

EXPOSE 80
CMD ["apache2-foreground"]
