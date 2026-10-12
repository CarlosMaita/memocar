# Administración

Panel de estadísticas generales en **https://memocar.upsofte.com/admin** (pestaña **Admin** del menú, visible solo para administradores).

## Qué muestra

- **Totales**: usuarios registrados (+ últimos 30 días y cuántos tienen carros), carros, registros cargados y gasto registrado.
- **Registros cargados por mes** (últimos 12 meses, por fecha de carga).
- **Espectro de fallas y mantenimientos**: ranking de los conceptos más registrados con su participación (%), acumulado (Pareto: cuántos conceptos concentran el 80 % de los registros), costo promedio y en cuántos carros aparece. Los conceptos se agrupan por nombre (el catálogo es por usuario).
- **Espectrograma por categoría**: matriz categoría × mes (fecha del servicio); más intenso = más registros.
- **Registros por categoría** y **últimos usuarios**.

Por defecto excluye las cuentas demo ("Ver como demo"); el botón **Incluir demo** las suma.

## Cuenta de administrador

- Usuario: `admin@upsofte.com`
- **Contraseña**: no está en el repositorio (es público). Está en el servidor en
  `/home/upsofte/web/memocar.upsofte.com/private/admin-credentials.md` (fuera de la web, solo root/upsofte).

Crear otro administrador, promover a un usuario existente o cambiar la contraseña:

```bash
cd /home/upsofte/web/memocar.upsofte.com/public_html
sudo -u upsofte php artisan memocar:admin admin@upsofte.com                      # genera y muestra una contraseña nueva
sudo -u upsofte php artisan memocar:admin otro@correo.com --password='...'       # contraseña elegida
```

El administrador también es un usuario normal: puede registrar sus propios carros.
