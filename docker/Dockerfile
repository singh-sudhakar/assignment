FROM php:8.2-fpm

# Set working directory
WORKDIR /patient-management

# Install system dependencies
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
    vim \
    default-mysql-client \
    libcurl4-openssl-dev \
    libicu-dev \
    libxslt-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libjpeg-dev \
    libpng-dev \
    libmemcached-dev \
    libssl-dev \
 && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js 18 (LTS)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Copy application code
COPY . .

# Set permissions
RUN chown -R www-data:www-data /patient-management \
 && chmod -R ug+w /patient-management/storage /patient-management/bootstrap/cache

# Optional: install JS deps and build
# You can skip here if done in entrypoint
# RUN npm install && npm run dev

# Expose PHP port
EXPOSE 9000

# Entrypoint
COPY docker/migration.sh /migration.sh
RUN chmod +x /migration.sh
ENTRYPOINT ["/migration.sh"]