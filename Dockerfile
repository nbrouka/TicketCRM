FROM php:8.4-fpm

# Install system dependencies including Node.js
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd dom

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy custom php.ini file from the build context
COPY src/php.ini /usr/local/etc/php/conf.d/custom.ini

# Set working directory
WORKDIR /var/www


# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]