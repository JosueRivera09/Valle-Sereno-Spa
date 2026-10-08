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
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.8px;">Total Cuentas</small>
                <h3 class="fw-bold mb-0 text-dark" style="font-family: var(--font-serif);"><?= count($usuarios) ?></h3>
                <small class="text-muted" style="font-size: 0.75rem;">En el sistema</small>
            </div>
            <div class="stat-icon" style="background: rgba(30, 61, 52, 0.1); color: var(--spa-primary);">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.8px;">Activas</small>
                <h3 class="fw-bold mb-0 text-success" style="font-family: var(--font-serif);">
                    <?= count(array_filter($usuarios, fn($u) => $u['estado'] === 'activo')) ?>
                </h3>
                <small class="text-success" style="font-size: 0.75rem;"><i class="bi bi-check-circle"></i> Con acceso normal</small>
            </div>
            <div class="stat-icon" style="background: #eef6f3; color: #3b735c;">
                <i class="bi bi-shield-check"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.8px;">Bloqueadas / Inactivas</small>
                <?php 
                    $bloqueadas = count(array_filter($usuarios, fn($u) => $u['estado'] === 'bloqueado'));
                    $inactivas = count(array_filter($usuarios, fn($u) => $u['estado'] === 'inactivo'));
                ?>
                <h3 class="fw-bold mb-0 <?= $bloqueadas > 0 ? 'text-danger' : 'text-dark' ?>" style="font-family: var(--font-serif);">
                    <?= $bloqueadas + $inactivas ?>
                </h3>
                <small class="<?= $bloqueadas > 0 ? 'text-danger fw-semibold' : 'text-muted' ?>" style="font-size: 0.75rem;">
                    <?= $bloqueadas ?> por intentos &bull; <?= $inactivas ?> manual
                </small>
            </div>
            <div class="stat-icon" style="background: <?= $bloqueadas > 0 ? 'rgba(220, 53, 69, 0.12)' : 'rgba(197, 160, 89, 0.15)' ?>; color: <?= $bloqueadas > 0 ? '#dc3545' : '#997838' ?>;">
                <i class="bi <?= $bloqueadas > 0 ? 'bi-shield-x' : 'bi-lock-fill' ?>"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.8px;">Seguridad de Intentos</small>
                <h5 class="fw-bold mb-0 text-dark mt-1" style="font-family: var(--font-serif);">Máx. 3 Fallos</h5>
                <small class="text-primary" style="font-size: 0.75rem;"><i class="bi bi-shield-lock"></i> Auto-Bloqueo Activo</small>
            </div>
            <div class="stat-icon" style="background: #fdf5ea; color: #c5a059;">
                <i class="bi bi-key-fill"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabla y Controles -->
<div class="spa-card p-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
        <div class="input-group" style="max-width: 320px;">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="text" id="filtroUsuarios" class="form-control bg-light border-start-0 ps-0" placeholder="Buscar por usuario, nombre o cargo..." onkeyup="filtrarTabla()">
        </div>
        <div class="text-muted small">
            Mostrando <strong id="conteoVisible"><?= count($usuarios) ?></strong> usuario(s)
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tablaUsuarios">
            <thead class="table-light">
                <tr style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #5a6e65;">
                    <th>Usuario / Identificador</th>
                    <th>Personal Vinculado</th>
                    <th>Rol en Sistema</th>
                    <th>Intentos / Acceso</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-1"></i>
                            No hay cuentas de usuario registradas.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $u): ?>
                        <tr class="<?= $u['estado'] === 'bloqueado' ? 'table-danger-subtle' : '' ?>">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar-circle" style="width: 36px; height: 36px; font-size: 0.85rem; background: <?= $u['estado'] === 'bloqueado' ? '#dc3545' : 'var(--spa-primary)' ?>; color: #fff;">
                                        <?= strtoupper(substr($u['usuario'], 0, 2)) ?>
                                    </div>
                                    <div>
                                        <span class="fw-semibold text-dark d-block"><?= htmlspecialchars($u['usuario']) ?></span>
                                        <small class="text-muted" style="font-size: 0.75rem;">ID: #<?= $u['id'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium text-dark"><?= htmlspecialchars($u['nombre_completo']) ?></div>
                                <small class="text-muted" style="font-size: 0.76rem;"><?= htmlspecialchars($u['cargo']) ?> &bull; <?= htmlspecialchars($u['email']) ?></small>
                            </td>
                            <td>
                                <?php
                                    $badgeClass = match($u['rol_nombre']) {
                                        'Administrador' => 'badge-admin',
                                        'Recepcionista' => 'badge-recepcion',
                                        'Terapeuta'     => 'badge-terapeuta',
                                        default         => 'badge-default'
                                    };
                                ?>
                                <span class="badge-rol <?= $badgeClass ?>">
                                    <?= htmlspecialchars($u['rol_nombre']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="small">
                                    <?php if ((int)($u['intentos_fallidos'] ?? 0) > 0): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
                                            <i class="bi bi-exclamation-circle-fill"></i> <?= (int)$u['intentos_fallidos'] ?> fallo(s)
                                        </span>
                                    <?php else: ?>
                                        <span class="text-success small"><i class="bi bi-check2"></i> 0 fallos</span>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted" style="font-size: 0.73rem;">
                                    Último: <?= $u['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($u['ultimo_acceso'])) : '<span class="text-black-50">Nunca</span>' ?>
                                </small>
                            </td>
                            <td>
                                <?php if ($u['estado'] === 'activo'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                        <i class="bi bi-dot"></i> Activo
                                    </span>
                                <?php elseif ($u['estado'] === 'bloqueado'): ?>
                                    <span class="badge bg-danger text-white rounded-pill px-2 py-1 shadow-sm" title="Bloqueado por superar intentos de ingreso">
                                        <i class="bi bi-shield-x"></i> Bloqueado
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1">
                                        <i class="bi bi-dot"></i> Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <?php if ($u['estado'] === 'bloqueado'): ?>
                                        <button class="btn btn-danger btn-sm px-2 text-white fw-semibold" 
                                                title="Desbloquear cuenta de usuario" 
                                                onclick="desbloquearUsuario(<?= $u['id'] ?>, '<?= htmlspecialchars($u['usuario']) ?>')">
                                            <i class="bi bi-unlock-fill me-1"></i> Desbloquear
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-outline-secondary" title="Editar Usuario" onclick="abrirModalEditar(<?= $u['id'] ?>)">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" title="Cambiar Contraseña" onclick="abrirModalPassword(<?= $u['id'] ?>, '<?= htmlspecialchars($u['usuario']) ?>')">
                                        <i class="bi bi-key"></i>
                                    </button>
                                    <?php if ($u['id'] != $currentUserId): ?>
                                        <button class="btn <?= $u['estado'] === 'activo' ? 'btn-outline-danger' : 'btn-outline-success' ?>" 
                                                title="<?= $u['estado'] === 'activo' ? 'Desactivar cuenta' : 'Activar cuenta' ?>" 
                                                onclick="toggleEstadoUsuario(<?= $u['id'] ?>, '<?= htmlspecialchars($u['usuario']) ?>', '<?= $u['estado'] ?>')">
                                            <i class="bi <?= $u['estado'] === 'activo' ? 'bi-person-slash' : 'bi-person-check' ?>"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
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

function filtrarTabla() {
    const q = document.getElementById('filtroUsuarios').value.toLowerCase();
    const rows = document.querySelectorAll('#tablaUsuarios tbody tr');
    let visible = 0;

    rows.forEach(r => {
        const text = r.innerText.toLowerCase();
        if (text.includes(q)) {
            r.style.display = '';
            visible++;
        } else {
            r.style.display = 'none';
        }
    });

    const conteoEl = document.getElementById('conteoVisible');
    if (conteoEl) conteoEl.innerText = visible;
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
