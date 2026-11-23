FROM nginx:alpine

# Salin konfigurasi vhost Nginx
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Salin aset statis dan struktur web ke direktori kerja Nginx
COPY . /var/www/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]

