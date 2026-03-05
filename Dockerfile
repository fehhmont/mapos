FROM php:8.2-apache

# 1. Instala extensões PHP e dependências
COPY --from=ghcr.io/mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apt update && apt upgrade -y \
    && apt install -y \
    wget unzip cron supervisor \
    && install-php-extensions mysqli pdo_mysql gd zip bz2 calendar exif gettext opcache xsl intl imap sockets \
    && a2enmod rewrite

# 2. Configura o Apache para ler do diretório correto
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 3. Configura o Cron (usando seu arquivo existente)
COPY cron-jobs /etc/cron.d/cron-jobs
RUN chmod 0644 /etc/cron.d/cron-jobs && crontab /etc/cron.d/cron-jobs

# 4. Copia o Código do MapOS
COPY . /var/www/html

# 5. Ajusta Permissões (Crítico para rodar)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/assets

# 6. Configura o Supervisor para rodar Apache e Cron juntos
RUN mkdir -p /var/log/supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord"]
