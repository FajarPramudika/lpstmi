FROM php:7.3-fpm-buster

# Install PHP extension installer helper
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install ekstensi wajib sesuai kebutuhan proyek
RUN install-php-extensions \
    mysqli \
    gd \
    fileinfo \
    mbstring \
    iconv \
    opcache

# Salin konfigurasi kustom PHP dan PHP-FPM
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

# Salin skrip entrypoint dan setup
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/setup.sh /usr/local/bin/setup.sh
RUN chmod +x /usr/local/bin/entrypoint.sh /usr/local/bin/setup.sh

# Set working directory
WORKDIR /var/www/html

# Salin seluruh kode sumber aplikasi ke dalam image (Self-Contained)
COPY . /var/www/html

# Siapkan direktori writable awal dengan kepemilikan www-data
RUN mkdir -p application/cache/sessions application/cache/ratelimit application/logs wp-content/uploads \
    && chown -R www-data:www-data application/cache application/logs wp-content/uploads \
    && chmod -R 775 application/cache application/logs wp-content/uploads

# Expose port FastCGI
EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm", "-F"]
