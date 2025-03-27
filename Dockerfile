# Use an official PHP image from the Docker Hub
FROM php:8.1-apache

# Enable mod_rewrite by default
RUN a2enmod rewrite

# Install necessary extensions
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd && \
    docker-php-ext-install mysqli && \
    docker-php-ext-enable mysqli

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy the contents of the current directory (the app code) to the container
COPY . .

# Ensure the apache2 user has the proper permissions
RUN chown -R www-data:www-data /var/www/html

# Allow overrides with .htaccess
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
</Directory>' >> /etc/apache2/apache2.conf

# Expose port 80 (the default HTTP port for Apache)
EXPOSE 80

# Start the Apache server
CMD ["apache2-foreground"]
