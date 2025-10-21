FROM php:8.2-apache

# Instala extensões necessárias do PHP
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura o diretório de trabalho
WORKDIR /var/www/html

# Copia os arquivos do projeto
COPY . .

# Instala as dependências do Composer
RUN composer install --no-dev --optimize-autoloader

# Configura permissões
RUN chown -R www-data:www-data /var/www/html

# Habilita o mod_rewrite do Apache
RUN a2enmod rewrite

# Configura o Apache para usar a pasta public como DocumentRoot
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's|<Directory /var/www/>|<Directory /var/www/html/public>|' /etc/apache2/apache2.conf && \
    sed -i 's|AllowOverride None|AllowOverride All|' /etc/apache2/apache2.conf

# Expõe a porta 80
EXPOSE 80

# Executa as migrations e inicia o Apache
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
