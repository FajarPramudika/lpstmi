#!/bin/bash
set -e

echo "======================================================"
echo "    Inisialisasi Database Politeknik STMI Jakarta     "
echo "======================================================"

echo "1. Menjalankan migrasi struktur database..."
php index.php tools migrate

echo "2. Memuat seed konten awal (post, halaman, layout)..."
php index.php tools import_seed || true

echo "3. Membuat akun administrator awal (slug author: admin_tro)..."
ADMIN_USER="${ADMIN_USERNAME:-admin}"
php index.php tools set_login admin_tro "$ADMIN_USER" admin

echo "======================================================"
echo "Inisialisasi selesai! Simpan password admin di atas."
echo "======================================================"

