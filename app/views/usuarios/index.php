<?php
$csrfToken = $csrfToken ?? AuthHelper::csrfToken();
$usuarios = $usuarios ?? [];
$roles = $catalogos['roles'] ?? [];
$empleados = $catalogos['empleados'] ?? [];
$currentUserId = $_SESSION['usuario_id'] ?? 0;

$totalUsuarios = count($usuarios);
$totalActivos = count(array_filter($usuarios, fn($u) => ($u['estado'] ?? '') === 'activo'));
$totalBloqueados = count(array_filter($usuarios, fn($u) => ($u['estado'] ?? '') === 'bloqueado'));
$totalAdmins = count(array_filter($usuarios, fn($u) => ($u['rol_nombre'] ?? '') === 'Administrador'));
?>

<!-- SaaS Page Header -->
<div class="saas-page-header">
    <div>
        <div class="saas-breadcrumb">
            <i class="bi bi-shield-lock-fill"></i>
            <span>Administración del Sistema</span>
            <span class="sep">/</span>
            <span>Control de Accesos</span>
        </div>
        <h1 class="saas-title">Gestión de Usuarios &amp; Permisos</h1>
        <p class="saas-subtitle">Administración de credenciales, roles de seguridad, monitoreo de intentos fallidos y vinculación con personal.</p>
    </div>
    <div class="saas-header-actions">
        <button class="saas-btn saas-btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="abrirModalCrear()">
            <i class="bi bi-person-plus-fill"></i>
            <span>Nuevo Usuario</span>
        </button>
    </div>
</div>

<!-- SaaS KPI Stats Grid -->
<div class="saas-stats-grid mb-4">
    <div class="saas-stat-card">
        <div class="saas-stat-header">
            <span class="saas-stat-label">Cuentas Registradas</span>
            <div class="saas-stat-icon icon-teal">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= $totalUsuarios ?></div>
        <div class="saas-stat-change">
            <span class="badge-neutral"><i class="bi bi-person-badge"></i> Directorio</span>
            Credenciales en base de datos
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-header">
            <span class="saas-stat-label">Usuarios Activos</span>
            <div class="saas-stat-icon icon-emerald">
                <i class="bi bi-shield-check"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= $totalActivos ?></div>
        <div class="saas-stat-change">
            <span class="badge-positive"><i class="bi bi-check-circle"></i> Habilitados</span>
            Con acceso al sistema
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-header">
            <span class="saas-stat-label">Administradores</span>
            <div class="saas-stat-icon icon-gold">
                <i class="bi bi-key-fill"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= $totalAdmins ?></div>
        <div class="saas-stat-change">
            <span class="badge-neutral"><i class="bi bi-award"></i> Nivel Total</span>
            Control global
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-header">
            <span class="saas-stat-label">Cuentas Bloqueadas</span>
            <div class="saas-stat-icon icon-indigo">
                <i class="bi bi-shield-exclamation"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= $totalBloqueados ?></div>
        <div class="saas-stat-change">
            <?php if ($totalBloqueados > 0): ?>
                <span class="saas-badge saas-badge-danger"><i class="bi bi-lock-fill"></i> Requiere atención</span>
            <?php else: ?>
                <span class="badge-positive"><i class="bi bi-check2"></i> Sin bloqueos</span>
            <?php endif; ?>
            Seguridad activa
        </div>
    </div>
</div>

<!-- Buscador & Filtro por Rol -->
<div class="saas-card mb-3">
    <div class="py-2 px-3">
        <div class="row g-2 align-items-center justify-content-between">
            <div class="col-md-6">
                <div class="saas-search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="filtroUsuarios" placeholder="Buscar usuario por nombre, login, rol o correo..." onkeyup="filtrarGaleriaUsuarios()">
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-md-end">
                <div class="saas-filter-tabs" role="group" aria-label="Filtro por rol">
                    <button type="button" class="saas-filter-tab active" onclick="filtrarRolUsuario('todos', this)">Todos (<?= $totalUsuarios ?>)</button>
                    <button type="button" class="saas-filter-tab" onclick="filtrarRolUsuario('Administrador', this)">Admin</button>
                    <button type="button" class="saas-filter-tab" onclick="filtrarRolUsuario('Recepcionista', this)">Recepción</button>
                    <button type="button" class="saas-filter-tab" onclick="filtrarRolUsuario('Terapeuta', this)">Terapeutas</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Galería Grid de Usuarios -->
<div class="row g-3 mb-3" id="galeriaUsuarios">
    <?php if (empty($usuarios)): ?>
        <div class="col-12">
            <div class="saas-card text-center py-5">
                <div class="saas-stat-icon icon-teal mx-auto mb-3" style="width: 56px; height: 56px; font-size: 1.5rem;">
                    <i class="bi bi-person-x"></i>
                </div>
                <h5 class="fw-bold mb-1">No hay cuentas de usuario registradas</h5>
                <p class="text-muted small mb-3">Haz clic en "Nuevo Usuario" para agregar una credencial de acceso.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($usuarios as $u): 
            $initials = strtoupper(substr($u['usuario'], 0, 2));
            $rolIcon = match($u['rol_nombre']) {
                'Administrador' => 'bi-shield-fill-check',
                'Recepcionista' => 'bi-headset',
                'Terapeuta'     => 'bi-heart-pulse-fill',
                default         => 'bi-person-badge'
            };
            $rolBadgeClass = match($u['rol_nombre']) {
                'Administrador' => 'saas-badge-gold',
                'Recepcionista' => 'saas-badge-info',
                'Terapeuta'     => 'saas-badge-success',
                default         => 'saas-badge-neutral'
            };
        ?>
            <div class="col-sm-6 col-lg-4 col-xl-3 tarjeta-usuario-item" data-rol="<?= htmlspecialchars($u['rol_nombre']) ?>">
                <div class="saas-card p-3 h-100 position-relative d-flex flex-column justify-content-between" style="transition: all .2s ease;">
                    
                    <div>
                        <!-- Header Tarjeta -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                                     style="width: 46px; height: 46px; font-size: 0.95rem; flex-shrink: 0; background: <?= $u['estado'] === 'bloqueado' ? '#dc2626' : 'linear-gradient(135deg, #1e3d34, #2c5341)' ?>;">
                                    <?= htmlspecialchars($initials) ?>
                                </div>
                                <div style="min-width: 0;">
                                    <h6 class="fw-bold text-dark mb-0 text-truncate" title="<?= htmlspecialchars($u['usuario']) ?>"><?= htmlspecialchars($u['usuario']) ?></h6>
                                    <small class="text-muted font-monospace" style="font-size: 0.72rem;">ID #<?= $u['id'] ?></small>
                                </div>
                            </div>
                            <?php if ($u['estado'] === 'activo'): ?>
                                <span class="saas-badge saas-badge-success">Activo</span>
                            <?php elseif ($u['estado'] === 'bloqueado'): ?>
                                <span class="saas-badge saas-badge-danger" title="Bloqueado por superar 3 intentos fallidos">Bloqueado</span>
                            <?php else: ?>
                                <span class="saas-badge saas-badge-neutral">Inactivo</span>
                            <?php endif; ?>
                        </div>

                        <!-- Detalles del Usuario & Empleado -->
                        <div class="p-2 px-3 rounded-2 mb-3" style="background: #f8fafc; border: 1px solid #f1f5f9; font-size: 0.8rem;">
                            <div class="fw-bold text-dark mb-1 text-truncate" title="<?= htmlspecialchars($u['nombre_completo']) ?>">
                                <i class="bi bi-person text-success me-2"></i><?= htmlspecialchars($u['nombre_completo']) ?>
                            </div>
                            <div class="text-muted mb-1 text-truncate">
                                <i class="bi bi-briefcase text-primary me-2"></i><?= htmlspecialchars($u['cargo']) ?>
                            </div>
                            <div class="text-muted text-truncate" title="<?= htmlspecialchars($u['email']) ?>">
                                <i class="bi bi-envelope me-2"></i><?= htmlspecialchars($u['email']) ?>
                            </div>
                        </div>

                        <!-- Rol & Fallos -->
                        <div class="d-flex align-items-center justify-content-between mb-3" style="font-size: 0.78rem;">
                            <span class="saas-badge <?= $rolBadgeClass ?>">
                                <i class="bi <?= $rolIcon ?> me-1"></i><?= htmlspecialchars($u['rol_nombre']) ?>
                            </span>
                            <div>
                                <?php if ((int)($u['intentos_fallidos'] ?? 0) > 0): ?>
                                    <span class="saas-badge saas-badge-danger">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i><?= (int)$u['intentos_fallidos'] ?> fallo(s)
                                    </span>
                                <?php else: ?>
                                    <small class="text-success fw-semibold"><i class="bi bi-shield-check me-1"></i>0 fallos</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Acciones -->
                    <div class="pt-3 border-top d-flex gap-2 justify-content-between align-items-center">
                        <?php if ($u['estado'] === 'bloqueado'): ?>
                            <button class="saas-btn saas-btn-primary w-100 py-1 bg-danger border-danger" style="font-size: 0.78rem;" onclick="desbloquearUsuario(<?= (int)$u['id'] ?>, '<?= htmlspecialchars($u['usuario'], ENT_QUOTES) ?>')">
                                <i class="bi bi-unlock-fill"></i> Desbloquear Cuenta
                            </button>
                        <?php else: ?>
                            <button class="saas-btn saas-btn-outline flex-fill py-1" style="font-size: 0.78rem;" title="Editar Usuario" onclick="abrirModalEditar(<?= (int)$u['id'] ?>)">
                                <i class="bi bi-pencil-square"></i> Editar
                            </button>
                            <button class="saas-btn saas-btn-outline flex-fill py-1" style="font-size: 0.78rem;" title="Cambiar Contraseña" onclick="abrirModalPassword(<?= (int)$u['id'] ?>, '<?= htmlspecialchars($u['usuario'], ENT_QUOTES) ?>')">
                                <i class="bi bi-key text-warning"></i> Clave
                            </button>
                            <?php if ($u['id'] != $currentUserId): ?>
                                <button class="saas-btn saas-btn-outline px-2 py-1 <?= $u['estado'] === 'activo' ? 'text-danger' : 'text-success' ?>" 
                                        title="<?= $u['estado'] === 'activo' ? 'Desactivar cuenta' : 'Activar cuenta' ?>" 
                                        onclick="toggleEstadoUsuario(<?= (int)$u['id'] ?>, '<?= htmlspecialchars($u['usuario'], ENT_QUOTES) ?>', '<?= $u['estado'] ?>')">
                                    <i class="bi <?= $u['estado'] === 'activo' ? 'bi-person-slash' : 'bi-person-check' ?>"></i>
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- MODAL: Crear / Editar Usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-editorial">
            <div class="modal-header">
                <div>
                    <span class="modal-kicker">Acceso</span>
                    <h5 class="modal-title" id="modalUsuarioTitulo">
                        <i class="bi bi-person-fill-gear me-2"></i>Nuevo Usuario
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formUsuario" onsubmit="guardarUsuario(event)">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" id="usuario_id" name="id" value="">

                <div class="modal-body">
                    <div id="modalAlertError" class="alert alert-danger py-2 small d-none"></div>

                    <!-- Usuario -->
                    <div class="mb-3">
                        <label for="inputUsuario" class="form-label">Nombre de usuario <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="inputUsuario" name="usuario" placeholder="ej. nombre.apellido" required minlength="3" maxlength="50" autocomplete="off">
                        </div>
                        <small class="text-muted" style="font-size: 0.74rem;">Alias corto de acceso (alternativo al correo).</small>
                    </div>

                    <!-- Correo institucional -->
                    <div class="mb-3">
                        <label for="inputEmail" class="form-label">Correo institucional <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="inputEmail" name="email" placeholder="usuario@vallesereno.com" required maxlength="100" autocomplete="off">
                        </div>
                        <small class="text-muted" style="font-size: 0.74rem;">Credencial principal para iniciar sesión.</small>
                    </div>

                    <!-- Empleado Vinculado -->
                    <div class="mb-3">
                        <label for="selectEmpleado" class="form-label">Vincular a empleado <span class="text-muted">(opcional)</span></label>
                        <select class="form-select" id="selectEmpleado" name="id_empleado">
                            <option value="">-- Sin vincular a personal --</option>
                            <?php foreach ($empleados as $emp): ?>
                                <option value="<?= $emp['id'] ?>">
                                    <?= htmlspecialchars($emp['nombre_completo']) ?> (<?= htmlspecialchars($emp['cargo']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted" style="font-size: 0.74rem;">Asocia la cuenta con su ficha de terapeuta o recepcionista.</small>
                    </div>

                    <!-- Rol -->
                    <div class="mb-3">
                        <label for="selectRol" class="form-label">Rol en el spa <span class="text-danger">*</span></label>
                        <select class="form-select" id="selectRol" name="id_rol" required>
                            <option value="">-- Seleccionar Rol --</option>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nombre']) ?> - <?= htmlspecialchars($r['descripcion']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Contraseña (Solo en creación) -->
                    <div class="mb-3" id="campoPasswordWrapper">
                        <label for="inputPassword" class="form-label">Contraseña inicial <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="inputPassword" name="password" placeholder="Mínimo 8 caracteres (letras y números)" minlength="8" autocomplete="new-password">
                        </div>
                        <small class="text-muted" style="font-size: 0.74rem;">Se encriptará con BCRYPT antes de persistirse.</small>
                    </div>

                    <!-- Estado -->
                    <div class="mb-0">
                        <label for="selectEstado" class="form-label">Estado de la cuenta</label>
                        <select class="form-select" id="selectEstado" name="estado">
                            <option value="activo">Activo (Habilitado para ingresar)</option>
                            <option value="inactivo">Inactivo (Bloqueado)</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-quiet btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="saas-btn saas-btn-primary px-4" id="btnGuardarUsuario">
                        <i class="bi bi-check2-circle me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: Cambiar Contraseña -->
<div class="modal fade" id="modalPassword" tabindex="-1" aria-labelledby="modalPasswordTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content modal-editorial">
            <div class="modal-header">
                <div>
                    <span class="modal-kicker">Seguridad</span>
                    <h5 class="modal-title" id="modalPasswordTitulo">
                        <i class="bi bi-key-fill me-2"></i>Cambiar Contraseña
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPassword" onsubmit="guardarNuevaPassword(event)">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" id="pass_usuario_id" name="id" value="">

                <div class="modal-body">
                    <p class="small text-muted mb-2">
                        Actualizar credencial de acceso para: <strong id="passUsuarioNombre" class="text-dark"></strong>
                    </p>
                    <div id="modalPassAlertError" class="alert alert-danger py-1 small d-none"></div>

                    <div class="mb-0">
                        <label for="inputNuevaPass" class="form-label">Nueva contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="inputNuevaPass" name="password" placeholder="Mínimo 8 caracteres (letras y números)" minlength="8" required autocomplete="new-password">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-quiet btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="saas-btn saas-btn-primary px-4" id="btnGuardarPass">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// CSRF_TOKEN y el parche global de fetch (X-CSRF-TOKEN / X-Requested-With)
// los define el layout base app/views/layouts/main.php para todo el sistema.

let filtroRolActual = 'todos';

function filtrarGaleriaUsuarios() {
    const q = document.getElementById('filtroUsuarios').value.toLowerCase();
    const cards = document.querySelectorAll('#galeriaUsuarios .tarjeta-usuario-item');

    cards.forEach(c => {
        const text = c.innerText.toLowerCase();
        const rol = c.getAttribute('data-rol') || '';

        const coincideTexto = text.includes(q);
        const coincideRol = (filtroRolActual === 'todos' || rol === filtroRolActual);

        if (coincideTexto && coincideRol) {
            c.style.display = '';
        } else {
            c.style.display = 'none';
        }
    });
}

function filtrarRolUsuario(rol, btn) {
    filtroRolActual = rol;
    document.querySelectorAll('.saas-filter-tabs .saas-filter-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    filtrarGaleriaUsuarios();
}

function abrirModalCrear() {
    document.getElementById('formUsuario').reset();
    document.getElementById('usuario_id').value = '';
    document.getElementById('modalUsuarioTitulo').innerHTML = '<i class="bi bi-person-plus-fill me-2 text-warning"></i> Nuevo Usuario';
    document.getElementById('campoPasswordWrapper').style.display = 'block';
    document.getElementById('inputPassword').setAttribute('required', 'required');
    document.getElementById('modalAlertError').classList.add('d-none');
}

async function abrirModalEditar(id) {
    try {
        const res = await fetch(`index.php?c=usuarios&a=get&id=${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();
        if (!json.success) {
            alert(json.message || 'Error al cargar usuario');
            return;
        }

        const u = json.data;
        document.getElementById('usuario_id').value = u.id;
        document.getElementById('inputUsuario').value = u.usuario;
        document.getElementById('inputEmail').value = u.email || '';
        document.getElementById('selectEmpleado').value = u.id_empleado || '';
        document.getElementById('selectRol').value = u.id_rol;
        document.getElementById('selectEstado').value = u.estado;

        // Ocultar campo password al editar (se cambia por modal dedicado)
        document.getElementById('campoPasswordWrapper').style.display = 'none';
        document.getElementById('inputPassword').removeAttribute('required');

        document.getElementById('modalUsuarioTitulo').innerHTML = `<i class="bi bi-pencil-square me-2 text-warning"></i> Editar Usuario: ${u.usuario}`;
        document.getElementById('modalAlertError').classList.add('d-none');

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalUsuario'));
        modal.show();
    } catch (e) {
        alert('Error de conexión al cargar datos del usuario.');
    }
}

async function guardarUsuario(e) {
    e.preventDefault();
    const id = document.getElementById('usuario_id').value;
    const accion = id ? 'actualizar' : 'guardar';
    const form = document.getElementById('formUsuario');
    const formData = new FormData(form);

    const btn = document.getElementById('btnGuardarUsuario');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Guardando...';

    try {
        const res = await fetch(`index.php?c=usuarios&a=${accion}`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();

        if (json.success) {
            window.location.reload();
        } else {
            const errDiv = document.getElementById('modalAlertError');
            errDiv.innerText = json.message || 'Ocurrió un error.';
            errDiv.classList.remove('d-none');
        }
    } catch (err) {
        alert('Error en la comunicación con el servidor.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Guardar';
    }
}

function abrirModalPassword(id, usuario) {
    document.getElementById('formPassword').reset();
    document.getElementById('pass_usuario_id').value = id;
    document.getElementById('passUsuarioNombre').innerText = usuario;
    document.getElementById('modalPassAlertError').classList.add('d-none');

    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPassword'));
    modal.show();
}

async function guardarNuevaPassword(e) {
    e.preventDefault();
    const form = document.getElementById('formPassword');
    const formData = new FormData(form);

    const btn = document.getElementById('btnGuardarPass');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    try {
        const res = await fetch('index.php?c=usuarios&a=cambiarPassword', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();

        if (json.success) {
            alert(json.message);
            bootstrap.Modal.getInstance(document.getElementById('modalPassword')).hide();
        } else {
            const errDiv = document.getElementById('modalPassAlertError');
            errDiv.innerText = json.message || 'Error al cambiar contraseña.';
            errDiv.classList.remove('d-none');
        }
    } catch (err) {
        alert('Error de conexión con el servidor.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Actualizar';
    }
}

async function toggleEstadoUsuario(id, usuario, estadoActual) {
    const accionTxt = estadoActual === 'activo' ? 'desactivar' : 'activar';
    if (!confirm(`¿Está seguro de ${accionTxt} la cuenta de "${usuario}"?`)) {
        return;
    }

    const formData = new FormData();
    formData.append('id', id);
    formData.append('csrf_token', CSRF_TOKEN);

    try {
        const res = await fetch('index.php?c=usuarios&a=toggleEstado', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();
        if (json.success) {
            window.location.reload();
        } else {
            alert(json.message || 'Error al alternar estado.');
        }
    } catch (e) {
        alert('Error al procesar la solicitud.');
    }
}

async function desbloquearUsuario(id, usuario) {
    if (!confirm(`¿Desea desbloquear la cuenta de "${usuario}" y reiniciar sus intentos fallidos a 0?`)) {
        return;
    }

    const formData = new FormData();
    formData.append('id', id);
    formData.append('csrf_token', CSRF_TOKEN);

    try {
        const res = await fetch('index.php?c=usuarios&a=desbloquear', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();
        if (json.success) {
            window.location.reload();
        } else {
            alert(json.message || 'Error al desbloquear usuario.');
        }
    } catch (e) {
        alert('Error de conexión al procesar el desbloqueo.');
    }
}
</script>
