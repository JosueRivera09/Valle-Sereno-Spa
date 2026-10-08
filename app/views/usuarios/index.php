<?php
$csrfToken = $csrfToken ?? AuthHelper::csrfToken();
$usuarios = $usuarios ?? [];
$roles = $catalogos['roles'] ?? [];
$empleados = $catalogos['empleados'] ?? [];
$currentUserId = $_SESSION['usuario_id'] ?? 0;
?>

<!-- Header de Sección -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--spa-primary); font-family: var(--font-serif);">
            <i class="bi bi-shield-lock-fill text-warning me-2"></i> Gestión de Usuarios
        </h4>
        <p class="text-muted small mb-0">Control de cuentas de acceso al sistema, asignación de roles corporativos y credenciales de seguridad.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-spa-primary btn-sm px-3 d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="abrirModalCrear()">
            <i class="bi bi-person-plus-fill"></i>
            <span>Nuevo Usuario</span>
        </button>
    </div>
</div>

<!-- Métricas rápidas de seguridad -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 text-white" style="background: var(--spa-primary);">
                <i class="bi bi-people-fill fs-3"></i>
            </div>
            <div>
                <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem; letter-spacing: 0.8px;">Total Cuentas</small>
                <h4 class="fw-bold mb-0 text-dark"><?= count($usuarios) ?> Usuarios</h4>
                <small class="text-muted" style="font-size: 0.75rem;">En el sistema</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 bg-success text-white">
                <i class="bi bi-shield-check fs-3"></i>
            </div>
            <div>
                <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem; letter-spacing: 0.8px;">Cuentas Activas</small>
                <h4 class="fw-bold mb-0 text-success">
                    <?= count(array_filter($usuarios, fn($u) => $u['estado'] === 'activo')) ?> Habilitadas
                </h4>
                <small class="text-success" style="font-size: 0.75rem;"><i class="bi bi-check-circle me-1"></i>Acceso normal</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 d-flex align-items-center gap-3">
            <?php 
                $bloqueadas = count(array_filter($usuarios, fn($u) => $u['estado'] === 'bloqueado'));
                $inactivas = count(array_filter($usuarios, fn($u) => $u['estado'] === 'inactivo'));
            ?>
            <div class="rounded-3 p-3 text-white <?= $bloqueadas > 0 ? 'bg-danger' : 'bg-warning' ?>">
                <i class="bi <?= $bloqueadas > 0 ? 'bi-shield-x' : 'bi-lock-fill' ?> fs-3"></i>
            </div>
            <div>
                <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem; letter-spacing: 0.8px;">Bloqueadas / Inactivas</small>
                <h4 class="fw-bold mb-0 <?= $bloqueadas > 0 ? 'text-danger' : 'text-dark' ?>">
                    <?= $bloqueadas + $inactivas ?> Cuentas
                </h4>
                <small class="<?= $bloqueadas > 0 ? 'text-danger fw-semibold' : 'text-muted' ?>" style="font-size: 0.75rem;">
                    <?= $bloqueadas ?> por fallos &bull; <?= $inactivas ?> manuales
                </small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 bg-secondary text-white">
                <i class="bi bi-key-fill fs-3"></i>
            </div>
            <div>
                <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem; letter-spacing: 0.8px;">Seguridad de Accesos</small>
                <h4 class="fw-bold mb-0 text-dark">Máx. 3 Fallos</h4>
                <small class="text-primary" style="font-size: 0.75rem;"><i class="bi bi-shield-lock me-1"></i>Auto-bloqueo activo</small>
            </div>
        </div>
    </div>
</div>

<!-- Buscador & Filtro por Rol -->
<div class="spa-card p-3 mb-4">
    <div class="row g-3 align-items-center justify-content-between">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filtroUsuarios" class="form-control bg-light border-start-0 ps-0" placeholder="Buscar usuario por nombre, login, rol o correo..." onkeyup="filtrarGaleriaUsuarios()">
            </div>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="btn-group btn-group-sm" role="group" aria-label="Filtro por rol">
                <button type="button" class="btn btn-outline-secondary active" onclick="filtrarRolUsuario('todos', this)">Todos (<?= count($usuarios) ?>)</button>
                <button type="button" class="btn btn-outline-secondary" onclick="filtrarRolUsuario('Administrador', this)">Admin</button>
                <button type="button" class="btn btn-outline-secondary" onclick="filtrarRolUsuario('Recepcionista', this)">Recepción</button>
                <button type="button" class="btn btn-outline-secondary" onclick="filtrarRolUsuario('Terapeuta', this)">Terapeutas</button>
            </div>
        </div>
    </div>
</div>

<!-- Galería Grid de Usuarios -->
<div class="row g-4 mb-4" id="galeriaUsuarios">
    <?php if (empty($usuarios)): ?>
        <div class="col-12 text-center py-5 text-muted">
            <i class="bi bi-person-x fs-1 d-block mb-2 text-muted"></i>
            No hay cuentas de usuario registradas en la base de datos.<br>
            <small>Haz clic en "Nuevo Usuario" para agregar uno.</small>
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
            $rolColorClass = match($u['rol_nombre']) {
                'Administrador' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'Recepcionista' => 'bg-info-subtle text-info border border-info-subtle',
                'Terapeuta'     => 'bg-success-subtle text-success border border-success-subtle',
                default         => 'bg-secondary-subtle text-secondary'
            };
            $cardBorderColor = $u['estado'] === 'bloqueado' ? 'border-danger' : ($u['estado'] === 'activo' ? 'border-success' : 'border-secondary');
        ?>
            <div class="col-md-6 col-lg-4 tarjeta-usuario-item" data-rol="<?= htmlspecialchars($u['rol_nombre']) ?>">
                <div class="spa-card p-4 h-100 position-relative d-flex flex-column justify-content-between border-top border-4 <?= $cardBorderColor ?> shadow-sm hover-shadow transition-all">
                    
                    <div>
                        <!-- Header Tarjeta -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                                     style="width: 48px; height: 48px; font-size: 1rem; background: <?= $u['estado'] === 'bloqueado' ? '#dc3545' : 'linear-gradient(135deg, var(--spa-primary), #2c5341)' ?>;">
                                    <?= htmlspecialchars($initials) ?>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0 fs-6"><?= htmlspecialchars($u['usuario']) ?></h6>
                                    <small class="text-muted" style="font-size: 0.73rem;">ID: #<?= $u['id'] ?></small>
                                </div>
                            </div>
                            <?php if ($u['estado'] === 'activo'): ?>
                                <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.7rem;">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>Activo
                                </span>
                            <?php elseif ($u['estado'] === 'bloqueado'): ?>
                                <span class="badge rounded-pill bg-danger text-white px-2 py-1 shadow-sm" style="font-size: 0.7rem;" title="Bloqueado por superar 3 intentos fallidos">
                                    <i class="bi bi-shield-x me-1"></i>Bloqueado
                                </span>
                            <?php else: ?>
                                <span class="badge rounded-pill bg-secondary-subtle text-secondary px-2 py-1" style="font-size: 0.7rem;">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>Inactivo
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Detalles del Usuario & Empleado -->
                        <div class="p-3 rounded-3 mb-3" style="background: #f8faf9; font-size: 0.8rem;">
                            <div class="fw-bold text-dark mb-1">
                                <i class="bi bi-person text-success me-2"></i><?= htmlspecialchars($u['nombre_completo']) ?>
                            </div>
                            <div class="text-muted mb-1">
                                <i class="bi bi-briefcase text-primary me-2"></i><?= htmlspecialchars($u['cargo']) ?>
                            </div>
                            <div class="text-muted text-truncate" title="<?= htmlspecialchars($u['email']) ?>">
                                <i class="bi bi-envelope me-2"></i><?= htmlspecialchars($u['email']) ?>
                            </div>
                        </div>

                        <!-- Rol & Fallos -->
                        <div class="d-flex align-items-center justify-content-between mb-3" style="font-size: 0.78rem;">
                            <span class="badge <?= $rolColorClass ?> px-2 py-1">
                                <i class="bi <?= $rolIcon ?> me-1"></i><?= htmlspecialchars($u['rol_nombre']) ?>
                            </span>
                            <div>
                                <?php if ((int)($u['intentos_fallidos'] ?? 0) > 0): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
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
                            <button class="btn btn-danger btn-sm w-100 fw-semibold d-inline-flex align-items-center justify-content-center gap-1 shadow-sm" onclick="desbloquearUsuario(<?= $u['id'] ?>, '<?= htmlspecialchars($u['usuario']) ?>')">
                                <i class="bi bi-unlock-fill"></i> Desbloquear Cuenta
                            </button>
                        <?php else: ?>
                            <button class="btn btn-outline-secondary btn-sm flex-fill" title="Editar Usuario" onclick="abrirModalEditar(<?= $u['id'] ?>)">
                                <i class="bi bi-pencil-square me-1"></i>Editar
                            </button>
                            <button class="btn btn-outline-warning btn-sm flex-fill text-dark" title="Cambiar Contraseña" onclick="abrirModalPassword(<?= $u['id'] ?>, '<?= htmlspecialchars($u['usuario']) ?>')">
                                <i class="bi bi-key me-1"></i>Clave
                            </button>
                            <?php if ($u['id'] != $currentUserId): ?>
                                <button class="btn <?= $u['estado'] === 'activo' ? 'btn-outline-danger' : 'btn-outline-success' ?> btn-sm px-2" 
                                        title="<?= $u['estado'] === 'activo' ? 'Desactivar cuenta' : 'Activar cuenta' ?>" 
                                        onclick="toggleEstadoUsuario(<?= $u['id'] ?>, '<?= htmlspecialchars($u['usuario']) ?>', '<?= $u['estado'] ?>')">
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
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: var(--spa-primary); color: #fff;">
                <h5 class="modal-title fw-bold" id="modalUsuarioTitulo" style="font-family: var(--font-serif);">
                    <i class="bi bi-person-fill-gear me-2 text-warning"></i> Nuevo Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formUsuario" onsubmit="guardarUsuario(event)">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" id="usuario_id" name="id" value="">

                <div class="modal-body p-4">
                    <div id="modalAlertError" class="alert alert-danger py-2 small d-none"></div>

                    <!-- Usuario -->
                    <div class="mb-3">
                        <label for="inputUsuario" class="form-label small fw-semibold text-dark">Nombre de Usuario *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="inputUsuario" name="usuario" placeholder="ej. mdelgado" required minlength="3" maxlength="50" autocomplete="off">
                        </div>
                        <small class="text-muted" style="font-size: 0.74rem;">Se utiliza para acceder al sistema.</small>
                    </div>

                    <!-- Empleado Vinculado -->
                    <div class="mb-3">
                        <label for="selectEmpleado" class="form-label small fw-semibold text-dark">Vincular a Empleado (Opcional)</label>
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
                        <label for="selectRol" class="form-label small fw-semibold text-dark">Rol en el Spa *</label>
                        <select class="form-select" id="selectRol" name="id_rol" required>
                            <option value="">-- Seleccionar Rol --</option>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nombre']) ?> - <?= htmlspecialchars($r['descripcion']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Contraseña (Solo en creación) -->
                    <div class="mb-3" id="campoPasswordWrapper">
                        <label for="inputPassword" class="form-label small fw-semibold text-dark">Contraseña Inicial *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="inputPassword" name="password" placeholder="Mínimo 6 caracteres" minlength="6" autocomplete="new-password">
                        </div>
                        <small class="text-muted" style="font-size: 0.74rem;">Se encriptará con BCRYPT antes de persistirse.</small>
                    </div>

                    <!-- Estado -->
                    <div class="mb-3">
                        <label for="selectEstado" class="form-label small fw-semibold text-dark">Estado de la Cuenta</label>
                        <select class="form-select" id="selectEstado" name="estado">
                            <option value="activo">Activo (Habilitado para ingresar)</option>
                            <option value="inactivo">Inactivo (Bloqueado)</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-spa-primary px-4" id="btnGuardarUsuario">
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
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: var(--spa-primary); color: #fff;">
                <h6 class="modal-title fw-bold" id="modalPasswordTitulo" style="font-family: var(--font-serif);">
                    <i class="bi bi-key-fill text-warning me-1"></i> Cambiar Contraseña
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPassword" onsubmit="guardarNuevaPassword(event)">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" id="pass_usuario_id" name="id" value="">

                <div class="modal-body p-3">
                    <p class="small text-muted mb-2">
                        Actualizar credencial de acceso para: <strong id="passUsuarioNombre" class="text-dark"></strong>
                    </p>
                    <div id="modalPassAlertError" class="alert alert-danger py-1 small d-none"></div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nueva Contraseña</label>
                        <input type="password" class="form-control form-control-sm" id="inputNuevaPass" name="password" placeholder="Mínimo 6 caracteres" minlength="6" required autocomplete="new-password">
                    </div>
                </div>

                <div class="modal-footer bg-light p-2">
                    <button type="button" class="btn btn-xs btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-xs btn-spa-primary" id="btnGuardarPass">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.badge-rol {
    display: inline-block;
    padding: 0.3rem 0.65rem;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 50rem;
}
.badge-admin {
    background: rgba(197, 160, 89, 0.2);
    color: #8b6b25;
    border: 1px solid rgba(197, 160, 89, 0.35);
}
.badge-recepcion {
    background: #eef6f3;
    color: #2c594c;
    border: 1px solid #c2ded4;
}
.badge-terapeuta {
    background: #eef2ff;
    color: #4338ca;
    border: 1px solid #c7d2fe;
}
.badge-default {
    background: #f3f4f6;
    color: #4b5563;
}
</style>

<script>
const CSRF_TOKEN = '<?= htmlspecialchars($csrfToken) ?>';

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
    document.querySelectorAll('.btn-group button').forEach(b => b.classList.remove('active'));
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
        document.getElementById('selectEmpleado').value = u.id_empleado || '';
        document.getElementById('selectRol').value = u.id_rol;
        document.getElementById('selectEstado').value = u.estado;

        // Ocultar campo password al editar (se cambia por modal dedicado)
        document.getElementById('campoPasswordWrapper').style.display = 'none';
        document.getElementById('inputPassword').removeAttribute('required');

        document.getElementById('modalUsuarioTitulo').innerHTML = `<i class="bi bi-pencil-square me-2 text-warning"></i> Editar Usuario: ${u.usuario}`;
        document.getElementById('modalAlertError').classList.add('d-none');

        const modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
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

    const modal = new bootstrap.Modal(document.getElementById('modalPassword'));
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
