FROM php:8.2-apache

# Instalar extensiones requeridas de PHP
RUN docker-php-ext-install pdo pdo_mysql

# Activar mod_rewrite y mod_headers en Apache para las rutas amigables
RUN a2enmod rewrite headers

# Configurar Apache para servir desde la carpeta /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar el código del proyecto
COPY . /var/www/html/

# Asignar permisos al usuario web de Apache
RUN mkdir -p /var/www/html/data && chown -R www-data:www-data /var/www/html/data

EXPOSE 80
CMD ["apache2-foreground"]
