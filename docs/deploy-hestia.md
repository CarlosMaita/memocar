# Despliegue en serve.upsofte.com (HestiaCP)

Producción de `memocar.upsofte.com` en el VPS `162.35.113.182` (HestiaCP 1.10, usuario `upsofte`), instalación nativa igual que `contable.upsofte.com`:

- nginx de Hestia con la plantilla **laravel** (docroot → `public/`), PHP-FPM 8.5.
- MySQL: base `upsofte_memocar`, usuario `upsofte_memocar` (clave en el `.env` de producción).
- Cola `sync`, sesiones y caché en `database`. Correo por `sendmail` (reset de contraseña).
- Sin Node: Tailwind/Alpine van por CDN, no hay `npm run build`.

Rutas:

- Producción: `/home/upsofte/web/memocar.upsofte.com/public_html` (el `.env` vive ahí; no está en el repo).
- Repo de desarrollo: `/home/upsofte/dev/memocar` (es el `origin` de producción hasta que exista el repo en GitHub).

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

Requiere el registro DNS `A memocar.upsofte.com → 162.35.113.182`. Luego:

```bash
/usr/local/hestia/bin/v-add-letsencrypt-domain upsofte memocar.upsofte.com "" yes
```

y en el `.env` de producción `SESSION_SECURE_COOKIE=true` + `php artisan config:cache`.

## Conectar con GitHub

1. Crear el repo vacío (ej. `CarlosMaita/memocar`).
2. Clave de despliegue (como `ml-simulador`): `sudo -u upsofte ssh-keygen -t ed25519 -f ~upsofte/.ssh/memocar-deploy -N ""`, agregar la `.pub` en *Settings → Deploy keys* con permiso de escritura, y en `~upsofte/.ssh/config`:

   ```
   Host github-memocar
     HostName github.com
     User git
     IdentityFile ~/.ssh/memocar-deploy
   ```
3. Push desde el repo de desarrollo y apuntar producción al mismo remoto:

   ```bash
   cd /home/upsofte/dev/memocar && sudo -u upsofte git remote add origin git@github-memocar:CarlosMaita/memocar.git && sudo -u upsofte git push -u origin main
   cd /home/upsofte/web/memocar.upsofte.com/public_html && sudo -u upsofte git remote set-url origin git@github-memocar:CarlosMaita/memocar.git
   ```

## Operación

- Logs: `storage/logs/laravel-*.log` y `/var/log/nginx/domains/memocar.upsofte.com.*`.
- Tests en el servidor: `php -d disable_functions= vendor/bin/pest` (Hestia deshabilita `pcntl_*`).
- Backups: Hestia respalda a diario el usuario `upsofte` (web + DB).
