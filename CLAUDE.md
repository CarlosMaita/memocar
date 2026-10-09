# MemoCar

Historial de reparaciones y mantenimiento de carros con alertas del próximo servicio. Producción: https://memocar.upsofte.com (ver `docs/deploy-hestia.md`).

## Stack

- Laravel 13, PHP 8.5, Breeze (Blade), Pest. Lint con Pint (`./vendor/bin/pint`).
- **Sin Node**: el CSS se compila con el binario standalone de Tailwind v4 (`scripts/build-css.sh`: `resources/css/app.css` → `public/css/app.css`, que se commitea). Alpine se sirve desde `public/js/alpine.min.js`. Nada de CDN en runtime (un bloqueador dejó la app sin estilos). **Después de cambiar clases en vistas, correr `scripts/build-css.sh`.** No usar `@vite`.
- Diseño: tema oscuro con acento verde `#76B900` (texto negro encima). Tokens en `@theme` de `resources/css/app.css` (`bg`, `surface`, `line`, `fg`, `muted`, `accent`, estados `over`/`soon`/`ok` con `-bg`/`-line`/`-bar`/`-soft`) y clases `card`, `btn-primary`, `btn-secondary`, `pill`, `eyebrow`. Tipografías: Archivo (`font-display`), Figtree, JetBrains Mono (`font-mono`, para km y montos). Referencia: canvas https://claude.ai/artifact/39eHLGTbBempLrYYrJGyk6.
- Dev local: SQLite. Producción: MySQL/MariaDB `upsofte_memocar`. Sesión/caché en `database`, cola `sync`.
- Idioma `es` (traducciones en `lang/`, generadas con laravel-lang), zona horaria `America/Caracas` (`APP_TIMEZONE`). Moneda única: USD (helpers `money()` y `km()` en `app/helpers.php`).
- Tests: Hestia deshabilita `pcntl_*` en el CLI, por eso `php artisan test` falla. Usar `php -d disable_functions= vendor/bin/pest`.

## Dominio

- `Car` (usuario → muchos carros), `MaintenanceType` (catálogo de fallas/mantenimientos **por usuario**), `MaintenanceRecord` (registro de un carro: fecha, concepto, km, descripción, costo).
- Cada concepto tiene intervalo **recomendado** (referencia, viene del seed) y **seteado** (`set_km` / `set_months`, editable, es el que dispara alertas). null = sin límite por ese criterio; se usa "lo que ocurra primero".
- Catálogo base en `database/data/maintenance_catalog.php`. Se copia al usuario al registrarse (listener `SeedCatalogForNewUser` → `App\Actions\SeedMaintenanceCatalog`, idempotente). Tras agregar items al catálogo base: `php artisan db:seed --class=MaintenanceCatalogSeeder --force` los completa a los usuarios existentes.
- Carro seleccionado en sesión (`current_car_id`). El middleware `EnsureCarSelected` protege dashboard/registros/catálogo, comparte `$currentCar` con las vistas y lo expone con `$this->car()` en los controladores. Si el usuario tiene un solo carro se selecciona solo.
- Próximo mantenimiento: `App\Services\MaintenanceSchedule` — desde el último registro de cada concepto en el carro; estados vencido / próximo (≤30 días o ≤1.000 km) / al día.
- Guardar un registro con km mayor al del carro actualiza `cars.current_km` (hook `saved` en `MaintenanceRecord`).
- Pertenencia: los controladores devuelven 404 si el carro/registro/concepto no es del usuario (o del carro seleccionado).
