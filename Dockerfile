

# construction de l'image PHP/Apache une fois (extensions, docroot et mod_rewrite activé)

FROM php:8.2-apache

# Extensions PHP
RUN docker-php-ext-install pdo pdo_mysql

# Apache: activer mod_rewrite et pointer vers/public
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer 

WORKDIR /var/www/html