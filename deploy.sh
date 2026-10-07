#!/usr/bin/env bash
#
# deploy.sh — Deploy del sitio Valores Real Estate en PRODUCCIÓN (codexpy.com/valores-real-estate).
#
# Se ejecuta DESDE EL SERVIDOR, dentro del propio repo:
#   cd /home/codexpy/public_html/valores-real-estate && ./deploy.sh
#
# Qué hace:
#   1. git fetch + reset --hard a origin/main → trae lo último de GitHub.
#   2. Verifica la sintaxis PHP de todos los archivos .php con el PHP 8.3 del server.
#   3. Restaura permisos: los archivos quedan a nombre del usuario web.
#
set -euo pipefail

REPO_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BRANCH="main"
WEB_USER="codexpy"
PHP_BIN="/opt/php8-3/bin/php"

cd "$REPO_DIR"

echo "→ Actualizando desde origin/$BRANCH"
git fetch --quiet origin "$BRANCH"
git reset --hard --quiet "origin/$BRANCH"
echo "  $(git log -1 --format='%h %s')"

echo "→ Verificando sintaxis PHP"
[ -x "$PHP_BIN" ] || PHP_BIN="$(command -v php)"
find . -name '*.php' -not -path './.git/*' -print0 | xargs -0 -n1 "$PHP_BIN" -l >/dev/null

echo "→ Permisos"
chown -R "$WEB_USER:$WEB_USER" "$REPO_DIR"
find "$REPO_DIR" -path "$REPO_DIR/.git" -prune -o -type d -exec chmod 755 {} +
find "$REPO_DIR" -path "$REPO_DIR/.git" -prune -o -type f -exec chmod 644 {} +
chmod 755 "$REPO_DIR/deploy.sh"

echo "✓ Deploy completo"
