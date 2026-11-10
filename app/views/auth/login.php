<?php
$pageTitle = "Iniciar Sesión | Valle Sereno Spa & Wellness";
$errorFlash = $_SESSION['error_flash'] ?? null;
unset($_SESSION['error_flash']);
$logoutSuccess = isset($_GET['logout']) && $_GET['logout'] === 'success';
$sessionExpired = isset($_GET['expired']) && $_GET['expired'] === '1';
$csrfToken = AuthHelper::csrfToken();

// Iconografía del carrusel según el servicio más vendido (por palabras clave)
$iconoParaServicio = function (string $nombre): string {
    $n = mb_strtolower($nombre, 'UTF-8');
    $mapa = [
        'piedra'    => 'bi-fire',
        'volc'      => 'bi-fire',
        'aroma'     => 'bi-droplet-half',
        'lavanda'   => 'bi-droplet-half',
        'hidro'     => 'bi-water',
        'circuito'  => 'bi-water',
        'baño'      => 'bi-water',
        'tina'      => 'bi-water',
        'termal'    => 'bi-water',
        'facial'    => 'bi-stars',
        'colágeno'  => 'bi-gem',
        'colageno'  => 'bi-gem',
        'velo'      => 'bi-gem',
        'oro'       => 'bi-gem',
        'cuencos'   => 'bi-music-note-beamed',
        'tibetan'   => 'bi-music-note-beamed',
        'reflex'    => 'bi-activity',
        'drenaje'   => 'bi-activity',
        'ayurv'     => 'bi-flower1',
        'ritual'    => 'bi-gem',
        'día de spa' => 'bi-gem',
        'paquete'   => 'bi-gem',
    ];
    foreach ($mapa as $clave => $icono) {
        if (mb_strpos($n, $clave) !== false) {
            return $icono;
        }
    }
    return 'bi-flower2';
};
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Estilo Armónico Personalizado Valle Sereno -->
    <link rel="stylesheet" href="public/css/spa-theme.css?v=3.2">
</head>
<body class="login-body">

    <!-- Esferas de luz ambiental suave -->
    <div class="bg-ambient-blob blob-1"></div>
    <div class="bg-ambient-blob blob-2"></div>

    <div class="container login-card-container">
        <div class="login-card">
            <div class="row g-0">
                <!-- Panel Lateral: Marca, Filosofía y Acceso Rápido por Roles -->
                <div class="col-lg-5 login-brand-panel">
                    <div>
                        <span class="brand-badge">
                            <i class="bi bi-flower1"></i> Plataforma SaaS &amp; Wellness
                        </span>
                        <h1 class="brand-title mt-3" style="font-weight: 800; letter-spacing: -0.03em;">Valle <span>Sereno</span></h1>
                        <p class="brand-lead">
                            Sistema empresarial de gestión de bienestar, cabinas de relajación, agenda terapéutica, control de ingresos y expedientes clínicos.
                        </p>
                    </div>

                    <!-- Carrusel Armónico de Servicios Más Vendidos -->
                    <?php if (!empty($serviciosDestacados)): ?>
                    <div class="spa-services-carousel">
                        <div class="mb-2 d-flex align-items-center justify-content-between">
                            <small class="text-uppercase fw-bold" style="font-size:0.68rem; letter-spacing:1px; color:#d2dec3;">
                                <i class="bi bi-stars text-warning me-1"></i> Experiencias Destacadas
                            </small>
                            <span class="badge rounded-pill" style="background: rgba(255,255,255,0.12); font-size: 0.65rem;">Catálogo Activo</span>
                        </div>

                        <div id="spaServicesCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3500">
                            <div class="carousel-inner">
                                <?php foreach ($serviciosDestacados as $i => $serv): ?>
                                <?php
                                    $desc = trim((string)($serv['descripcion'] ?? ''));
                                    if ($desc === '') {
                                        $desc = 'Tratamiento exclusivo de nuestro catálogo de bienestar.';
                                    }
                                    if (mb_strlen($desc, 'UTF-8') > 118) {
                                        $desc = mb_substr($desc, 0, 115, 'UTF-8') . '…';
                                    }
                                ?>
                                <!-- Servicio <?= $i + 1 ?>: <?= htmlspecialchars($serv['nombre'], ENT_QUOTES, 'UTF-8') ?> -->
                                <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                                    <div class="service-card-slide">
                                        <div class="service-icon-box">
                                            <i class="bi <?= $iconoParaServicio($serv['nombre']) ?>"></i>
                                        </div>
                                        <div>
                                            <h6 class="service-info-title"><?= htmlspecialchars($serv['nombre'], ENT_QUOTES, 'UTF-8') ?></h6>
                                            <p class="service-info-desc"><?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if (count($serviciosDestacados) > 1): ?>
                            <!-- Indicadores del Carrusel -->
                            <div class="carousel-indicators spa-carousel-indicators">
                                <?php foreach ($serviciosDestacados as $i => $serv): ?>
                                <button type="button" data-bs-target="#spaServicesCarousel" data-bs-slide-to="<?= $i ?>"<?= $i === 0 ? ' class="active" aria-current="true"' : '' ?> aria-label="Servicio <?= $i + 1 ?>"></button>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>

                <!-- Panel Formulario de Login -->
                <div class="col-lg-7 login-form-panel d-flex flex-column justify-content-center">
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center gap-2 px-2 py-1 rounded-pill mb-2" style="background: #f0fdf4; border: 1px solid #bbf7d0; font-size: 0.72rem; font-weight: 600; color: #166534;">
                            <i class="bi bi-shield-lock-fill"></i> Acceso Corporativo Seguro
                        </div>
                        <h2 class="h4 fw-bold mb-1" style="color: #0f172a; letter-spacing: -0.02em;">
                            Bienvenido de nuevo
                        </h2>
                        <p class="text-muted small mb-0">
                            Ingrese sus credenciales institucionales para acceder a su estación de trabajo.
                        </p>
                    </div>

                    <!-- Mensajes de Alerta -->
                    <div id="loginAlertBox" style="<?= ($errorFlash || $logoutSuccess || $sessionExpired) ? '' : 'display:none;' ?>">
                        <?php if ($sessionExpired): ?>
                            <div class="alert alert-warning spa-alert mb-3" role="alert">
                                <i class="bi bi-clock-history fs-5"></i>
                                <div>Su sesión ha expirado por inactividad. Por favor ingrese de nuevo.</div>
                            </div>
                        <?php elseif ($errorFlash): ?>
                            <div class="alert alert-danger spa-alert mb-3" role="alert">
                                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                <div><?= htmlspecialchars($errorFlash) ?></div>
                            </div>
                        <?php elseif ($logoutSuccess): ?>
                            <div class="alert alert-success spa-alert mb-3" role="alert">
                                <i class="bi bi-check-circle-fill fs-5"></i>
                                <div>Ha cerrado sesión correctamente. ¡Esperamos verte pronto!</div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Formulario con validación limpia y sin autocompletado de prueba -->
                    <form id="loginForm" method="POST" action="index.php?c=auth&a=authenticate" autocomplete="off" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <!-- Campo Correo institucional -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo o Usuario Institucional</label>
                            <div class="input-icon-wrapper">
                                <input type="email" 
                                       class="form-control form-control-spa" 
                                       id="email" 
                                       name="email" 
                                       placeholder="correo@vallesereno.com o usuario" 
                                       required 
                                       autocomplete="username"
                                       spellcheck="false">
                                <i class="bi bi-envelope prefix-icon"></i>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.74rem;">
                                Personal interno: puede ingresar con su correo o alias asignado (ej. nombre.apellido).
                            </small>
                            <div class="invalid-feedback d-block mt-1" id="emailError" style="display:none !important; font-size: 0.8rem;"></div>
                        </div>

                        <!-- Campo Contraseña -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="form-label mb-0">Contraseña</label>
                                <a href="javascript:void(0)" onclick="mostrarAlertaSoporte()" class="text-decoration-none small" style="color: var(--spa-secondary); font-weight:600;">
                                    ¿Olvidó su contraseña?
                                </a>
                            </div>
                            <div class="input-icon-wrapper">
                                <input type="password" 
                                       class="form-control form-control-spa" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Tu contraseña" 
                                       required 
                                       autocomplete="current-password">
                                <i class="bi bi-lock prefix-icon"></i>
                                <button type="button" class="toggle-password-btn" id="togglePasswordBtn" title="Mostrar/Ocultar contraseña" tabindex="-1">
                                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback d-block mt-1" id="passwordError" style="display:none !important; font-size: 0.8rem;"></div>
                        </div>

                        <!-- Checkbox Recuérdame -->
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe" name="rememberMe">
                                <label class="form-check-label text-muted small" for="rememberMe">
                                    Recordar este dispositivo
                                </label>
                            </div>
                            <span class="saas-badge saas-badge-neutral"><i class="bi bi-lock-fill me-1"></i>BCRYPT + CSRF</span>
                        </div>

                        <!-- Botón de Envío -->
                        <button type="submit" class="btn btn-spa-primary w-100" id="btnSubmit">
                            <span id="btnSubmitText">Ingresar al Sistema</span>
                            <i class="bi bi-arrow-right-short fs-4"></i>
                        </button>
                    </form>

                    <div class="mt-4 pt-2 border-top text-center text-muted small">
                        ¿Necesita asistencia con sus credenciales? 
                        <a href="javascript:void(0)" onclick="mostrarAlertaSoporte()" class="fw-semibold text-decoration-none" style="color: var(--spa-primary);">
                            Contactar Administrador
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script de interactividad y validación correcta -->
    <script>
        // Alternar visualización de contraseña
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passInput = document.getElementById('password');
        const userInput = document.getElementById('email');
        const toggleIcon = document.getElementById('togglePasswordIcon');
        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');

        toggleBtn.addEventListener('click', function() {
            const isPassword = passInput.getAttribute('type') === 'password';
            passInput.setAttribute('type', isPassword ? 'text' : 'password');
            toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });

        // Limpiar errores visuales al tipear
        userInput.addEventListener('input', function() {
            if (userInput.classList.contains('is-invalid')) {
                userInput.classList.remove('is-invalid');
                emailError.style.setProperty('display', 'none', 'important');
                emailError.textContent = '';
            }
        });

        passInput.addEventListener('input', function() {
            if (passInput.classList.contains('is-invalid')) {
                passInput.classList.remove('is-invalid');
                passwordError.style.setProperty('display', 'none', 'important');
                passwordError.textContent = '';
            }
        });

        // Intercepción con Fetch/AJAX y validación estricta
        const loginForm = document.getElementById('loginForm');
        const alertBox = document.getElementById('loginAlertBox');
        const btnSubmit = document.getElementById('btnSubmit');
        const btnSubmitText = document.getElementById('btnSubmitText');

        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const emailVal = userInput.value.trim();
            const passVal = passInput.value.trim();
            let hasError = false;

            // Validación de campo usuario/correo
            if (!emailVal) {
                userInput.classList.add('is-invalid');
                emailError.textContent = 'Por favor ingrese su usuario o correo institucional.';
                emailError.style.setProperty('display', 'block', 'important');
                hasError = true;
            } else if (emailVal.length < 3) {
                userInput.classList.add('is-invalid');
                emailError.textContent = 'El identificador debe tener al menos 3 caracteres.';
                emailError.style.setProperty('display', 'block', 'important');
                hasError = true;
            }

            // Validación de contraseña
            if (!passVal) {
                passInput.classList.add('is-invalid');
                passwordError.textContent = 'Por favor ingrese su contraseña.';
                passwordError.style.setProperty('display', 'block', 'important');
                hasError = true;
            }

            if (hasError) {
                showAlert('Por favor complete los campos requeridos correctamente.', 'danger');
                if (!emailVal) {
                    userInput.focus();
                } else {
                    passInput.focus();
                }
                return;
            }

            // Ocultar alertas previas durante el envío
            alertBox.style.display = 'none';

            // Estado de carga en el botón
            btnSubmit.disabled = true;
            btnSubmitText.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Verificando...';

            try {
                const formData = new FormData(loginForm);
                formData.append('ajax', '1');

                const response = await fetch('index.php?c=auth&a=authenticate', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showAlert('¡Bienvenido(a) ' + data.user.nombre + '! Accediendo con rol: ' + data.user.rol + '...', 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 800);
                } else {
                    if (data.bloqueado) {
                        showAlert(`
                            <div class="fw-bold mb-1"><i class="bi bi-shield-x me-1"></i> ACCESO DENEGADO - LÍMITE ALCANZADO</div>
                            <div>${data.message}</div>
                            <div class="mt-2 pt-2 border-top border-danger-subtle small">
                                Comuníquese con la Administración: <strong>admin@vallesereno.com</strong>
                            </div>
                        `, 'danger');
                        passInput.value = '';
                        passInput.disabled = true;
                        userInput.disabled = true;
                        btnSubmit.disabled = true;
                        btnSubmitText.innerHTML = '<i class="bi bi-lock-fill me-1"></i> Acceso Bloqueado';
                    } else {
                        showAlert(data.message || 'Correo o contraseña incorrectos.', 'danger');
                        passInput.value = '';
                        passInput.classList.add('is-invalid');
                        passInput.focus();
                        btnSubmit.disabled = false;
                        btnSubmitText.textContent = 'Ingresar al Sistema';
                    }
                }
            } catch (err) {
                // Fallback tradicional en caso de fallo de red
                loginForm.submit();
            }
        });

        function mostrarAlertaSoporte() {
            showAlert('Para restablecer sus credenciales o solicitar acceso, por favor comuníquese con la administración del spa al correo: <strong>soporte@vallesereno.com</strong> o acérquese a Recepción.', 'info');
        }

        function showAlert(message, type) {
            const iconMap = {
                'success': 'bi-check-circle-fill',
                'danger': 'bi-exclamation-triangle-fill',
                'info': 'bi-info-circle-fill'
            };
            const icon = iconMap[type] || 'bi-info-circle-fill';
            alertBox.style.display = 'block';
            alertBox.innerHTML = `
                <div class="alert alert-${type} spa-alert" role="alert">
                    <i class="bi ${icon} fs-5"></i>
                    <div>${message}</div>
                </div>
            `;
            alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    </script>
</body>
</html>
