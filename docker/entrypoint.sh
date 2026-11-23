#!/bin/bash
set -e

# Pastikan direktori writable lokal/volume memiliki izin yang sesuai untuk user www-data
mkdir -p application/cache/sessions application/cache/ratelimit application/logs wp-content/uploads
chown -R www-data:www-data application/cache application/logs wp-content/uploads
chmod -R 775 application/cache application/logs wp-content/uploads

exec "$@"
