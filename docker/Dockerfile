# docker/Dockerfile

FROM php:8.2-fpm

# Set working directory
WORKDIR /patient-management

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    zip \
    unzip \
    git \
    libzip-dev \
    vim

# PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing Laravel app
COPY . /patient-management

# Set permissions
RUN chown -R www-data:www-data /patient-management \
    && chmod -R 755 /patient-management/storage

# Expose port
EXPOSE 9000

COPY docker/migration.sh /migration.sh
RUN chmod +x /migration.sh
ENTRYPOINT ["/migration.sh"]

