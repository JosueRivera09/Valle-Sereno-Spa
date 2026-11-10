<?php
/**
 * Layout Base — Editorial Wellness (Masthead Top Navigation)
 * Variables esperadas:
 * - $pageTitle: Título de la página
 * - $activePage: Identificador de la vista activa
 * - $contentView: Ruta al contenido
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

$nombreCompleto = trim($user['nombre']);
$partesNombre   = preg_split('/\s+/', $nombreCompleto);
$initials       = strtoupper(
    (isset($partesNombre[0]) ? mb_substr($partesNombre[0], 0, 1, 'UTF-8') : '') .
    (isset($partesNombre[1]) ? mb_substr($partesNombre[1], 0, 1, 'UTF-8') : '')
);
if ($initials === '') {
    $initials = 'U';
}
$userEmail = $user['email'] ?: $user['usuario'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Valle Sereno Spa', ENT_QUOTES, 'UTF-8') ?></title>

    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    <!-- jsPDF Core + AutoTable (exportación de reportes a PDF en el navegador) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Estilos -->
    <link href="public/css/spa-theme.css?v=3.2" rel="stylesheet">
</head>

<body class="app-body">
    <!-- Masthead Editorial -->
    <header class="masthead">
        <div class="masthead-inner">
            <button id="navToggle" class="nav-toggle d-lg-none" type="button" aria-label="Abrir menú de navegación">
                <i class="bi bi-list fs-4"></i>
            </button>

            <a class="wordmark" href="index.php?c=dashboard&a=index">
                Valle <span>Sereno</span>
            </a>

            <nav class="mainnav" id="mainnav">
                <?php if ($user['rol'] !== 'Terapeuta'): ?>
                    <a href="index.php?c=dashboard&a=index"
                        class="nav-link-ed <?= $activePage === 'dashboard' ? 'is-active' : '' ?>"
                        title="Panel Principal">
                        <span>Inicio</span>
                    </a>
                <?php endif; ?>

                <a href="index.php?c=citas&a=index"
                    class="nav-link-ed <?= $activePage === 'citas' ? 'is-active' : '' ?>"
                    title="Citas & Agenda">
                    <span>Citas</span>
                </a>

                <?php if (in_array($user['rol'], ['Administrador', 'Recepcionista'])): ?>
                    <a href="index.php?c=clientes&a=index"
                        class="nav-link-ed <?= $activePage === 'clientes' ? 'is-active' : '' ?>"
                        title="Clientes">
                        <span>Clientes</span>
                    </a>
                <?php endif; ?>

                <a href="index.php?c=servicios&a=index"
                    class="nav-link-ed <?= $activePage === 'servicios' ? 'is-active' : '' ?>"
                    title="Servicios & Terapias">
                    <span>Servicios</span>
                </a>

                <?php if ($user['rol'] !== 'Terapeuta'): ?>
                    <a href="index.php?c=pagos&a=index"
                        class="nav-link-ed <?= $activePage === 'pagos' ? 'is-active' : '' ?>"
                        title="Caja & Pagos">
                        <span>Caja</span>
                    </a>
                    <a href="index.php?c=empleados&a=index"
                        class="nav-link-ed <?= $activePage === 'empleados' ? 'is-active' : '' ?>"
                        title="Personal Terapéutico">
                        <span>Personal</span>
                    </a>
                <?php endif; ?>

                <?php if ($user['rol'] === 'Administrador'): ?>
                    <a href="index.php?c=reportes&a=index"
                        class="nav-link-ed <?= $activePage === 'reportes' ? 'is-active' : '' ?>"
                        title="Reportes & Analítica">
                        <span>Reportes</span>
                    </a>
                    <a href="index.php?c=usuarios&a=index"
                        class="nav-link-ed <?= $activePage === 'usuarios' ? 'is-active' : '' ?>"
                        title="Gestión de Usuarios">
                        <span>Usuarios</span>
                    </a>
                    <a href="index.php?c=respaldos&a=index"
                        class="nav-link-ed <?= $activePage === 'respaldos' ? 'is-active' : '' ?>"
                        title="Respaldos & Mantenimiento">
                        <span>Respaldos</span>
                    </a>
                <?php endif; ?>
            </nav>

            <div class="masthead-end">
                <div class="dropdown">
                    <button class="user-chip" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="user-avatar-circle"><?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="user-chip-name">
                            <strong><?= htmlspecialchars($user['nombre'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <small><?= htmlspecialchars($user['rol'], ENT_QUOTES, 'UTF-8') ?></small>
                        </span>
                        <i class="bi bi-chevron-down ms-1"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end masthead-menu">
                        <li>
                            <h6 class="dropdown-header mb-0">
                                <?= htmlspecialchars($user['nombre'], ENT_QUOTES, 'UTF-8') ?>
                            </h6>
                            <small class="d-block px-3 pb-1 text-muted">
                                <?= htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') ?>
                            </small>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="index.php?c=auth&a=logout"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="page-wrap">
        <?php if (!empty($_SESSION['success_flash'])): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 spa-alert" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div><?= htmlspecialchars($_SESSION['success_flash']) ?></div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_flash']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['warning_flash'])): ?>
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 spa-alert" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div><?= htmlspecialchars($_SESSION['warning_flash']) ?></div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['warning_flash']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error_flash'])): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 spa-alert" role="alert">
                <i class="bi bi-x-circle-fill fs-5"></i>
                <div><?= htmlspecialchars($_SESSION['error_flash']) ?></div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_flash']); ?>
        <?php endif; ?>

        <?php
        if (isset($contentView) && file_exists($contentView)) {
            include $contentView;
        } else {
            echo "<div class='alert alert-warning'>Vista no disponible.</div>";
        }
        ?>
    </main>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ---------------------------------------------------------------
        // Protección global CSRF + detección AJAX para TODOS los fetch()
        // Todas las peticiones POST envían el token (X-CSRF-TOKEN) y la
        // cabecera X-Requested-With para recibir JSON ante sesión expirada.
        // ---------------------------------------------------------------
        const CSRF_TOKEN = '<?= AuthHelper::csrfToken() ?>';

        // Helper global de escape para interpolaciones en innerHTML (previene XSS)
        function esc(valor) {
            const div = document.createElement('div');
            div.textContent = (valor === null || valor === undefined) ? '' : String(valor);
            return div.innerHTML;
        }

        const _fetchOriginal = window.fetch;
        window.fetch = function(input, init) {
            init = init || {};
            const headers = new Headers(init.headers || {});
            headers.set('X-CSRF-TOKEN', CSRF_TOKEN);
            headers.set('X-Requested-With', 'XMLHttpRequest');
            init.headers = headers;
            if (init.body instanceof FormData) {
                init.body.append('csrf_token', CSRF_TOKEN);
            }
            return _fetchOriginal(input, init);
        };

        // Toggle navegación móvil
        const navToggle = document.getElementById('navToggle');
        const mainNav = document.getElementById('mainnav');

        if (navToggle && mainNav) {
            navToggle.addEventListener('click', function() {
                mainNav.classList.toggle('is-open');
                const icon = navToggle.querySelector('i');
                if (icon) {
                    icon.classList.toggle('bi-list');
                    icon.classList.toggle('bi-x');
                }
            });

            // Cerrar menú móvil al hacer clic en un enlace
            mainNav.querySelectorAll('.nav-link-ed').forEach(function(link) {
                link.addEventListener('click', function() {
                    mainNav.classList.remove('is-open');
                    const icon = navToggle.querySelector('i');
                    if (icon) {
                        icon.classList.add('bi-list');
                        icon.classList.remove('bi-x');
                    }
                });
            });
        }
    </script>
</body>

</html>
