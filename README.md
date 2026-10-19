# Valores Real Estate

Sitio público y CMS de Valores Real Estate: Laravel 12 + Filament 4.
Producción: https://codexpy.com/valores-real-estate (CMS en `/admin`).
El sitio PHP original está en el tag `v1-vanilla`.

## Local

```bash
composer install
cp .env.example .env   # DB local: valoresrealestate, root sin contraseña
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan db:seed --class=LegacyContentSeeder   # contenido y medios originales
php artisan make:filament-user
```

URL local: http://localhost/valores-real-estate/public (Apache de Homebrew; la raíz del proyecto está bloqueada por `.htaccess`).

Tests: `php artisan test`

## Contenido

- Todo se edita desde el CMS: proyectos (galería, tipologías, lotes/unidades), noticias, plana directiva, asesoras, textos de cada página y configuración (WhatsApp, email/SMTP, redes, anti-spam).
- Lotes y unidades: se editan en cada proyecto o se importan desde la planilla del cliente (botón "Importar planilla"). También por consola: `php artisan lots:import lotes.xlsx --dry-run`. **La planilla trae precios y cuentas: no se versiona y no se guarda.**
- Los formularios se guardan siempre en "Mensajes recibidos". Investor Pass continúa por WhatsApp.

## Deploy

```bash
deploy/deploy-ferozo.sh
```

Necesita `.deploy.env` (credenciales SSH) y, sólo en el primer deploy, `deploy/.env.production.local`. Ninguno se versiona.
La app vive en `/home/codexpy/valores-real-estate-app` (fuera de `public_html`); `public_html/valores-real-estate` sólo tiene el front controller y symlinks.
El deploy nunca toca `storage/app` (archivos subidos desde el CMS y CV). Backups: antes de cada deploy y cada noche (`deploy/backup-nightly.sh`, cron de root, 14 días) en `/home/codexpy/backups/valores-real-estate`.
