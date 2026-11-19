<?php
require_once __DIR__ . '/_compras.php';
require_once __DIR__ . '/../models/Categoria.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $accion = $_POST['accion'] ?? '';
    $nombre = trim(filter_input(INPUT_POST, 'nombre') ?? '');
    $descripcion = trim(filter_input(INPUT_POST, 'descripcion') ?? '');
    $mensaje = 'Revisa el nombre y la categoría.';
    try {
        if ($accion === 'desactivar' && $id) {
            Categoria::eliminar($pdo, $id);
            $mensaje = 'Categoría desactivada.';
        } elseif ($nombre !== '' && strlen($nombre) <= 400 && strlen($descripcion) <= 2000) {
            if ($accion === 'crear') {
                Categoria::crear($pdo, $nombre, $descripcion);
                $mensaje = 'Categoría registrada.';
            } elseif ($accion === 'actualizar' && $id && Categoria::buscarPorId($pdo, $id)) {
                Categoria::actualizar($pdo, $id, $nombre, $descripcion);
                $mensaje = 'Categoría actualizada.';
            }
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $mensaje = 'No se pudo guardar la categoría.';
    }
    fc_set_flash($mensaje);
    fc_redirect('backend/admin/Categorias_admin.php');
}

$categorias = Categoria::listar($pdo);
admin_inicio('Categorías');
?>
<form method="post">
    <?php admin_token(); ?>
    <label>Nombre <input name="nombre" maxlength="100" required></label>
    <label>Descripción <input name="descripcion" maxlength="500"></label>
    <button name="accion" value="crear">Registrar categoría</button>
</form>
<h2>Categorías activas</h2>
<?php if (!$categorias): ?><p>No hay categorías activas.</p><?php endif; ?>
<?php foreach ($categorias as $categoria): ?>
    <section>
        <form method="post">
            <?php admin_token(); ?>
            <input type="hidden" name="id" value="<?= (int) $categoria['id'] ?>">
            <label>Nombre <input name="nombre" value="<?= admin_h($categoria['nombre']) ?>" maxlength="100" required></label>
            <label>Descripción <input name="descripcion" value="<?= admin_h($categoria['descripcion']) ?>" maxlength="500"></label>
            <button name="accion" value="actualizar">Guardar</button>
            <button name="accion" value="desactivar" formnovalidate>Desactivar</button>
        </form>
    </section>
<?php endforeach; ?>
</main>
</body>
</html>
