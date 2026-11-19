/**
 * FASHIONCLOUD — JAVASCRIPT DE LA LANDING PAGE
 * Gestión de catálogo representativo, modales interactivos y navegación responsive
 */

const datosCatalogo = JSON.parse(document.getElementById('catalogoDatos').textContent);
const CATALOGO_FASHIONCLOUD = datosCatalogo.productos.map(producto => ({
    ...producto,
    id: Number(producto.id),
    precio: Number(producto.precio),
    stock: Number(producto.stock),
    categoria_slug: String(producto.categoria_id ?? ''),
    talla: producto.talla || 'Sin especificar',
    color: producto.color || 'Sin especificar'
}));

function escaparHTML(valor) {
    const elemento = document.createElement('span');
    elemento.textContent = String(valor ?? '');
    return elemento.innerHTML.replaceAll('"', '&quot;').replaceAll("'", '&#39;');
}

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
        <article class="product-card" data-category="${escaparHTML(producto.categoria_slug)}">
            <div class="product-thumb">
                <span class="product-badge">${escaparHTML(producto.categoria_nombre)}</span>
                ${producto.imagen_url ? `<img src="${escaparHTML(producto.imagen_url)}" alt="${escaparHTML(producto.nombre)}" loading="lazy">` : ''}
            </div>
            <div class="product-info">
                <div class="product-meta-row">
                    <span class="product-code">${escaparHTML(producto.codigo)}</span>
                    <span>Stock: ${producto.stock} uds.</span>
                </div>
                <h3 class="product-title">${escaparHTML(producto.nombre)}</h3>
                <div class="product-specs">
                    <span>Talla: <strong>${escaparHTML(producto.talla)}</strong></span>
                    <span>Color: <strong>${escaparHTML(producto.color)}</strong></span>
                </div>
                <div class="product-footer">
                    <div class="product-price">
                        <small>Bs.</small> ${producto.precio.toFixed(2)}
                    </div>
                    <button type="button" class="btn btn-outline btn-sm" onclick="abrirModalProducto(${producto.id})">
                        Ver detalles
                    </button>
                    <form method="post" action="carrito.php">
                        <input type="hidden" name="csrf_token" value="${escaparHTML(datosCatalogo.csrf)}">
                        <input type="hidden" name="producto_id" value="${producto.id}">
                        <button class="btn btn-accent btn-sm" name="accion" value="agregar" ${producto.stock > 0 ? '' : 'disabled'}>
                            ${producto.stock > 0 ? 'Agregar al carrito' : 'Agotado'}
                        </button>
                    </form>
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
    (targetBtn ?? document.querySelector('.filter-btn[data-filter="todos"]'))?.click();
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
