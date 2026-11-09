<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuario = $_SESSION['usuario'] ?? null;
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$modo = $_GET['modo'] ?? 'login';
if (!in_array($modo, ['login', 'register'], true)) {
    $modo = 'login';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Accede a FashionCloud y descubre nuestra colección de moda.">
    <title>Bienvenidos a FashionCloud</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <main class="auth-page">
        <a class="back-home" href="index.php">← Volver a la tienda</a>

        <section class="auth-card">
            <aside class="brand-panel">
                <div class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 100 75" role="img">
                        <path d="M26 58H18C7 58 3 50 5 41c2-9 10-14 20-12C29 9 43 2 57 7c10 3 16 11 17 22 11-1 20 6 20 16 0 8-7 13-16 13H26Z"/>
                        <path d="M50 24v16m0 0-14 12m14-12 14 12"/>
                        <path d="M36 52h28"/>
                    </svg>
                </div>
                <div class="brand-name">FashionCloud<span>.</span></div>
                <p class="brand-slogan">MODA QUE INSPIRA</p>

                <div class="brand-photo">
                    <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=900&q=85"
                         alt="Moda y compras FashionCloud">
                </div>
                <p class="brand-phrase">Viste tus sueños <span>♡</span></p>
                <p class="brand-description">Encuentra prendas que expresen tu estilo y personalidad.</p>
            </aside>

            <section class="form-panel">
                <?php if ($usuario): ?>
                    <div class="welcome-icon">✓</div>
                    <p class="eyebrow">TU CUENTA FASHIONCLOUD</p>
                    <h1>¡Hola, <?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8') ?>!</h1>
                    <p class="form-intro">Ya has iniciado sesión en tu cuenta.</p>
                    <a class="submit-button button-link" href="index.php">Ir a la tienda</a>
                    <a class="text-link logout-link" href="../backend/controllers/AuthController.php?action=logout">Cerrar sesión</a>
                <?php else: ?>
                    <div class="form-emblem" aria-hidden="true">♙</div>
                    <p class="eyebrow">TU ESTILO COMIENZA AQUÍ</p>
                    <h1>Bienvenidos a<br><span>FashionCloud</span></h1>
                    <p class="form-intro">
                        <?= $modo === 'register'
                            ? 'Crea tu cuenta y comienza a descubrir tu próximo estilo.'
                            : 'Inicia sesión y descubre lo mejor de la moda.' ?>
                    </p>

                    <?php if ($flash !== ''): ?>
                        <div class="flash-message" role="alert">
                            <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <div class="auth-tabs">
                        <a class="<?= $modo === 'login' ? 'active' : '' ?>" href="auth.php?modo=login">Iniciar sesión</a>
                        <a class="<?= $modo === 'register' ? 'active' : '' ?>" href="auth.php?modo=register">Registrarme</a>
                    </div>

                    <?php if ($modo === 'login'): ?>
                        <form action="../backend/controllers/AuthController.php?action=login" method="POST">
                            <label for="correo-login">Correo electrónico</label>
                            <div class="input-wrap">
                                <span aria-hidden="true">✉</span>
                                <input id="correo-login" type="email" name="correo"
                                       placeholder="ejemplo@correo.com" autocomplete="email" required>
                            </div>

                            <label for="password-login">Contraseña</label>
                            <div class="input-wrap">
                                <span aria-hidden="true">▣</span>
                                <input id="password-login" type="password" name="password"
                                       placeholder="Tu contraseña" autocomplete="current-password" required>
                                <button class="show-password" type="button" data-target="password-login"
                                        aria-label="Mostrar u ocultar contraseña">◉</button>
                            </div>

                            <button class="submit-button" type="submit">Ingresar a mi cuenta <span>→</span></button>
                        </form>
                        <p class="switch-copy">¿No tienes cuenta? <a href="auth.php?modo=register">Regístrate aquí</a></p>
                    <?php else: ?>
                        <form action="../backend/controllers/AuthController.php?action=register" method="POST">
                            <label for="nombre-register">Nombre completo</label>
                            <div class="input-wrap">
                                <span aria-hidden="true">♙</span>
                                <input id="nombre-register" type="text" name="nombre"
                                       placeholder="Nombre y apellido" autocomplete="name" required>
                            </div>

                            <label for="correo-register">Correo electrónico</label>
                            <div class="input-wrap">
                                <span aria-hidden="true">✉</span>
                                <input id="correo-register" type="email" name="correo"
                                       placeholder="ejemplo@correo.com" autocomplete="email" required>
                            </div>

                            <label for="password-register">Contraseña (mínimo 8 caracteres)</label>
                            <div class="input-wrap">
                                <span aria-hidden="true">▣</span>
                                <input id="password-register" type="password" name="password"
                                       placeholder="Crea una contraseña segura" minlength="8"
                                       autocomplete="new-password" required>
                                <button class="show-password" type="button" data-target="password-register"
                                        aria-label="Mostrar u ocultar contraseña">◉</button>
                            </div>

                            <button class="submit-button" type="submit">Crear mi cuenta <span>→</span></button>
                        </form>
                        <p class="switch-copy">¿Ya tienes cuenta? <a href="auth.php?modo=login">Inicia sesión</a></p>
                    <?php endif; ?>

                    <div class="trust-row">
                        <div><span>♢</span><small>Compra<br>con confianza</small></div>
                        <div><span>♡</span><small>Estilo para<br>cada ocasión</small></div>
                        <div><span>✧</span><small>Tu moda,<br>tu identidad</small></div>
                    </div>
                <?php endif; ?>
            </section>
        </section>

        <p class="copyright">© <?= date('Y') ?> FashionCloud · Moda que inspira</p>
    </main>

    <script>
    document.querySelectorAll('.show-password').forEach(function(button) {
        button.addEventListener('click', function() {
            const field = document.getElementById(button.dataset.target);
            if (!field) return;
            field.type = field.type === 'password' ? 'text' : 'password';
        });
    });
    </script>
</body>
</html>
