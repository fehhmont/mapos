FROM php:8.2-apache

# 1. Instala extensões PHP e dependências do sistema
COPY --from=ghcr.io/mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apt update && apt upgrade -y \
    && apt install -y \
    wget unzip cron supervisor git \
    && install-php-extensions mysqli pdo_mysql gd zip bz2 calendar exif gettext opcache xsl intl imap sockets \
    && a2enmod rewrite

# 2. Instala o Composer (Necessário para a pasta vendor)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Configura o Apache para ler do diretório correto
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Configura o Cron
COPY cron-jobs /etc/cron.d/cron-jobs
RUN chmod 0644 /etc/cron.d/cron-jobs && crontab /etc/cron.d/cron-jobs

# 5. Copia o Código do MapOS
COPY . /var/www/html

# 6. Executa o Composer Install (Resolve o erro do autoload)
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 7. Ajusta Permissões (Crítico para rodar e para o Composer)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/assets \
    && chmod -R 775 /var/www/html/application/config

# 8. Configura o Supervisor
RUN mkdir -p /var/log/supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord"]
