# MemoCar

Bitácora web de reparaciones y mantenimiento de tus carros: registra cada servicio (fecha, concepto, kilometraje, descripción y costo en USD) y MemoCar te avisa cuándo toca el próximo.

**Producción:** https://memocar.upsofte.com

## Funciones

- Registro e inicio de sesión (con recuperación de contraseña).
- Varios carros por usuario; al entrar eliges el carro a revisar y todo (dashboard, registros, alertas) se muestra para ese carro.
- Catálogo de ~38 fallas/mantenimientos comunes (aceite, frenos, correa de tiempo, cauchos, empacaduras, batería…) agrupados por categoría, con intervalo **recomendado** (referencia común) e intervalo **seteado** (el que usa la app para alertar, editable por el usuario). Se pueden agregar conceptos propios.
- Dashboard por carro: kilometraje, gastos del año y total, último servicio y tabla de próximos mantenimientos (vencido / próximo / al día) calculada desde el último registro de cada tipo, por km y por fecha (lo que ocurra primero).
- Historial filtrable por concepto y año, con total gastado.

## Stack

Laravel 13 · Blade (Breeze) · Tailwind v4 compilado con el binario standalone (`scripts/build-css.sh`, sin Node) · Alpine · MySQL/MariaDB · Pest.

## Desarrollo local

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed     # en APP_ENV=local crea demo@memocar.test / password
php artisan serve
```

Tests: `php artisan test` (en el servidor Hestia: `php -d disable_functions= vendor/bin/pest`).

## Despliegue

Ver [docs/deploy-hestia.md](docs/deploy-hestia.md).
