FROM wordpress:php8.2-apache

# Site files (WordPress core, theme, plugins, uploads)
COPY . /var/www/html/
# wp-config that reads secrets from environment variables (no secrets in git)
COPY wp-config-render.php /var/www/html/wp-config.php

RUN a2enmod rewrite headers expires \
 && printf 'upload_max_filesize=64M\npost_max_size=64M\nmemory_limit=512M\nmax_execution_time=300\n' > /usr/local/etc/php/conf.d/site.ini \
 && chown -R www-data:www-data /var/www/html
