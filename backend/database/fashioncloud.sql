-- =====================================================================
-- FASHIONCLOUD — BASE DE DATOS OFICIAL EN MYSQL
-- Proyecto Universitario: Tienda de Ropa Online
-- Compatible con XAMPP / MariaDB y MySQL 8.0+
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `fashioncloud` 
  DEFAULT CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `fashioncloud`;

-- ---------------------------------------------------------------------
-- 1. TABLA: usuarios
-- Model: backend/models/Usuario.php
-- Controller: backend/controllers/AuthController.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(120) NOT NULL,
  `correo` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `rol` ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. TABLA: categorias
-- Model: backend/models/Categoria.php
-- Controller: backend/controllers/CategoriaController.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `descripcion` TEXT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. TABLA: productos
-- Model: backend/models/Producto.php
-- Controller: backend/controllers/ProductoController.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `productos` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `codigo` VARCHAR(50) NOT NULL UNIQUE,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` TEXT NULL,
  `precio` DECIMAL(10,2) NOT NULL,
  `stock` INT NOT NULL DEFAULT 0,
  `talla` VARCHAR(50) NULL,
  `color` VARCHAR(50) NULL,
  `imagen_url` VARCHAR(255) NULL,
  `categoria_id` INT UNSIGNED NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`categoria_id`) 
    REFERENCES `categorias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. TABLA: pedidos
-- Model: backend/models/Pedido.php
-- Controller: backend/controllers/PedidoController.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT UNSIGNED NOT NULL,
  `total` DECIMAL(10,2) NOT NULL,
  `direccion` TEXT NOT NULL,
  `estado` ENUM('pendiente', 'confirmado', 'enviado', 'entregado', 'cancelado') NOT NULL DEFAULT 'pendiente',
  `metodo_pago` ENUM('por_confirmar', 'transferencia', 'efectivo') NOT NULL DEFAULT 'por_confirmar',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_pedidos_usuario` FOREIGN KEY (`usuario_id`) 
    REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. TABLA: detalle_pedidos
-- Controller: backend/controllers/PedidoController.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `detalle_pedidos` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `pedido_id` INT UNSIGNED NOT NULL,
  `producto_id` INT UNSIGNED NOT NULL,
  `cantidad` INT NOT NULL,
  `precio_unitario` DECIMAL(10,2) NOT NULL,
  CONSTRAINT `fk_detalle_pedido` FOREIGN KEY (`pedido_id`) 
    REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`producto_id`) 
    REFERENCES `productos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- DATOS INICIALES (SEMILLAS DE PRUEBA PARA XAMPP)
-- =====================================================================

-- Usuarios de prueba inicial:
-- Administrador: admin@fashioncloud.tienda / admin12345
-- Cliente: cliente@fashioncloud.tienda / cliente12345
INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `password`, `rol`) VALUES
(1, 'Administrador FashionCloud', 'admin@fashioncloud.tienda', '$2y$10$O2dM1G1pG8xYp6lQhM8z7.5T6A8a8iFwz9i1J9u.aO9gC7q4sHk9e', 'admin'),
(2, 'Cliente de Prueba', 'cliente@fashioncloud.tienda', '$2y$10$Wp7Z3C8iE5bQ9sA1jF2nOe5V2R8t4hY7u1K9o.cM6vB8w5q3e1k7u', 'cliente')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Categorías oficiales de ropa:
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'Prendas Superiores', 'Blusas, camisas elegantes, tops y poleras casuales', 1),
(2, 'Pantalones & Jeans', 'Pantalones sastreros, jeans denim de corte clásico y moderno', 1),
(3, 'Vestidos & Faldas', 'Vestidos de fiesta, eventos formales y faldas casuales fluidas', 1),
(4, 'Abrigos & Chaquetas', 'Blazers ejecutivos, chaquetas de temporada y abrigos estructurados', 1),
(5, 'Accesorios', 'Cinturones de cuero, bufandas y complementos de moda', 1)
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Catálogo de productos representativos con stock real:
INSERT INTO `productos` (`id`, `codigo`, `nombre`, `descripcion`, `precio`, `stock`, `talla`, `color`, `imagen_url`, `categoria_id`, `estado`) VALUES
(1, 'FC-TOP-001', 'Blusa Minimalista de Seda', 'Blusa confeccionada en tejido suave con acabado mate y caída fluida.', 185.00, 14, 'S, M, L', 'Blanco Perla', 'https://images.unsplash.com/photo-1598554747436-c9293d6a588f?auto=format&fit=crop&w=700&q=80', 1, 1),
(2, 'FC-PAN-002', 'Pantalón Sastrero Wide Leg', 'Pantalón de tiro alto con pinzas frontales y corte recto holgado.', 220.00, 9, '36, 38, 40, 42', 'Negro Carbón', 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=700&q=80', 2, 1),
(3, 'FC-VES-003', 'Vestido Midi Plisado', 'Vestido midi con falda plisada y escote cruzado. Tejido liviano para eventos.', 260.00, 8, 'S, M, L', 'Terracota Cálido', 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=700&q=80', 3, 1),
(4, 'FC-ABR-004', 'Blazer Estructurado Clásico', 'Blazer con solapas en muesca, cierre simple de dos botones y forro interior.', 340.00, 6, 'M, L, XL', 'Camel Neutro', 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=700&q=80', 4, 1),
(5, 'FC-TOP-005', 'Camisa Oxford Slim Fit', 'Camisa 100% algodón hilado en tejido Oxford transpirable.', 165.00, 18, 'S, M, L, XL', 'Azul Cielo', 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?auto=format&fit=crop&w=700&q=80', 1, 1),
(6, 'FC-PAN-006', 'Jeans Straight Denim Premium', 'Pantalón vaquero de corte recto con tiro medio-alto y ligero confort stretch.', 195.00, 12, '38, 40, 42, 44', 'Azul Índigo Lavado', 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=700&q=80', 2, 1),
(7, 'FC-ACC-007', 'Cinturón Silla de Cuero Genuino', 'Cinturón artesanal de cuero con hebilla metálica en acabado dorado envejecido.', 95.00, 15, 'Ajustable', 'Marrón Suave', 'https://images.unsplash.com/photo-1624222247344-550fb60583dc?auto=format&fit=crop&w=700&q=80', 5, 1),
(8, 'FC-VES-008', 'Vestido Corto Camisero', 'Vestido estilo camisero con cinturón anudado a la cintura y bolsillos laterales.', 210.00, 7, 'XS, S, M', 'Verde Oliva', 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?auto=format&fit=crop&w=700&q=80', 3, 1)
ON DUPLICATE KEY UPDATE `id` = `id`;
