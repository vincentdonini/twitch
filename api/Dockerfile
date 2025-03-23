# Utiliser PHP 8.1 avec FPM
FROM php:8.1-fpm

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev unzip \
    && docker-php-ext-install pdo pdo_mysql zip

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/symfony

# Copier les fichiers du projet
COPY . /var/www/symfony

# Installer les dépendances Symfony
RUN composer install --no-interaction --optimize-autoloader

# Donner les bonnes permissions
RUN chown -R www-data:www-data /var/www/symfony

# Exposer le port de PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
