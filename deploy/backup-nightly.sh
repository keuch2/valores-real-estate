#!/usr/bin/env bash
#
# Backup nocturno de Valores Real Estate en el servidor (cron de root, 03:15):
# base de datos, archivos subidos desde el CMS (imágenes, videos, PDFs, CV) y .env (contiene APP_KEY,
# necesaria para leer la contraseña SMTP y la clave de reCAPTCHA guardadas encriptadas).
# Conserva 14 días. Instalación: ver deploy/README o el cron de root.

set -euo pipefail

APP_DIR="${APP_DIR:-/home/codexpy/valores-real-estate-app}"
BACKUP_DIR="${BACKUP_DIR:-/home/codexpy/backups/valores-real-estate}"
KEEP_DAYS="${KEEP_DAYS:-14}"
STAMP="$(date +%Y%m%d-%H%M)"

mkdir -p "$BACKUP_DIR"
chmod 700 "$BACKUP_DIR"
umask 077

DB="$(grep '^DB_DATABASE=' "$APP_DIR/.env" | cut -d= -f2- | tr -d '"')"
mysqldump --single-transaction --routines "$DB" | gzip > "$BACKUP_DIR/db-$STAMP.sql.gz"
tar -czf "$BACKUP_DIR/files-$STAMP.tar.gz" -C "$APP_DIR" storage/app/public storage/app/private .env

find "$BACKUP_DIR" -maxdepth 1 \( -name 'db-*.sql.gz' -o -name 'files-*.tar.gz' \) -mtime +"$KEEP_DAYS" -delete
