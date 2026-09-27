<?php
/**
 * Layout Base con Sidebar Izquierdo y Header Superior
 * Variables esperadas:
 * - $pageTitle: Título de la página
 * - $activePage: Identificador de la vista activa ('dashboard', 'citas', 'clientes', 'servicios', 'empleados', 'pagos', 'reportes', 'usuarios')
 * - $contentView: Ruta absoluta o relativa al contenido de la vista
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = [
    'id'      => $_SESSION['usuario_id'] ?? 0,
    'usuario' => $_SESSION['usuario'] ?? 'Invitado',
    'nombre'  => $_SESSION['nombre'] ?? 'Usuario',
    'email'   => $_SESSION['email'] ?? '',
    'rol'     => $_SESSION['rol_nombre'] ?? 'Sin rol',
    'cargo'   => $_SESSION['cargo'] ?? 'Personal'
];

$activePage = $activePage ?? 'dashboard';
$userInitials = strtoupper(substr($user['nombre'], 0, 1) . (strpos($user['nombre'], ' ') !== false ? substr($user['nombre'], strpos($user['nombre'], ' ') + 1, 1) : ''));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Valle Sereno Spa') ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Estilo Armónico Personalizado Valle Sereno -->
    <link rel="stylesheet" href="public/css/spa-theme.css?v=1.2">
</head>
<body>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="app-wrapper">
        <!-- Sidebar Izquierdo -->
        <aside class="app-sidebar" id="appSidebar">
            <!-- Cabecera Sidebar -->
            <div class="sidebar-header d-flex align-items-center justify-content-between">
                <a href="index.php?c=dashboard&a=index" class="spa-brand">
                    <i class="bi bi-flower1 me-2 text-warning"></i>Valle <span>Sereno</span>
                </a>
                <button class="btn btn-sm btn-link text-white-50 d-lg-none p-0" id="closeSidebarBtn">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>

            <!-- Navegación Lateral -->
            <nav class="sidebar-nav">
                <div class="sidebar-section-title">Principal</div>
                <a href="index.php?c=dashboard&a=index" class="nav-spa-link <?= $activePage === 'dashboard' ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>

                <div class="sidebar-section-title">Gestión Operativa</div>
                <a href="index.php?c=citas&a=index" class="nav-spa-link <?= $activePage === 'citas' ? 'active' : '' ?>">
                    <i class="bi bi-calendar2-heart-fill"></i>
                    <span>Citas & Agenda</span>
                </a>
                <a href="index.php?c=clientes&a=index" class="nav-spa-link <?= $activePage === 'clientes' ? 'active' : '' ?>">
                    <i class="bi bi-people-fill"></i>
                    <span>Clientes</span>
                </a>
                <a href="index.php?c=servicios&a=index" class="nav-spa-link <?= $activePage === 'servicios' ? 'active' : '' ?>">
                    <i class="bi bi-droplet-half"></i>
                    <span>Servicios & Terapias</span>
                </a>

                <div class="sidebar-section-title">Finanzas & Personal</div>
                <a href="index.php?c=pagos&a=index" class="nav-spa-link <?= $activePage === 'pagos' ? 'active' : '' ?>">
                    <i class="bi bi-credit-card-2-front-fill"></i>
                    <span>Caja & Pagos</span>
                </a>
                <a href="index.php?c=empleados&a=index" class="nav-spa-link <?= $activePage === 'empleados' ? 'active' : '' ?>">
                    <i class="bi bi-person-badge-fill"></i>
                    <span>Personal Terapéutico</span>
                </a>

                <?php if ($user['rol'] === 'Administrador'): ?>
                    <div class="sidebar-section-title">Sistema</div>
                    <a href="index.php?c=reportes&a=index" class="nav-spa-link <?= $activePage === 'reportes' ? 'active' : '' ?>">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>Reportes</span>
                    </a>
                    <a href="index.php?c=usuarios&a=index" class="nav-spa-link <?= $activePage === 'usuarios' ? 'active' : '' ?>">
                        <i class="bi bi-shield-lock-fill"></i>
                        <span>Usuarios & Roles</span>
                    </a>
                <?php endif; ?>
            </nav>

            <!-- Footer Sidebar: Usuario Autenticado -->
            <div class="sidebar-footer">
                <div class="user-badge-box">
                    <div class="user-avatar-circle">
                        <?= htmlspecialchars($userInitials) ?>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-white fw-semibold small text-truncate"><?= htmlspecialchars($user['nombre']) ?></div>
                        <small class="badge rounded-pill text-warning border border-warning-subtle" style="font-size: 0.65rem; background: rgba(197, 160, 89, 0.15);">
                            <?= htmlspecialchars($user['rol']) ?>
                        </small>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="app-main">
            <!-- Header Superior -->
            <header class="app-header">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-secondary btn-sm d-lg-none" id="toggleSidebarBtn">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div>
                        <h5 class="mb-0 fw-bold" style="color: var(--spa-primary); font-family: var(--font-serif);">
                            <?= htmlspecialchars($pageTitle ?? 'Santuario de Bienestar') ?>
                        </h5>
                        <small class="text-muted" style="font-size: 0.78rem;">
                            <?= date('d \d\e F, Y') ?> &bull; Centro de Relajación & Terapias
                        </small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <a href="index.php?c=citas&a=index" class="btn btn-sm btn-outline-success d-none d-md-inline-flex align-items-center gap-1 rounded-pill px-3" style="border-color: #2c594c; color: #2c594c;">
                        <i class="bi bi-plus-circle"></i> Nueva Cita
                    </a>

                    <div class="dropdown">
                        <button class="btn btn-light btn-sm rounded-pill dropdown-toggle d-flex align-items-center gap-2 border px-3" type="button" data-bs-toggle="dropdown">
                            <span class="user-avatar-circle" style="width:26px; height:26px; font-size:0.75rem;">
                                <?= htmlspecialchars($userInitials) ?>
                            </span>
                            <span class="d-none d-sm-inline fw-semibold small"><?= htmlspecialchars($user['usuario']) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold small"><?= htmlspecialchars($user['nombre']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 text-danger small" href="index.php?c=auth&a=logout">
                                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Contenido Dinámico de la Vista Inyectada -->
            <main class="page-container flex-grow-1">
                <?php 
                if (isset($contentView) && file_exists($contentView)) {
                    include $contentView;
                } else {
                    echo "<div class='alert alert-warning'>Vista no disponible.</div>";
                }
                ?>
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar en dispositivos móviles
        const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
        const closeSidebarBtn = document.getElementById('closeSidebarBtn');
        const appSidebar = document.getElementById('appSidebar');
        const sidebarNav = document.querySelector('.sidebar-nav');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        function toggleSidebar() {
            appSidebar.classList.toggle('show');
            sidebarBackdrop.classList.toggle('show');
        }

        if (toggleSidebarBtn) toggleSidebarBtn.addEventListener('click', toggleSidebar);
        if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', toggleSidebar);
        if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', toggleSidebar);

        // Mantener la posición de scroll y foco en el botón/enlace seleccionado
        document.addEventListener('DOMContentLoaded', function() {
            if (!sidebarNav) return;

            // 1. Restaurar posición exacta guardada si existe
            const savedScroll = sessionStorage.getItem('spa_sidebar_scroll');
            if (savedScroll !== null) {
                sidebarNav.scrollTop = parseInt(savedScroll, 10);
            }

            // 2. Asegurar que el elemento activo esté siempre enfocado y a la vista
            const activeLink = sidebarNav.querySelector('.nav-spa-link.active');
            if (activeLink) {
                activeLink.scrollIntoView({ block: 'nearest', inline: 'nearest' });
            }

            // 3. Guardar posición cada vez que el usuario hace scroll dentro del sidebar
            sidebarNav.addEventListener('scroll', function() {
                sessionStorage.setItem('spa_sidebar_scroll', sidebarNav.scrollTop);
            });

            // 4. Guardar inmediatamente cuando se hace clic en cualquier enlace
            const navLinks = sidebarNav.querySelectorAll('.nav-spa-link');
            navLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    sessionStorage.setItem('spa_sidebar_scroll', sidebarNav.scrollTop);
                });
            });
        });
    </script>
</body>
</html>
