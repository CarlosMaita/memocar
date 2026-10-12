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

## Correo

- La app envía por SMTP al exim local de Hestia: `MAIL_MAILER=smtp`, `MAIL_HOST=127.0.0.1`, `MAIL_PORT=25`, `MAIL_SCHEME=smtp`, `MAIL_EHLO_DOMAIN=serve.upsofte.com`, `MAIL_VERIFY_PEER=false` (el certificado de exim es del hostname, no de 127.0.0.1). No usar `sendmail`: PHP-FPM tiene `proc_open` deshabilitado.
- Remitente `no-reply@upsofte.com` (cuenta creada en el dominio de correo de Hestia para que exim acepte el remitente).
- El proveedor bloquea el puerto 25 de salida: exim entrega todo por el **relay SMTP** de Hestia (`/etc/exim4/smtp_relay.conf`, configurable en el panel: Configuración del servidor → Correo → SMTP relay).
- DNS recomendados para `upsofte.com`: TXT `mail._domainkey` (DKIM, sale de `v-list-mail-domain-dkim-dns upsofte upsofte.com`) y TXT `_dmarc` = `v=DMARC1; p=none`.
- Acceso por código: `LOGIN_CODES_ENABLED=true` en `.env` + `php artisan config:cache`. Activarlo solo cuando el envío funcione.
- Probar: `sudo -u upsofte php artisan tinker --execute 'Mail::raw("prueba", fn ($m) => $m->to("tu@correo.com")->subject("MemoCar prueba"));'` y revisar `/var/log/exim4/mainlog`.

## Operación

- Logs: `storage/logs/laravel-*.log` y `/var/log/nginx/domains/memocar.upsofte.com.*`.
- Tests en el servidor: `php -d disable_functions= vendor/bin/pest` (Hestia deshabilita `pcntl_*`).
- Backups: Hestia respalda a diario el usuario `upsofte` (web + DB).
