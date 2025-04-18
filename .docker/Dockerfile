# Use a stable PHP base image
FROM php:8.4-fpm

# Set working directory
WORKDIR /var/www/html/

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    libzip-dev \
    unzip \
    git \
    libonig-dev \
    curl \
    mariadb-client \
    supervisor \
    cron \
    imagemagick \
    libmagickwand-dev \
    libmagickcore-dev \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@11.0.0

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Redis extension
RUN curl -fsSL https://github.com/FriendsOfPHP/pickle/releases/latest/download/pickle.phar --output /usr/bin/pickle \
    && chmod +x /usr/bin/pickle \
    && pecl install igbinary && docker-php-ext-enable igbinary \
    && pecl install redis && docker-php-ext-enable redis

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install gd
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install Imagick extension (fixed)
# RUN pecl install imagick-3.7.0 && docker-php-ext-enable imagick

# Create a non-root user for security
RUN useradd -u 1000 docker_app_user
RUN adduser www-data docker_app_user
RUN mkdir /home/docker_app_user
RUN chown -R docker_app_user:docker_app_user /home/docker_app_user

# Add Laravel scheduler to cron
RUN echo "* * * * * root php /var/www/html/artisan schedule:run >> /var/www/html/storage/logs/cron.log 2>&1" >> /etc/crontab

# Copy and set up entrypoint script
COPY ./start-container.sh /usr/bin/start-container
RUN chmod +x /usr/bin/start-container

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Set entrypoint
ENTRYPOINT ["/usr/bin/start-container"]
