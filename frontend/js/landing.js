/**
 * FASHIONCLOUD — JAVASCRIPT DE LA LANDING PAGE
 * Gestión de catálogo representativo, modales interactivos y navegación responsive
 */

// Catálogo de demostración estructurado estrictamente según el modelo Producto.php y la base de datos
const CATALOGO_FASHIONCLOUD = [
    {
        id: 1,
        codigo: 'FC-TOP-001',
        nombre: 'Blusa Minimalista de Seda',
        descripcion: 'Blusa confeccionada en tejido suave con acabado mate y caída fluida. Ideal para looks ejecutivos o casuales.',
        precio: 185.00,
        stock: 14,
        talla: 'S, M, L',
        color: 'Blanco Perla',
        imagen_url: 'https://images.unsplash.com/photo-1598554747436-c9293d6a588f?auto=format&fit=crop&w=700&q=80',
        categoria_id: 1,
        categoria_slug: 'superiores',
        categoria_nombre: 'Prendas Superiores'
    },
    {
        id: 2,
        codigo: 'FC-PAN-002',
        nombre: 'Pantalón Sastrero Wide Leg',
        descripcion: 'Pantalón de tiro alto con pinzas frontales y corte recto holgado. Confeccionado en mezcla de viscosa y poliéster.',
        precio: 220.00,
        stock: 9,
        talla: '36, 38, 40, 42',
        color: 'Negro Carbón',
        imagen_url: 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=700&q=80',
        categoria_id: 2,
        categoria_slug: 'pantalones',
        categoria_nombre: 'Pantalones & Jeans'
    },
    {
        id: 3,
        codigo: 'FC-VES-003',
        nombre: 'Vestido Midi Plisado',
        descripcion: 'Vestido midi con falda plisada y escote cruzado. Tejido liviano con movimiento elegante para eventos y cócteles.',
        precio: 260.00,
        stock: 8,
        talla: 'S, M, L',
        color: 'Terracota Cálido',
        imagen_url: 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=700&q=80',
        categoria_id: 3,
        categoria_slug: 'vestidos',
        categoria_nombre: 'Vestidos & Faldas'
    },
    {
        id: 4,
        codigo: 'FC-ABR-004',
        nombre: 'Blazer Estructurado Clásico',
        descripcion: 'Blazer con solapas en muesca, cierre simple de dos botones y forro interior satinado. Aporta distinción y presencia.',
        precio: 340.00,
        stock: 6,
        talla: 'M, L, XL',
        color: 'Camel Neutro',
        imagen_url: 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=700&q=80',
        categoria_id: 4,
        categoria_slug: 'abrigos',
        categoria_nombre: 'Abrigos & Chaquetas'
    },
    {
        id: 5,
        codigo: 'FC-TOP-005',
        nombre: 'Camisa Oxford Slim Fit',
        descripcion: 'Camisa 100% algodón hilado en tejido Oxford transpirable. Cuello con botones ocultos y corte limpio contemporáneo.',
        precio: 165.00,
        stock: 18,
        talla: 'S, M, L, XL',
        color: 'Azul Cielo',
        imagen_url: 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?auto=format&fit=crop&w=700&q=80',
        categoria_id: 1,
        categoria_slug: 'superiores',
        categoria_nombre: 'Prendas Superiores'
    },
    {
        id: 6,
        codigo: 'FC-PAN-006',
        nombre: 'Jeans Straight Denim Premium',
        descripcion: 'Pantalón vaquero de corte recto con tiro medio-alto. Denim de 12 oz con ligero confort stretch para el día a día.',
        precio: 195.00,
        stock: 12,
        talla: '38, 40, 42, 44',
        color: 'Azul Índigo Lavado',
        imagen_url: 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=700&q=80',
        categoria_id: 2,
        categoria_slug: 'pantalones',
        categoria_nombre: 'Pantalones & Jeans'
    },
    {
        id: 7,
        codigo: 'FC-ACC-007',
        nombre: 'Cinturón Silla de Cuero Genuino',
        descripcion: 'Cinturón artesanal de cuero con hebilla metálica en acabado dorado envejecido. Anchura media versátil para trajes y jeans.',
        precio: 95.00,
        stock: 15,
        talla: 'Ajustable (85-105 cm)',
        color: 'Marrón Suave',
        imagen_url: 'https://images.unsplash.com/photo-1624222247344-550fb60583dc?auto=format&fit=crop&w=700&q=80',
        categoria_id: 5,
        categoria_slug: 'accesorios',
        categoria_nombre: 'Accesorios'
    },
    {
        id: 8,
        codigo: 'FC-VES-008',
        nombre: 'Vestido Corto Camisero',
        descripcion: 'Vestido estilo camisero con cinturón anudado a la cintura y bolsillos laterales discretos. Fresco, moderno y cómodo.',
        precio: 210.00,
        stock: 7,
        talla: 'XS, S, M',
        color: 'Verde Oliva',
        imagen_url: 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?auto=format&fit=crop&w=700&q=80',
        categoria_id: 3,
        categoria_slug: 'vestidos',
        categoria_nombre: 'Vestidos & Faldas'
    }
];

// Estado global de la aplicación cliente
let filtroCategoriaActual = 'todos';

document.addEventListener('DOMContentLoaded', () => {
    inicializarNavegacion();
    renderizarProductos(CATALOGO_FASHIONCLOUD);
    inicializarFiltros();
    inicializarModales();
});

/**
 * Inicialización de la barra de navegación y menú móvil
 */
function inicializarNavegacion() {
    const header = document.querySelector('.site-header');
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const navLinks = document.getElementById('navLinks');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 40) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    if (mobileBtn && navLinks) {
        mobileBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });

        // Cerrar menú al hacer clic en un enlace
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
            });
        });
    }
}

/**
 * Renderizado de tarjetas de producto en el grid
 */
function renderizarProductos(productos) {
    const grid = document.getElementById('productsGrid');
    if (!grid) return;

    if (productos.length === 0) {
        grid.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 3rem 1rem; color: var(--color-text-muted);">
                <p>No se encontraron prendas disponibles para la categoría seleccionada.</p>
            </div>
        `;
        return;
    }

    grid.innerHTML = productos.map(producto => `
        <article class="product-card" data-category="${producto.categoria_slug}">
            <div class="product-thumb">
                <span class="product-badge">${producto.categoria_nombre}</span>
                <img src="${producto.imagen_url}" alt="${producto.nombre}" loading="lazy">
            </div>
            <div class="product-info">
                <div class="product-meta-row">
                    <span class="product-code">${producto.codigo}</span>
                    <span>Stock: ${producto.stock} uds.</span>
                </div>
                <h3 class="product-title">${producto.nombre}</h3>
                <div class="product-specs">
                    <span>Talla: <strong>${producto.talla}</strong></span>
                    <span>Color: <strong>${producto.color}</strong></span>
                </div>
                <div class="product-footer">
                    <div class="product-price">
                        <small>Bs.</small> ${producto.precio.toFixed(2)}
                    </div>
                    <button type="button" class="btn btn-outline btn-sm" onclick="abrirModalProducto(${producto.id})">
                        Consultar Prenda
                    </button>
                </div>
            </div>
        </article>
    `).join('');
}

/**
 * Filtros de categoría en la colección
 */
function inicializarFiltros() {
    const filterButtons = document.querySelectorAll('.filter-btn');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const categoria = btn.getAttribute('data-filter');
            filtroCategoriaActual = categoria;

            if (categoria === 'todos') {
                renderizarProductos(CATALOGO_FASHIONCLOUD);
            } else {
                const filtrados = CATALOGO_FASHIONCLOUD.filter(p => p.categoria_slug === categoria);
                renderizarProductos(filtrados);
            }
        });
    });
}

/**
 * Filtro directo activable desde las tarjetas visuales de categoría
 */
function filtrarPorCategoria(slug) {
    const targetBtn = document.querySelector(`.filter-btn[data-filter="${slug}"]`);
    if (targetBtn) {
        targetBtn.click();
    }
    const coleccionSec = document.getElementById('coleccion');
    if (coleccionSec) {
        coleccionSec.scrollIntoView({ behavior: 'smooth' });
    }
}

/**
 * Inicialización y control de ventanas modales
 */
function inicializarModales() {
    // Cerrar modales con tecla Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            cerrarModalProducto();
            cerrarModalAuth();
        }
    });

    // Cerrar al hacer clic en el backdrop
    const productBackdrop = document.getElementById('productModal');
    if (productBackdrop) {
        productBackdrop.addEventListener('click', (e) => {
            if (e.target === productBackdrop) {
                cerrarModalProducto();
            }
        });
    }

    const authBackdrop = document.getElementById('authModal');
    if (authBackdrop) {
        authBackdrop.addEventListener('click', (e) => {
            if (e.target === authBackdrop) {
                cerrarModalAuth();
            }
        });
    }
}

/**
 * Abre el modal con información detallada de una prenda
 */
function abrirModalProducto(id) {
    const producto = CATALOGO_FASHIONCLOUD.find(p => p.id === id);
    if (!producto) return;

    document.getElementById('modalProductImg').src = producto.imagen_url;
    document.getElementById('modalProductImg').alt = producto.nombre;
    document.getElementById('modalProductCode').textContent = `Código: ${producto.codigo}`;
    document.getElementById('modalProductTitle').textContent = producto.nombre;
    document.getElementById('modalProductPrice').textContent = `Bs. ${producto.precio.toFixed(2)}`;
    document.getElementById('modalProductDesc').textContent = producto.descripcion;
    document.getElementById('modalProductCategory').textContent = producto.categoria_nombre;
    document.getElementById('modalProductSizes').textContent = producto.talla;
    document.getElementById('modalProductColor').textContent = producto.color;
    document.getElementById('modalProductStock').textContent = `${producto.stock} unidades disponibles`;

    const modal = document.getElementById('productModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function cerrarModalProducto() {
    const modal = document.getElementById('productModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

/**
 * Modal integrado de autenticación (Login & Registro)
 */
function abrirModalAuth(modo = 'login') {
    const modal = document.getElementById('authModal');
    if (!modal) return;

    cambiarPestanaAuth(modo);
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function cerrarModalAuth() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function cambiarPestanaAuth(pestana) {
    const tabLogin = document.getElementById('tabLoginBtn');
    const tabRegister = document.getElementById('tabRegisterBtn');
    const formLogin = document.getElementById('modalLoginForm');
    const formRegister = document.getElementById('modalRegisterForm');

    if (pestana === 'login') {
        tabLogin.classList.add('active');
        tabRegister.classList.remove('active');
        formLogin.style.display = 'block';
        formRegister.style.display = 'none';
    } else {
        tabRegister.classList.add('active');
        tabLogin.classList.remove('active');
        formRegister.style.display = 'block';
        formLogin.style.display = 'none';
    }
}
