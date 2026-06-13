# Usamos una imagen oficial de PHP con Apache (igual que XAMPP)
FROM php:8.2-apache

# Copiamos todos los archivos de tu proyecto a la carpeta del servidor web
COPY . /var/www/html/

# Le damos permisos a Apache para que pueda leer y escribir en tus archivos JSON
RUN chown -W www-data:www-data /var/www/html/data || true
RUN chmod -R 755 /var/www/html/

# Exponemos el puerto 80 estándar
EXPOSE 80