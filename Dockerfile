FROM php:8.2-apache

# Habilita o mod_rewrite do Apache para as rotas amigáveis funcionarem
RUN a2enmod rewrite

# Instala as extensões necessárias para conectar no PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copia o código inteiro do projeto para o diretório padrão do Apache
COPY . /var/www/html/

# Configura o Apache para apontar para a pasta /public em vez da raiz do projeto
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configura permissões
RUN chown -R www-data:www-data /var/www/html
