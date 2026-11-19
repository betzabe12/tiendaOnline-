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

## Activar Yape y probar la compra

Si la base `fashioncloud` ya existe, abre phpMyAdmin, selecciona la base y
usa la pestaña **Importar** para cargar `backend/database/agregar_pago_yape.sql`.
La actualización amplía las opciones de pago y conserva los datos existentes.
Para una instalación nueva basta con el archivo `fashioncloud.sql`.

1. Abre la tienda y agrega una prenda disponible al carrito.
2. Inicia sesión como cliente, abre el carrito e indica la dirección de entrega.
3. Elige **Yape por QR** y confirma el pedido. En **Mis pedidos** aparecerán
   el QR y el importe exacto del pedido pendiente.
4. Entra como administrador y abre **Pedidos**. Comprueba los datos del
   pedido y verifica el ingreso antes de marcarlo como confirmado.
5. En el panel también puedes gestionar categorías y actualizar el nombre
   y correo de los clientes desde **Usuarios**.

El QR es estático y el pago se verifica manualmente. Para sustituir el
QR de cobro, reemplaza `frontend/img/yape-qr.jpeg`.
