<?php
require_once __DIR__ . '/../models/Producto.php';

class ProductoController {
    public static function crear(PDO $pdo): void {
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
        $categoria = ($_POST['categoria_id'] ?? '') !== '' ? (int)$_POST['categoria_id'] : null;

        if ($codigo === '' || $nombre === '' || $precio === false || $precio < 0 || $stock === false || $stock < 0) {
            $_SESSION['flash'] = 'Verifica el código, nombre, precio y stock.';
            header('Location: ../views/admin.php');
            exit;
        }
        $stmt = $pdo->prepare('INSERT INTO productos (codigo,nombre,descripcion,precio,stock,talla,color,imagen_url,categoria_id) VALUES (?,?,?,?,?,?,?,?,?)');
        $stmt->execute([$codigo, $nombre, $descripcion ?: null, $precio, $stock, $talla ?: null, $color ?: null, $imagen ?: null, $categoria]);
        $_SESSION['flash'] = 'Producto registrado correctamente.';
        header('Location: ../views/admin.php');
        exit;
    }

    public static function eliminar(PDO $pdo): void {
        if (($_SESSION['usuario']['rol'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Acceso denegado.');
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $stmt = $pdo->prepare('UPDATE productos SET estado = 0 WHERE id = ?');
            $stmt->execute([$id]);
        }
        header('Location: ../views/admin.php');
        exit;
    }
}

