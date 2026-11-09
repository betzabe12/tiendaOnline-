
<?php
// ======================================================
// FASHIONCLOUD - CONTROL DE SESIÓN
// ======================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuario = $_SESSION['usuario'] ?? null;

$nombreUsuario = null;
$rolUsuario = null;

if (is_array($usuario)) {
    if (isset($usuario['nombre'])) {
        $nombreUsuario = htmlspecialchars(
            (string) $usuario['nombre'],
            ENT_QUOTES,
            'UTF-8'
        );
    }

    $rolUsuario = htmlspecialchars(
        (string) ($usuario['rol'] ?? 'cliente'),
        ENT_QUOTES,
        'UTF-8'
    );
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>FashionCloud — Tienda de Ropa Online</title>

    <meta name="description" content="FashionCloud es tu tienda virtual de moda contemporánea. Consulta precios, tallas, colores y disponibilidad.">
    <meta name="keywords" content="moda, ropa online, boutique, fashioncloud, prendas, pantalones, vestidos">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/landing.css">

    <style>
        .user-menu {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .user-welcome {
            color: #182033;
            font-size: 14px;
            white-space: nowrap;
        }

        .user-welcome strong {
            color: #c69a35;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .user-menu {
                gap: 8px;
            }

            .user-welcome {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>

    <!-- =====================================================
         1. BARRA DE NAVEGACIÓN
    ====================================================== -->

    <header class="site-header">
        <div class="container header-container">

            <a href="#hero" class="brand-logo" aria-label="FashionCloud Inicio">
                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/>
                    <path d="M12 12v3"/>
                    <path d="m9 15 3-3 3 3"/>
                </svg>

                <span>FashionCloud<span class="brand-dot">.</span></span>
            </a>

            <nav>
                <ul class="nav-links" id="navLinks">
                    <li><a href="#hero" class="nav-link active">Inicio</a></li>
                    <li><a href="#coleccion" class="nav-link">Colección</a></li>
                    <li><a href="#categorias" class="nav-link">Categorías</a></li>
                    <li><a href="#beneficios" class="nav-link">Beneficios</a></li>
                    <li><a href="#nosotros" class="nav-link">Nosotros</a></li>
                    <li><a href="#contacto" class="nav-link">Contacto</a></li>
                </ul>
            </nav>

            <div class="header-actions">

                <?php if ($usuario && $nombreUsuario !== null): ?>

                    <div class="user-menu">

                        <span class="user-welcome">
                            Hola, <strong><?php echo $nombreUsuario; ?></strong>
                        </span>

                        <a href="../backend/index.php"
                           class="btn btn-outline btn-sm">
                            Mi cuenta
                        </a>

                        <a href="../backend/controllers/AuthController.php?action=logout"
                           class="btn btn-primary btn-sm">
                            Cerrar sesión
                        </a>

                    </div>

                <?php else: ?>

                    <!-- AHORA ABREN LA NUEVA PANTALLA AUTH.PHP -->

                    <a href="auth.php?modo=login"
                       class="btn btn-outline btn-sm">
                        Iniciar Sesión
                    </a>

                    <a href="auth.php?modo=register"
                       class="btn btn-primary btn-sm">
                        Registrarse
                    </a>

                <?php endif; ?>

                <button type="button"
                        class="mobile-toggle"
                        id="mobileMenuBtn"
                        aria-label="Abrir menú de navegación">
                    ☰
                </button>

            </div>
        </div>
    </header>


    <main>

        <!-- =====================================================
             2. PORTADA PRINCIPAL
        ====================================================== -->

        <section class="hero-section" id="hero">
            <div class="container">

                <div class="hero-grid">

                    <div class="hero-content">

                        <div class="hero-badge">
                            <span class="hero-badge-dot"></span>
                            Nueva Temporada 2026 • Catálogo Activo
                        </div>

                        <h1 class="hero-title">
                            Moda contemporánea que define tu
                            <span class="highlight">estilo</span> y presencia.
                        </h1>

                        <p class="hero-description">
                            Explora nuestra selección exclusiva de prendas.
                            Consulta disponibilidad de tallas, colores y
                            existencias en tiempo real y realiza tus pedidos
                            de forma directa y transparente.
                        </p>

                        <div class="hero-buttons">

                            <a href="#coleccion" class="btn btn-accent">
                                Explorar Colección

                                <svg width="18" height="18"
                                     viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2.5">
                                    <path d="m9 18 6-6-6-6"/>
                                </svg>
                            </a>

                            <!-- CAMBIO: ENLACE A AUTH.PHP -->

                            <a href="auth.php?modo=login"
                               class="btn btn-outline">
                                Acceder a mi Cuenta
                            </a>

                        </div>

                        <div class="hero-meta">

                            <div class="meta-item">
                                <span class="meta-number">100%</span>
                                <span class="meta-label">Disponibilidad en línea</span>
                            </div>

                            <div class="meta-item">
                                <span class="meta-number">24/7</span>
                                <span class="meta-label">Consulta de catálogo</span>
                            </div>

                            <div class="meta-item">
                                <span class="meta-number">Directo</span>
                                <span class="meta-label">Gestión de pedidos</span>
                            </div>

                        </div>
                    </div>

                    <div class="hero-visual">

                        <div class="hero-image-wrapper">
                            <img
                                src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=800&q=80"
                                alt="Modelo vistiendo moda contemporánea FashionCloud"
                                loading="eager">
                        </div>

                        <div class="hero-floating-card">
                            <div class="floating-icon">✦</div>

                            <div class="floating-text">
                                <h4>Inventario Verificado</h4>
                                <p>
                                    Control de stock en tiempo real según
                                    catálogo del sistema.
                                </p>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </section>


        <!-- =====================================================
             3. CATEGORÍAS
        ====================================================== -->

        <section class="categories-section" id="categorias">
            <div class="container">

                <div class="section-header">
                    <span class="section-tag">Colecciones</span>

                    <h2 class="section-title">Nuestras Categorías</h2>

                    <p class="section-subtitle">
                        Prendas diseñadas para adaptarse a tu ritmo diario,
                        combinando comodidad, estética y confección de calidad.
                    </p>
                </div>

                <div class="categories-grid">

                    <div class="category-card"
                         onclick="filtrarPorCategoria('superiores')">

                        <img
                            src="https://images.unsplash.com/photo-1598554747436-c9293d6a588f?auto=format&fit=crop&w=600&q=80"
                            alt="Prendas Superiores">

                        <div class="category-overlay">
                            <h3>Prendas Superiores</h3>
                            <span>Ver blusas y camisas →</span>
                        </div>
                    </div>

                    <div class="category-card"
                         onclick="filtrarPorCategoria('pantalones')">

                        <img
                            src="https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=600&q=80"
                            alt="Pantalones y Jeans">

                        <div class="category-overlay">
                            <h3>Pantalones & Jeans</h3>
                            <span>Ver cortes y estilos →</span>
                        </div>
                    </div>

                    <div class="category-card"
                         onclick="filtrarPorCategoria('vestidos')">

                        <img
                            src="https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=600&q=80"
                            alt="Vestidos y Faldas">

                        <div class="category-overlay">
                            <h3>Vestidos & Faldas</h3>
                            <span>Ver prendas fluidas →</span>
                        </div>
                    </div>

                    <div class="category-card"
                         onclick="filtrarPorCategoria('abrigos')">

                        <img
                            src="https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=600&q=80"
                            alt="Abrigos y Chaquetas">

                        <div class="category-overlay">
                            <h3>Abrigos & Blazers</h3>
                            <span>Ver sastrería exterior →</span>
                        </div>
                    </div>

                    <div class="category-card"
                         onclick="filtrarPorCategoria('accesorios')">

                        <img
                            src="https://images.unsplash.com/photo-1624222247344-550fb60583dc?auto=format&fit=crop&w=600&q=80"
                            alt="Accesorios de Moda">

                        <div class="category-overlay">
                            <h3>Accesorios</h3>
                            <span>Ver complementos →</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>


        <!-- =====================================================
             4. COLECCIÓN DESTACADA
        ====================================================== -->

        <section class="collection-section" id="coleccion">
            <div class="container">

                <div class="section-header">
                    <span class="section-tag">Catálogo de Productos</span>

                    <h2 class="section-title">Colección Destacada</h2>

                    <p class="section-subtitle">
                        Consulta prendas, tallas, colores y precios.
                        Cada prenda responde a la estructura de datos de FashionCloud.
                    </p>
                </div>

                <div class="catalog-notice">

                    <svg width="20" height="20"
                         viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2">

                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>

                    </svg>

                    <span>
                        <strong>Catálogo Activo:</strong>
                        Las prendas mostradas representan el esquema de la
                        base de datos (código, nombre, precio, stock, talla y color).
                    </span>
                </div>

                <div class="collection-filters">

                    <button type="button" class="filter-btn active"
                            data-filter="todos">
                        Todas las prendas
                    </button>

                    <button type="button" class="filter-btn"
                            data-filter="superiores">
                        Prendas Superiores
                    </button>

                    <button type="button" class="filter-btn"
                            data-filter="pantalones">
                        Pantalones & Jeans
                    </button>

                    <button type="button" class="filter-btn"
                            data-filter="vestidos">
                        Vestidos & Faldas
                    </button>

                    <button type="button" class="filter-btn"
                            data-filter="abrigos">
                        Abrigos
                    </button>

                    <button type="button" class="filter-btn"
                            data-filter="accesorios">
                        Accesorios
                    </button>

                </div>

                <div class="products-grid" id="productsGrid">
                    <!-- Productos renderizados mediante landing.js -->
                </div>

            </div>
        </section>


        <!-- =====================================================
             5. BENEFICIOS
        ====================================================== -->

        <section class="benefits-section" id="beneficios">
            <div class="container">

                <div class="section-header">
                    <span class="section-tag">Por qué FashionCloud</span>

                    <h2 class="section-title">
                        Beneficios y Características Reales
                    </h2>

                    <p class="section-subtitle">
                        Diseñado para una experiencia de consulta clara
                        y un control ordenado del proceso de compra.
                    </p>
                </div>

                <div class="benefits-grid">

                    <div class="benefit-card">
                        <div class="benefit-icon">🕒</div>
                        <h3>Catálogo en Línea 24/7</h3>
                        <p>
                            Explora libremente todas las prendas,
                            características y disponibilidad desde cualquier dispositivo.
                        </p>
                    </div>

                    <div class="benefit-card">
                        <div class="benefit-icon">📊</div>
                        <h3>Información Transparente</h3>
                        <p>
                            Detalle claro de tallas disponibles,
                            colores y unidades en stock.
                        </p>
                    </div>

                    <div class="benefit-card">
                        <div class="benefit-icon">📝</div>
                        <h3>Gestión Directa de Pedidos</h3>
                        <p>
                            Tus compras quedan formalmente registradas
                            en el sistema.
                        </p>
                    </div>

                    <div class="benefit-card">
                        <div class="benefit-icon">💵</div>
                        <h3>Métodos de Pago Acordados</h3>
                        <p>
                            Modalidades de pago mediante transferencia
                            bancaria o pago contra entrega.
                        </p>
                    </div>

                </div>
            </div>
        </section>


        <!-- =====================================================
             6. NOSOTROS
        ====================================================== -->

        <section class="about-section" id="nosotros">
            <div class="container">

                <div class="about-grid">

                    <div class="about-text">

                        <span class="section-tag">Sobre Nosotros</span>

                        <h2>
                            Un sistema pensado para modernizar
                            la compra de ropa
                        </h2>

                        <p>
                            <strong>FashionCloud</strong> es una plataforma
                            de comercio electrónico desarrollada con el
                            propósito de optimizar la gestión de una tienda
                            de ropa en línea.
                        </p>

                        <p>
                            Nuestro sistema conecta a los clientes con un
                            catálogo visual, confiable y actualizado, al
                            tiempo que brinda a los administradores herramientas
                            para controlar productos, categorías, inventarios y pedidos.
                        </p>

                        <ul class="about-features">

                            <li>
                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2.5">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>

                                <span>
                                    Consulta rápida de precios, tallas y stock.
                                </span>
                            </li>

                            <li>
                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2.5">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>

                                <span>
                                    Módulo de seguridad para inicio de sesión
                                    y registro de usuarios.
                                </span>
                            </li>

                            <li>
                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2.5">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>

                                <span>
                                    Estructura preparada para integración
                                    con base de datos relacional.
                                </span>
                            </li>

                        </ul>
                    </div>

                    <div class="about-image">
                        <img
                            src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=800&q=80"
                            alt="Boutique de ropa FashionCloud"
                            loading="lazy">
                    </div>

                </div>
            </div>
        </section>

    </main>


    <!-- =====================================================
         7. FOOTER Y CONTACTO
    ====================================================== -->

    <footer class="site-footer" id="contacto">
        <div class="container">

            <div class="footer-grid">

                <div class="footer-brand">

                    <div class="brand-logo">
                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">

                            <path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/>
                            <path d="M12 12v3"/>
                            <path d="m9 15 3-3 3 3"/>

                        </svg>

                        <span>FashionCloud<span class="brand-dot">.</span></span>
                    </div>

                    <p class="footer-desc">
                        Plataforma de comercio electrónico para ropa
                        de moda contemporánea. Calidad, estilo y organización
                        en cada pedido.
                    </p>

                </div>


                <div class="footer-col">

                    <h4>Navegación</h4>

                    <ul class="footer-links">
                        <li><a href="#hero">Inicio</a></li>
                        <li><a href="#coleccion">Colección</a></li>
                        <li><a href="#categorias">Categorías</a></li>
                        <li><a href="#beneficios">Beneficios</a></li>
                        <li><a href="#nosotros">Nosotros</a></li>
                    </ul>

                </div>


                <div class="footer-col">

                    <h4>Mi Cuenta</h4>

                    <ul class="footer-links">

                        <?php if ($usuario && $nombreUsuario !== null): ?>

                            <li>
                                <span>
                                    Sesión iniciada: <?php echo $nombreUsuario; ?>
                                </span>
                            </li>

                            <li>
                                <a href="../backend/controllers/AuthController.php?action=logout">
                                    Cerrar Sesión
                                </a>
                            </li>

                        <?php else: ?>

                            <li>
                                <a href="auth.php?modo=login">
                                    Iniciar Sesión
                                </a>
                            </li>

                            <li>
                                <a href="auth.php?modo=register">
                                    Registrarse
                                </a>
                            </li>

                            <li>
                                <a href="auth.php">
                                    Página de Acceso Directo
                                </a>
                            </li>

                        <?php endif; ?>

                        <li>
                            <a href="../backend/index.php">
                                Panel de Sistema
                            </a>
                        </li>

                    </ul>

                </div>


                <div class="footer-col">

                    <h4>Atención y Contacto</h4>

                    <div class="footer-contact-item">
                        <span>✉</span>
                        <span>contacto@fashioncloud.tienda</span>
                    </div>

                    <div class="footer-contact-item">
                        <span>◷</span>
                        <span>Lunes a Sábado: 09:00 - 19:00</span>
                    </div>

                    <div class="footer-contact-item">
                        <span>◎</span>
                        <span>Proyecto Universitario — 5to Semestre</span>
                    </div>

                </div>

            </div>


            <div class="footer-bottom">

                <p>
                    &copy; 2026 FashionCloud — Tienda de Ropa Online.
                    Todos los derechos reservados.
                </p>

                <p>
                    Desarrollado con arquitectura PHP + HTML5 + CSS3 + JS
                </p>

            </div>

        </div>
    </footer>


    <!-- =====================================================
         8. MODAL DE CONSULTA DE PRENDA
    ====================================================== -->

    <div class="modal-backdrop"
         id="productModal"
         role="dialog"
         aria-modal="true"
         aria-labelledby="modalProductTitle">

        <div class="modal-box">

            <button type="button"
                    class="modal-close-btn"
                    onclick="cerrarModalProducto()"
                    aria-label="Cerrar ventana">
                ✕
            </button>

            <div class="product-modal-grid">

                <div class="product-modal-image">
                    <img id="modalProductImg" src="" alt="Prenda de vestir">
                </div>

                <div class="product-modal-body">

                    <span class="modal-product-code" id="modalProductCode">
                        CÓDIGO: FC-000
                    </span>

                    <h3 class="modal-product-title" id="modalProductTitle">
                        Nombre de Prenda
                    </h3>

                    <div class="modal-product-price" id="modalProductPrice">
                        Bs. 0.00
                    </div>

                    <p class="modal-product-desc" id="modalProductDesc">
                        Descripción detallada de la prenda.
                    </p>

                    <div class="modal-specs-list">

                        <div>
                            <span>Categoría:</span>
                            <strong id="modalProductCategory">-</strong>
                        </div>

                        <div>
                            <span>Tallas disponibles:</span>
                            <strong id="modalProductSizes">-</strong>
                        </div>

                        <div>
                            <span>Color:</span>
                            <strong id="modalProductColor">-</strong>
                        </div>

                        <div>
                            <span>Disponibilidad:</span>
                            <strong class="stock-tag-ok" id="modalProductStock">
                                En inventario
                            </strong>
                        </div>

                    </div>

                    <div class="order-info-notice">
                        ℹ️ <strong>Modalidad de Pedido:</strong>
                        Para solicitar esta prenda, inicia sesión en el
                        sistema y coordina tu pedido.
                    </div>

                    <div style="display:flex; gap:0.75rem;">

                        <a href="auth.php?modo=login"
                           class="btn btn-primary btn-full">
                            Iniciar Sesión para Solicitar
                        </a>

                    </div>

                </div>
            </div>
        </div>
    </div>


    <!-- =====================================================
         9. MODAL DE AUTENTICACIÓN ORIGINAL
         Se conserva para no afectar landing.js.
    ====================================================== -->

    <div class="modal-backdrop"
         id="authModal"
         role="dialog"
         aria-modal="true"
         aria-labelledby="authModalHeading">

        <div class="modal-box auth-modal-box">

            <button type="button"
                    class="modal-close-btn"
                    onclick="cerrarModalAuth()"
                    aria-label="Cerrar">
                ✕
            </button>

            <div class="auth-modal-header">

                <h3 id="authModalHeading">
                    Acceso FashionCloud
                </h3>

                <p>
                    Gestiona tus compras y consultas en la plataforma
                </p>

            </div>

            <div class="auth-tabs">

                <button type="button"
                        class="auth-tab-btn active"
                        id="tabLoginBtn"
                        onclick="cambiarPestanaAuth('login')">
                    Iniciar Sesión
                </button>

                <button type="button"
                        class="auth-tab-btn"
                        id="tabRegisterBtn"
                        onclick="cambiarPestanaAuth('register')">
                    Registrarse
                </button>

            </div>


            <form id="modalLoginForm"
                  action="../backend/controllers/AuthController.php?action=login"
                  method="POST">

                <div class="auth-form-group">

                    <label for="modalLoginCorreo">
                        Correo Electrónico
                    </label>

                    <input type="email"
                           id="modalLoginCorreo"
                           name="correo"
                           placeholder="ejemplo@correo.com"
                           required>

                </div>

                <div class="auth-form-group">

                    <label for="modalLoginPassword">
                        Contraseña
                    </label>

                    <input type="password"
                           id="modalLoginPassword"
                           name="password"
                           placeholder="Tu contraseña"
                           required>

                </div>

                <button type="submit"
                        class="btn btn-primary btn-full">
                    Ingresar al Sistema
                </button>

            </form>


            <form id="modalRegisterForm"
                  action="../backend/controllers/AuthController.php?action=register"
                  method="POST"
                  style="display:none;">

                <div class="auth-form-group">

                    <label for="modalRegNombre">
                        Nombre Completo
                    </label>

                    <input type="text"
                           id="modalRegNombre"
                           name="nombre"
                           placeholder="Nombre y Apellido"
                           required>

                </div>

                <div class="auth-form-group">

                    <label for="modalRegCorreo">
                        Correo Electrónico
                    </label>

                    <input type="email"
                           id="modalRegCorreo"
                           name="correo"
                           placeholder="ejemplo@correo.com"
                           required>

                </div>

                <div class="auth-form-group">

                    <label for="modalRegPassword">
                        Contraseña (Mínimo 8 caracteres)
                    </label>

                    <input type="password"
                           id="modalRegPassword"
                           name="password"
                           minlength="8"
                           placeholder="Contraseña segura"
                           required>

                </div>

                <button type="submit"
                        class="btn btn-accent btn-full">
                    Crear mi Cuenta
                </button>

            </form>


            <div class="auth-standalone-link">
                ¿Prefieres acceder desde otra pantalla?

                <a href="auth.php">
                    Abrir pantalla completa
                </a>
            </div>

        </div>
    </div>


    <!-- =====================================================
         10. JAVASCRIPT
    ====================================================== -->

    <script src="js/landing.js"></script>

</body>
</html>
