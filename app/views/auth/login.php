<?php
$pageTitle = "Iniciar Sesión | Valle Sereno Spa & Wellness";
$errorFlash = $_SESSION['error_flash'] ?? null;
unset($_SESSION['error_flash']);
$logoutSuccess = isset($_GET['logout']) && $_GET['logout'] === 'success';
$sessionExpired = isset($_GET['expired']) && $_GET['expired'] === '1';
$csrfToken = AuthHelper::csrfToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <!-- Google Fonts: Playfair Display & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Estilo Armónico Personalizado Valle Sereno -->
    <link rel="stylesheet" href="public/css/spa-theme.css">
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
                            <i class="bi bi-flower1"></i> Santuario & Bienestar
                        </span>
                        <h1 class="brand-title">Valle <span>Sereno</span></h1>
                        <p class="brand-lead">
                            Sistema integral de gestión de bienestar, cabinas de relajación, agenda terapéutica y experiencias sensoriales de alta gama.
                        </p>
                    </div>

                    <!-- Carrusel Armónico de Servicios de Spa -->
                    <div class="spa-services-carousel">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-uppercase text-light" style="font-size:0.72rem; letter-spacing:1px; color:#d2dec3 !important;">
                                <i class="bi bi-stars text-warning me-1"></i> Experiencias Destacadas
                            </small>
                            <span class="badge rounded-pill bg-dark-subtle text-light border border-secondary" style="font-size:0.68rem;">Catálogo Spa</span>
                        </div>

                        <div id="spaServicesCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3500">
                            <div class="carousel-inner">
                                <!-- Servicio 1 -->
                                <div class="carousel-item active">
                                    <div class="service-card-slide">
                                        <div class="service-icon-box">
                                            <i class="bi bi-fire"></i>
                                        </div>
                                        <div>
                                            <h6 class="service-info-title">Piedras Volcánicas Calientes</h6>
                                            <p class="service-info-desc">Termoterapia profunda con aceites esenciales para disolver tensión muscular.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Servicio 2 -->
                                <div class="carousel-item">
                                    <div class="service-card-slide">
                                        <div class="service-icon-box">
                                            <i class="bi bi-droplet-half"></i>
                                        </div>
                                        <div>
                                            <h6 class="service-info-title">Aromaterapia & Lavanda</h6>
                                            <p class="service-info-desc">Inmersión olfativa y masajes relajantes que restauran el equilibrio nervioso.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Servicio 3 -->
                                <div class="carousel-item">
                                    <div class="service-card-slide">
                                        <div class="service-icon-box">
                                            <i class="bi bi-water"></i>
                                        </div>
                                        <div>
                                            <h6 class="service-info-title">Circuito de Hidroterapia</h6>
                                            <p class="service-info-desc">Pozas termales, jacuzzi de esencias minerales y duchas de contraste suizo.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Servicio 4 -->
                                <div class="carousel-item">
                                    <div class="service-card-slide">
                                        <div class="service-icon-box">
                                            <i class="bi bi-sparkles"></i>
                                        </div>
                                        <div>
                                            <h6 class="service-info-title">Facial Iluminador de Oro & Rosa</h6>
                                            <p class="service-info-desc">Nutrición botánica antioxidante para devolver vitalidad y luminosidad celular.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Indicadores del Carrusel -->
                            <div class="carousel-indicators spa-carousel-indicators">
                                <button type="button" data-bs-target="#spaServicesCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Servicio 1"></button>
                                <button type="button" data-bs-target="#spaServicesCarousel" data-bs-slide-to="1" aria-label="Servicio 2"></button>
                                <button type="button" data-bs-target="#spaServicesCarousel" data-bs-slide-to="2" aria-label="Servicio 3"></button>
                                <button type="button" data-bs-target="#spaServicesCarousel" data-bs-slide-to="3" aria-label="Servicio 4"></button>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Panel Formulario de Login -->
                <div class="col-lg-7 login-form-panel d-flex flex-column justify-content-center">
                    <div class="mb-3">
                        <h2 class="h4 fw-bold mb-1" style="font-family: var(--font-serif); color: var(--spa-primary);">
                            Bienvenido de nuevo
                        </h2>
                        <p class="text-muted small mb-0">
                            Ingrese sus credenciales para acceder a su espacio de trabajo.
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
                        <!-- Campo Usuario o Correo -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Usuario o Correo Institucional</label>
                            <div class="input-icon-wrapper">
                                <input type="text" 
                                       class="form-control form-control-spa" 
                                       id="email" 
                                       name="email" 
                                       placeholder="Ingrese su usuario o correo" 
                                       required 
                                       autocomplete="off"
                                       spellcheck="false">
                                <i class="bi bi-person-circle prefix-icon"></i>
                            </div>
                            <div class="invalid-feedback d-block mt-1" id="emailError" style="display:none !important; font-size: 0.8rem;"></div>
                        </div>

                        <!-- Campo Contraseña -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="form-label mb-0">Contraseña</label>
                                <a href="javascript:void(0)" onclick="mostrarAlertaSoporte()" class="text-decoration-none small" style="color: var(--spa-secondary); font-weight:500;">
                                    ¿Olvidó su contraseña?
                                </a>
                            </div>
                            <div class="input-icon-wrapper">
                                <input type="password" 
                                       class="form-control form-control-spa" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Ingrese su contraseña" 
                                       required 
                                       autocomplete="new-password">
                                <i class="bi bi-lock prefix-icon"></i>
                                <button type="button" class="toggle-password-btn" id="togglePasswordBtn" title="Mostrar/Ocultar contraseña" tabindex="-1">
                                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback d-block mt-1" id="passwordError" style="display:none !important; font-size: 0.8rem;"></div>
                        </div>

                        <!-- Checkbox Recuérdame -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe" name="rememberMe">
                                <label class="form-check-label text-muted small" for="rememberMe">
                                    Recordar este dispositivo
                                </label>
                            </div>
                            <span class="badge bg-light text-secondary border">Seguridad Activa</span>
                        </div>

                        <!-- Botón de Envío -->
                        <button type="submit" class="btn btn-spa-primary w-100" id="btnSubmit">
                            <span id="btnSubmitText">Ingresar al Sistema</span>
                            <i class="bi bi-arrow-right-short fs-4"></i>
                        </button>
                    </form>

                    <div class="mt-3 pt-2 text-center text-muted small">
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
                    } else if (data.inexistente) {
                        showAlert(data.message, 'danger');
                        userInput.classList.add('is-invalid');
                        emailError.textContent = 'Ingrese un usuario o correo corporativo válido.';
                        emailError.style.setProperty('display', 'block', 'important');
                        passInput.value = '';
                        userInput.focus();
                        btnSubmit.disabled = false;
                        btnSubmitText.textContent = 'Ingresar al Sistema';
                    } else {
                        showAlert(data.message || 'Error en las credenciales proporcionadas.', 'danger');
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
