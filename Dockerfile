FROM thiagobarradas/lamp:php-7.4

WORKDIR /var/www/html

COPY . /var/www/html/

RUN rm -f /var/www/html/index.html && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    mkdir -p /var/www/html/upload/uploads && \
    chmod -R 777 /var/www/html/upload/uploads

EXPOSE 80
