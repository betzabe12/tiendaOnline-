<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si el usuario inicio sesion correctamente
if (isset($_SESSION['usuario'])) {
    echo "<h1>Bienvenido, " . htmlspecialchars($_SESSION['usuario']['nombre']) . "!</h1>";
    echo "<p>Tu correo es: " . htmlspecialchars($_SESSION['usuario']['correo']) . "</p>";
    echo "<a href='controllers/AuthController.php?action=logout'>Cerrar Sesion</a>";
} else {
    // Si no ha iniciado sesion, o si hubo un error (flash), lo mandamos de vuelta al formulario
    if (isset($_SESSION['flash'])) {
        echo "<script>alert('" . htmlspecialchars($_SESSION['flash']) . "');</script>";
        unset($_SESSION['flash']); // Limpiar el mensaje
    }
    // Redirigir al formulario principal en frontend
    header('Location: ../frontend/index.html');
    exit;
}
