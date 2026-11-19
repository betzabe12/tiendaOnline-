
<?php

require_once __DIR__ . '/../models/Producto.php';

class ProductoController
{
    /**
     * Registrar un producto nuevo.
     */
    public static function crear(PDO $pdo): void
    {
        if (($_SESSION['usuario']['rol'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Acceso denegado.');
        }

        $codigo = trim($_POST['codigo'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = filter_var($_POST['precio'] ?? null, FILTER_VALIDATE_FLOAT);
        $stock = filter_var($_POST['stock'] ?? null, FILTER_VALIDATE_INT);
        $talla = trim($_POST['talla'] ?? '');
        $color = trim($_POST['color'] ?? '');
        $imagen = trim($_POST['imagen_url'] ?? '');

        $categoria = ($_POST['categoria_id'] ?? '') !== ''
            ? filter_var($_POST['categoria_id'], FILTER_VALIDATE_INT)
            : null;

        // Validar los campos obligatorios.
        if (
            $codigo === '' ||
            $nombre === '' ||
            $precio === false ||
            $precio < 0 ||
            $stock === false ||
            $stock < 0 ||
            ($categoria !== null && ($categoria === false || $categoria <= 0))
        ) {
            fc_set_flash(
                'Verifica el código, nombre, precio, stock y categoría.',
                'error'
            );

            fc_redirect('backend/admin/Productos_admin.php');
        }

        $datos = [
            'codigo' => $codigo,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => $precio,
            'stock' => $stock,
            'talla' => $talla,
            'color' => $color,
            'imagen_url' => $imagen,
            'categoria_id' => $categoria
        ];

        try {
            Producto::crear($pdo, $datos);

            fc_set_flash(
                'Producto registrado correctamente.',
                'success'
            );

        } catch (PDOException $e) {
            error_log('Error al registrar producto: ' . $e->getMessage());

            fc_set_flash(
                'No se pudo registrar el producto. Verifica que el código no esté repetido y que la categoría exista.',
                'error'
            );
        }

        fc_redirect('backend/admin/Productos_admin.php');
    }

    /**
     * Eliminar un producto mediante eliminación lógica.
     */
    public static function eliminar(PDO $pdo): void
    {
        if (($_SESSION['usuario']['rol'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Acceso denegado.');
        }

        $id = filter_var(
            $_POST['id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if ($id === false || $id === null || $id <= 0) {
            fc_set_flash('El identificador del producto no es válido.', 'error');
            fc_redirect('backend/admin/Productos_admin.php');
        }

        try {
            Producto::eliminar($pdo, $id);

            fc_set_flash(
                'Producto eliminado correctamente.',
                'success'
            );

        } catch (PDOException $e) {
            error_log('Error al eliminar producto: ' . $e->getMessage());

            fc_set_flash(
                'No se pudo eliminar el producto.',
                'error'
            );
        }

        fc_redirect('backend/admin/Productos_admin.php');
    }
}
