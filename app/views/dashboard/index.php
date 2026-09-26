<?php
$pageTitle = "Dashboard - Valle Sereno Spa";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="public/css/spa-theme.css">
</head>
<body style="background-color: #f4f7f5;">

    <!-- Barra de Navegación -->
    <nav class="navbar navbar-expand-lg spa-navbar navbar-dark py-3 px-4 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand spa-brand" href="#">
                <i class="bi bi-flower1 me-2 text-warning"></i>Valle <span>Sereno</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end text-light">
                        <div class="fw-semibold"><?= htmlspecialchars($usuario['nombre']) ?></div>
                        <small class="badge bg-warning text-dark px-2 py-1"><?= htmlspecialchars($usuario['rol']) ?></small>
                    </div>
                    <a href="index.php?c=auth&a=logout" class="btn btn-outline-light btn-sm px-3 rounded-pill">
                        <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container my-5">
        <!-- Banner de Bienvenida -->
        <div class="p-5 rounded-4 mb-4 text-white shadow" style="background: linear-gradient(135deg, #1e3d34 0%, #305e50 100%); border-left: 6px solid #c5a059;">
            <span class="badge bg-light text-dark mb-2 px-3 py-2 rounded-pill fw-semibold">
                <i class="bi bi-shield-check text-success me-1"></i> Autenticación Exitosa por Rol: <?= htmlspecialchars($usuario['rol']) ?>
            </span>
            <h1 class="display-6 fw-bold" style="font-family: var(--font-serif);">
                ¡Bienvenido(a), <?= htmlspecialchars($usuario['nombre']) ?>!
            </h1>
            <p class="lead mb-0 text-white-50">
                Has ingresado al sistema de gestión de Spa Valle Sereno. La base de datos y la arquitectura MVC se encuentran conectadas y operando correctamente.
            </p>
        </div>

        <!-- Módulos según Rol -->
        <h4 class="fw-bold mb-4" style="color: var(--spa-primary);">Módulos del Sistema</h4>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-3 rounded-circle me-3" style="background: #eef5f1; color: var(--spa-primary);">
                                <i class="bi bi-calendar2-heart fs-3"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-0">Citas & Terapias</h5>
                        </div>
                        <p class="card-text text-muted small">
                            Agendamiento de cabinas, hidroterapias, masajes relajantes y control de sesiones.
                        </p>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Listo para módulos N-Capas</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-3 rounded-circle me-3" style="background: #fdf5ea; color: #b38f4a;">
                                <i class="bi bi-people-fill fs-3"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-0">Clientes & Membresías</h5>
                        </div>
                        <p class="card-text text-muted small">
                            Ficha clínica de salud, preferencias de aromaterapia y membresías VIP.
                        </p>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Listo para vincular</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-3 rounded-circle me-3" style="background: #ebf5fb; color: #2980b9;">
                                <i class="bi bi-sliders fs-3"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-0">Configuración & Roles</h5>
                        </div>
                        <p class="card-text text-muted small">
                            Gestión de permisos, terapeutas, catálogos de servicios y reportes financieros.
                        </p>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Base de Datos OK</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
