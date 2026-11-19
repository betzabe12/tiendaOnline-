<?php
require_once __DIR__ . '/_compras.php';
require_once __DIR__ . '/../models/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $nombre = trim(filter_input(INPUT_POST, 'nombre') ?? '');
    $correo = trim(filter_input(INPUT_POST, 'correo') ?? '');
    $mensaje = 'Revisa el nombre y el correo.';
    try {
        $usuario = $id ? Usuario::buscarPorId($pdo, $id) : null;
        if ($usuario && $usuario['rol'] === 'cliente' && $nombre !== '' && filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            Usuario::actualizar($pdo, $id, $nombre, $correo);
            $mensaje = 'Datos del cliente actualizados.';
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $mensaje = 'No se pudo guardar. Verifica que el correo esté disponible.';
    }
    fc_set_flash($mensaje);
    fc_redirect('backend/admin/Usuarios_admin.php');
}

$usuarios = Usuario::listar($pdo);
admin_inicio('Usuarios');
?>
<?php if (!$usuarios): ?><p>No hay usuarios registrados.</p><?php endif; ?>
<?php foreach ($usuarios as $usuario): ?>
    <section>
        <h2><?= admin_h($usuario['nombre']) ?> · <?= admin_h($usuario['rol']) ?></h2>
        <?php if ($usuario['rol'] === 'cliente'): ?>
            <form method="post">
                <?php admin_token(); ?>
                <input type="hidden" name="id" value="<?= (int) $usuario['id'] ?>">
                <label>Nombre <input name="nombre" value="<?= admin_h($usuario['nombre']) ?>" required></label>
                <label>Correo <input type="email" name="correo" value="<?= admin_h($usuario['correo']) ?>" required></label>
                <button>Guardar datos</button>
            </form>
        <?php else: ?>
            <p><?= admin_h($usuario['correo']) ?></p>
        <?php endif; ?>
    </section>
<?php endforeach; ?>
</main>
</body>
</html>
