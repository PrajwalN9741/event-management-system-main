FROM php:8.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql pdo_sqlite mbstring zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install Composer dependencies
RUN if [ -f composer.json ]; then \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer; \
    composer install --no-interaction --optimize-autoloader; \
    fi

# Set permissions for the instance folder (for SQLite)
RUN mkdir -p instance && chown -R www-data:www-data instance && chmod -R 775 instance

# Configure PHP (optional, can be done via .htaccess or custom ini)
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Set Apache to use index.php as the directory index
RUN sed -i 's/DirectoryIndex index.html index.cgi index.pl index.php index.xhtml index.htm/DirectoryIndex index.php index.html/' /etc/apache2/mods-enabled/dir.conf

EXPOSE 80
