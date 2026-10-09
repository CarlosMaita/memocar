# Despliegue en serve.upsofte.com (HestiaCP)

Producción de `memocar.upsofte.com` en el VPS `162.35.113.182` (HestiaCP 1.10, usuario `upsofte`), instalación nativa igual que `contable.upsofte.com`:

- nginx de Hestia con la plantilla **laravel** (docroot → `public/`), PHP-FPM 8.5.
- MySQL: base `upsofte_memocar`, usuario `upsofte_memocar` (clave en el `.env` de producción).
- Cola `sync`, sesiones y caché en `database`. Correo por `sendmail` (reset de contraseña).
- Sin Node: el CSS compilado (`public/css/app.css`) va en el repo; se regenera con `scripts/build-css.sh` antes de commitear cambios de vistas.

Rutas:

- Producción: `/home/upsofte/web/memocar.upsofte.com/public_html` (el `.env` vive ahí; no está en el repo).
- Repo de desarrollo: `/home/upsofte/dev/memocar`.
- Repo: https://github.com/CarlosMaita/memocar (remoto `git@github-memocar:CarlosMaita/memocar.git` en ambos, con la deploy key `~upsofte/.ssh/memocar-deploy`).

## Actualizar la app

```bash
APP=/home/upsofte/web/memocar.upsofte.com/public_html
cd $APP && sudo -u upsofte git pull --ff-only && \
  sudo -u upsofte composer install --no-dev --no-interaction --optimize-autoloader && \
  sudo -u upsofte php artisan migrate --force && \
  sudo -u upsofte php artisan config:cache && sudo -u upsofte php artisan route:cache && sudo -u upsofte php artisan view:cache
```

Si se agregaron conceptos al catálogo base (`database/data/maintenance_catalog.php`):
`sudo -u upsofte php artisan db:seed --class=MaintenanceCatalogSeeder --force`.

## SSL

Activo desde 2026-10-09 (Let's Encrypt, Hestia lo renueva solo, HTTP→HTTPS forzado). Para rehacerlo, con el DNS `A memocar.upsofte.com → 162.35.113.182`:

```bash
/usr/local/hestia/bin/v-add-letsencrypt-domain upsofte memocar.upsofte.com
/usr/local/hestia/bin/v-add-web-domain-ssl-force upsofte memocar.upsofte.com
```

(Ojo: el 4º argumento de `v-add-letsencrypt-domain` es para el dominio de correo, no para redirigir.)
El `.env` de producción lleva `SESSION_SECURE_COOKIE=true`: el login no funciona por HTTP plano.

## GitHub

Ya conectado. Flujo: commit y `git push` desde `/home/upsofte/dev/memocar` y luego `git pull` en producción (sección "Actualizar la app"). La clave `memocar-deploy` está como deploy key con escritura en el repo; el alias SSH `github-memocar` vive en `~upsofte/.ssh/config`.

## Operación

- Logs: `storage/logs/laravel-*.log` y `/var/log/nginx/domains/memocar.upsofte.com.*`.
- Tests en el servidor: `php -d disable_functions= vendor/bin/pest` (Hestia deshabilita `pcntl_*`).
- Backups: Hestia respalda a diario el usuario `upsofte` (web + DB).
