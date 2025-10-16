#!/bin/bash
cd /var/www/wr-reclutamiento || exit

echo "🚀 Ejecutando mantenimiento post-deploy..."

# Limpieza y optimización
php artisan optimize:clear
php artisan config:cache

# Reiniciar workers y supervisor
php artisan queue:restart
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart all

echo "✅ Deploy refresh completado correctamente."

