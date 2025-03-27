# Use an official PHP image from the Docker Hub
FROM php:8.1-apache

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy the contents of the current directory (the app code) to the container
COPY . .

# Ensure the apache2 user has the proper permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 (the default HTTP port for Apache)
EXPOSE 80

# Start the Apache server
CMD ["apache2-foreground"]
