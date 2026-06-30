FROM thiagobarradas/lamp:php-7.4

WORKDIR /var/www/html

COPY . /var/www/html/

RUN rm -f /var/www/html/index.html && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    mkdir -p /var/www/html/upload/uploads && \
    chmod -R 777 /var/www/html/upload/uploads && \
    mkdir -p /var/www/html/tmp && \
    chmod -R 777 /var/www/html/tmp && \
    mkdir -p /var/log/apache2 && \
    touch /var/log/apache2/access.log && \
    touch /var/log/apache2/error.log && \
    chown -R www-data:www-data /var/log/apache2 && \
    chmod 755 /var/log/apache2 && \
    chmod 644 /var/log/apache2/access.log /var/log/apache2/error.log && \
    echo "AddType application/x-httpd-php .php3 .php4 .php5 .php7 .phtml .pht .phar" >> /etc/apache2/apache2.conf && \
    echo "<Directory /var/www/html/upload>" >> /etc/apache2/apache2.conf && \
    echo "    AllowOverride All" >> /etc/apache2/apache2.conf && \
    echo "</Directory>" >> /etc/apache2/apache2.conf && \
    echo "session.save_path = /var/www/html/tmp" >> /etc/php/7.4/apache2/php.ini && \
    a2enmod rewrite > /dev/null 2>&1 || true

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80 3306

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2ctl", "-D", "FOREGROUND"]
