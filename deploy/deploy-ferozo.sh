#!/usr/bin/env bash
#
# Despliegue de Valores Real Estate (Laravel + Filament) al VPS de codexpy.com, donde el sitio vive
# en una subcarpeta: https://codexpy.com/valores-real-estate
#
# Se ejecuta DESDE LA MÁQUINA LOCAL (adaptado de ciabaymkt/deploy/deploy-ferozo.sh):
#   1. rsync del código a $APP_DIR (fuera de public_html). NUNCA toca storage/app: ahí están las
#      imágenes subidas desde el CMS y los CV de las postulaciones.
#   2. .env inicial (sólo si el servidor no tiene uno).
#   3. backup de la base, composer, migraciones y cachés.
#   4. carpeta pública $PUBLIC_DIR: index.php que apunta a la app, .htaccess, .user.ini y symlinks.
#
# Uso:
#   deploy/deploy-ferozo.sh                 # producción
#   PUBLIC_DIR=/home/codexpy/public_html/vre-staging deploy/deploy-ferozo.sh   # otra carpeta pública
#
# Variables (con default): DEPLOY_HOST, DEPLOY_PORT, DEPLOY_USER, APP_DIR, PUBLIC_DIR, OWNER, PHP_BIN, ENV_FILE.
# Contraseña SSH: se toma de DEPLOY_PASS en .deploy.env (no versionado) y se usa con sshpass.

set -euo pipefail
cd "$(dirname "$0")/.."

[ -f .deploy.env ] && . ./.deploy.env

DEPLOY_HOST="${DEPLOY_HOST:-200.58.105.211}"
DEPLOY_PORT="${DEPLOY_PORT:-5221}"
DEPLOY_USER="${DEPLOY_USER:-root}"
APP_DIR="${APP_DIR:-/home/codexpy/valores-real-estate-app}"
PUBLIC_DIR="${PUBLIC_DIR:-/home/codexpy/public_html/valores-real-estate}"
OWNER="${OWNER:-codexpy:codexpy}"
PHP_BIN="${PHP_BIN:-/opt/ferozo/php8-3/bin/php-cli}"
ENV_FILE="${ENV_FILE:-deploy/.env.production.local}"
BACKUP_DIR="${BACKUP_DIR:-/home/codexpy/backups/valores-real-estate}"

SSH_OPTS=(-p "$DEPLOY_PORT" -o StrictHostKeyChecking=accept-new -o PubkeyAuthentication=no)
if [ -n "${DEPLOY_PASS:-}" ]; then
  export SSHPASS="$DEPLOY_PASS"
  SSH=(sshpass -e ssh "${SSH_OPTS[@]}")
  RSYNC_RSH="sshpass -e ssh ${SSH_OPTS[*]}"
else
  SSH=(ssh "${SSH_OPTS[@]}")
  RSYNC_RSH="ssh ${SSH_OPTS[*]}"
fi
REMOTE="$DEPLOY_USER@$DEPLOY_HOST"

echo "==> 1/5 rsync del código a $REMOTE:$APP_DIR"
"${SSH[@]}" "$REMOTE" "mkdir -p '$APP_DIR' && chown $OWNER '$APP_DIR'"
rsync -rlptDz --delete -e "$RSYNC_RSH" --exclude-from=deploy/rsync-exclude ./ "$REMOTE:$APP_DIR/"
"${SSH[@]}" "$REMOTE" "chown -R $OWNER '$APP_DIR'"

echo "==> 2/5 .env"
if "${SSH[@]}" "$REMOTE" "test -f '$APP_DIR/.env'"; then
  echo "    el servidor ya tiene .env: se conserva"
else
  [ -f "$ENV_FILE" ] || { echo "    falta $ENV_FILE para el primer despliegue"; exit 1; }
  rsync -az -e "$RSYNC_RSH" "$ENV_FILE" "$REMOTE:$APP_DIR/.env"
  "${SSH[@]}" "$REMOTE" "chown $OWNER '$APP_DIR/.env' && chmod 640 '$APP_DIR/.env'"
  echo "    .env inicial subido"
fi

echo "==> 3/5 backup de la base, composer, migraciones y cachés"
# El PHP CLI de Ferozo sólo lo puede ejecutar root; al final se devuelve la propiedad a $OWNER.
"${SSH[@]}" "$REMOTE" bash -s <<REMOTE_SCRIPT
set -euo pipefail
cd '$APP_DIR'
mkdir -p '$BACKUP_DIR' && chmod 700 '$BACKUP_DIR'
DB=\$(grep '^DB_DATABASE=' .env | cut -d= -f2- | tr -d '"')
if [ -n "\$DB" ] && mysql -e "use \$DB" 2>/dev/null; then
  (umask 077; mysqldump --single-transaction "\$DB" | gzip > '$BACKUP_DIR'/pre-deploy-\$(date +%Y%m%d-%H%M%S).sql.gz)
  ls -1t '$BACKUP_DIR'/pre-deploy-*.sql.gz | tail -n +11 | xargs -r rm -f
fi
export COMPOSER_ALLOW_SUPERUSER=1 COMPOSER_HOME=/root/.composer
$PHP_BIN /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --quiet
grep -q '^APP_KEY=.\+' .env || $PHP_BIN artisan key:generate --force --quiet
$PHP_BIN artisan migrate --force
$PHP_BIN artisan optimize:clear --quiet
$PHP_BIN artisan config:cache --quiet
# Sin route:cache: con el sitio en subcarpeta, el matcher compilado responde 405 en la raíz ("/vre-staging/").
$PHP_BIN artisan view:cache --quiet
$PHP_BIN artisan filament:optimize --quiet
mkdir -p storage/app/public storage/app/private
chown -R $OWNER .
chmod -R ug+rwX storage bootstrap/cache
REMOTE_SCRIPT

echo "==> 4/5 carpeta pública $PUBLIC_DIR"
"${SSH[@]}" "$REMOTE" bash -s <<REMOTE_SCRIPT
set -euo pipefail
mkdir -p '$PUBLIC_DIR'
# .htaccess de Laravel sin la directiva Require (sólo hace falta en el entorno local)
grep -v '^Require all granted' '$APP_DIR/public/.htaccess' | grep -v 'Única carpeta accesible' > '$PUBLIC_DIR/.htaccess'
cat > '$PUBLIC_DIR/.user.ini' <<'INI'
upload_max_filesize = 110M
post_max_size = 120M
max_execution_time = 120
memory_limit = 512M
INI
# Archivos estáticos de la app (assets del sitio, Filament, Livewire publicados) y medios del CMS.
for entry in assets css js fonts vendor favicon.ico robots.txt; do
  [ -e '$APP_DIR/public/'\$entry ] && ln -sfn '$APP_DIR/public/'\$entry '$PUBLIC_DIR/'\$entry
done
ln -sfn '$APP_DIR/storage/app/public' '$PUBLIC_DIR/storage'
cat > '$PUBLIC_DIR/index.php' <<'PHP'
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// La aplicación vive fuera de public_html; esta carpeta sólo expone el front controller.
\$app = '$APP_DIR';

if (file_exists(\$maintenance = \$app.'/storage/framework/maintenance.php')) {
    require \$maintenance;
}

require \$app.'/vendor/autoload.php';

(require_once \$app.'/bootstrap/app.php')
    ->handleRequest(Request::capture());
PHP
chown -R $OWNER '$PUBLIC_DIR'
chown -h $OWNER '$PUBLIC_DIR'/* 2>/dev/null || true
REMOTE_SCRIPT

echo "==> 5/5 comprobación"
URL="${CHECK_URL:-$(grep '^APP_URL=' "$ENV_FILE" 2>/dev/null | cut -d= -f2- | tr -d '"')}"
for path in /up / /proyectos/cumbres /admin/login; do
  curl -s -o /dev/null -w "    $URL$path -> %{http_code}\n" "$URL$path"
done
echo "Listo."
