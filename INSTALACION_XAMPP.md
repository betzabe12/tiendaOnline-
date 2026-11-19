# FashionCloud en XAMPP (primera corrección)

## Ubicación
1. Copia la carpeta `tiendaOnline--main` dentro de `C:\xampp\htdocs\`.
2. Inicia Apache y MySQL desde el panel de XAMPP.
3. Abre `http://localhost/phpmyadmin/`.
4. Importa `backend/database/fashioncloud.sql`.
5. Abre `http://localhost/tiendaOnline--main/frontend/`.

Si renombraste la carpeta del proyecto, utiliza ese nombre en la URL. La autenticación calcula la ruta base a partir de la carpeta `backend` o `frontend`, por lo que no requiere fijar el nombre `tiendaOnline--main`.

## Datos de prueba
- Administrador: `admin@fashioncloud.tienda` / `admin12345`
- Cliente: `cliente@fashioncloud.tienda` / `cliente12345`

Son credenciales de demostración para uso local. Cámbialas antes de publicar el sitio.

## Si ya habías importado la base de datos
En phpMyAdmin selecciona la base `fashioncloud`, abre la pestaña SQL y ejecuta el contenido de `backend/database/reparar_usuarios_demo.sql`. Esto corrige los hashes de las dos cuentas de demostración sin volver a crear las tablas.

## Qué se corrigió en esta entrega
- Se centralizó el inicio de sesión PHP y la generación de tokens CSRF en `backend/config/app.php`.
- `AuthController.php` ahora contiene solo la lógica de autenticación; el despacho está en `backend/routes/authRoutes.php`.
- Los formularios de `frontend/auth.php` envían a las rutas y contienen token CSRF.
- El cierre de sesión ahora usa POST y valida CSRF.
- Las redirecciones funcionan dentro de la carpeta del proyecto, no dependen de que la web esté instalada en la raíz del dominio.
- Los mensajes flash se muestran en la página de acceso y en la tienda.
- Las consultas de listado/búsqueda por ID de usuarios no exponen hashes de contraseñas.
- Se repararon los hashes de las cuentas demo.
- `auth.html` redirige a la pantalla oficial `auth.php` para evitar mantener dos sistemas de acceso distintos.

## Alcance de esta entrega
Esta fase corrige autenticación y rutas relacionadas. Todavía no implementa carrito, API dinámica de catálogo, panel administrativo ni flujo completo de pedidos. Esos módulos se deben abordar en fases posteriores.
