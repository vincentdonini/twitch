FROM bitnami/php-fpm:latest

# Installation de l'extension PDO MySQL
RUN install_packages php-mysql

# Vérification des extensions installées
RUN php -m | grep pdo_mysql
