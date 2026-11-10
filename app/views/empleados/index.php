<?php
$dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];

// Contadores de Resumen
$totalEmpleados = count($empleados);
$totalActivos = count(array_filter($empleados, fn($e) => ($e['estado'] ?? '') === 'activo'));
$totalInactivos = $totalEmpleados - $totalActivos;
$totalConEspecialidad = count(array_filter($empleados, fn($e) => !empty(trim($e['especialidades'] ?? ''))));
?>

<!-- SaaS Page Header -->
<div class="saas-page-header">
    <div>
        <div class="saas-breadcrumb">
            <i class="bi bi-people-fill"></i>
            <span>Equipo &amp; Especialistas</span>
            <span class="sep">/</span>
            <span>Directorio de Colaboradores</span>
        </div>
        <h1 class="saas-title">Personal Terapéutico</h1>
        <p class="saas-subtitle">Gestión de especialistas, credenciales de rol, catálogo de terapias asignadas y turnos de atención.</p>
    </div>
    <div class="saas-header-actions">
        <button class="saas-btn saas-btn-primary" data-bs-toggle="modal" data-bs-target="#modalEmpleado" onclick="limpiarFormEmpleado()">
            <i class="bi bi-person-plus-fill"></i>
            <span>Nuevo Colaborador</span>
        </button>
    </div>
</div>

<!-- SaaS KPI Stats -->
<div class="saas-stats-grid mb-4">
    <div class="saas-stat-card">
        <div class="saas-stat-header">
            <span class="saas-stat-label">Total Especialistas</span>
            <div class="saas-stat-icon icon-teal">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= $totalEmpleados ?></div>
        <div class="saas-stat-change">
            <span class="badge-neutral"><i class="bi bi-person-badge"></i> Plantilla</span>
            Registrados en nómina
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-header">
            <span class="saas-stat-label">Activos en Cabina</span>
            <div class="saas-stat-icon icon-emerald">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= $totalActivos ?></div>
        <div class="saas-stat-change">
            <span class="badge-positive"><i class="bi bi-shield-check"></i> Habilitados</span>
            Disponibles para agenda
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-header">
            <span class="saas-stat-label">Con Especialidades</span>
            <div class="saas-stat-icon icon-gold">
                <i class="bi bi-stars"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= $totalConEspecialidad ?></div>
        <div class="saas-stat-change">
            <span class="badge-neutral"><i class="bi bi-tag-fill"></i> Terapias</span>
            Servicios asignados
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-header">
            <span class="saas-stat-label">Inactivos / Pausa</span>
            <div class="saas-stat-icon icon-indigo">
                <i class="bi bi-pause-circle-fill"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= $totalInactivos ?></div>
        <div class="saas-stat-change">
            <span class="badge-neutral">Sin asignación</span>
            Bloqueados en agenda
        </div>
    </div>
</div>

<!-- Barra de Búsqueda y Filtros -->
<div class="saas-card mb-3">
    <div class="py-2 px-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-6 col-lg-7">
                <div class="saas-search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="busquedaTerapeuta" placeholder="Buscar especialista por nombre, cargo, teléfono o especialidad..." onkeyup="filtrarTerapeutas()">
                </div>
            </div>
            <div class="col-md-6 col-lg-5 text-md-end d-flex align-items-center justify-content-md-end gap-2">
                <span class="text-muted small">
                    <i class="bi bi-person-check me-1 text-primary"></i>
                    <strong id="contadorTerapeutasVisible"><?= $totalEmpleados ?></strong> colaboradores listados
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Galería Grid de Colaboradores -->
<div class="row g-3 mb-3" id="contenedorTerapeutas">
    <?php if (empty($empleados)): ?>
        <div class="col-12">
            <div class="saas-card text-center py-5">
                <div class="saas-stat-icon icon-teal mx-auto mb-3" style="width: 56px; height: 56px; font-size: 1.5rem;">
                    <i class="bi bi-person-x"></i>
                </div>
                <h5 class="fw-bold mb-1">No hay colaboradores registrados</h5>
                <p class="text-muted small mb-3">Registra a tu primer especialista o terapeuta para asignarle tratamientos y turnos de cabina.</p>
                <button class="saas-btn saas-btn-primary mx-auto" data-bs-toggle="modal" data-bs-target="#modalEmpleado" onclick="limpiarFormEmpleado()">
                    <i class="bi bi-person-plus-fill"></i>
                    <span>Nuevo Colaborador</span>
                </button>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($empleados as $emp): 
            $initials = strtoupper(substr($emp['nombre_completo'], 0, 1) . (strpos($emp['nombre_completo'], ' ') !== false ? substr($emp['nombre_completo'], strpos($emp['nombre_completo'], ' ') + 1, 1) : ''));
            $tagsEspecialidades = array_filter(array_map('trim', explode(',', $emp['especialidades'] ?? '')));
            $isActivo = ($emp['estado'] ?? '') === 'activo';
        ?>
            <div class="col-sm-6 col-lg-4 col-xl-3 tarjeta-terapeuta-item">
                <div class="saas-card h-100 p-3 d-flex flex-column justify-content-between position-relative" style="transition: all .2s ease;">
                    
                    <div>
                        <!-- Header Colaborador con Avatar e Iniciales -->
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                                     style="width: 48px; height: 48px; font-size: 1rem; background: linear-gradient(135deg, #1e3d34, #2c5341); flex-shrink: 0;">
                                    <?= htmlspecialchars($initials) ?>
                                </div>
                                <div style="min-width: 0;">
                                    <h6 class="fw-bold text-dark mb-0 text-truncate" title="<?= htmlspecialchars($emp['nombre_completo']) ?>">
                                        <?= htmlspecialchars($emp['nombre_completo']) ?>
                                    </h6>
                                    <span class="saas-badge bg-light text-primary border mt-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-shield-check me-1"></i><?= htmlspecialchars($emp['cargo']) ?>
                                    </span>
                                </div>
                            </div>
                            <span class="saas-badge <?= $isActivo ? 'saas-badge-success' : 'saas-badge-neutral' ?>" style="font-size: 0.68rem;">
                                <?= $isActivo ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </div>

                        <!-- Info de Contacto -->
                        <div class="p-2 px-3 rounded-2 mb-3" style="background: #f8fafc; border: 1px solid #f1f5f9; font-size: 0.8rem;">
                            <div class="text-dark mb-1 d-flex align-items-center gap-2">
                                <i class="bi bi-telephone text-success"></i>
                                <span class="fw-semibold"><?= htmlspecialchars($emp['telefono']) ?></span>
                            </div>
                            <div class="text-muted text-truncate d-flex align-items-center gap-2" title="<?= htmlspecialchars($emp['correo']) ?>">
                                <i class="bi bi-envelope text-primary"></i>
                                <span><?= htmlspecialchars($emp['correo']) ?></span>
                            </div>
                        </div>

                        <!-- Especialidades Asignadas -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted fw-bold" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px;">Especialidades</span>
                                <span class="badge bg-light text-muted border" style="font-size: 0.65rem;"><?= count($tagsEspecialidades) ?></span>
                            </div>
                            <?php if (empty($tagsEspecialidades)): ?>
                                <small class="text-muted fst-italic" style="font-size: 0.75rem;">Sin especialidades registradas</small>
                            <?php else: ?>
                                <div class="d-flex flex-wrap gap-1" style="max-height: 80px; overflow-y: auto;">
                                    <?php foreach ($tagsEspecialidades as $tag): ?>
                                        <span class="saas-badge saas-badge-gold" style="font-size: 0.72rem; padding: 2px 7px;">
                                            <i class="bi bi-sparkles me-1"></i><?= htmlspecialchars($tag) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="pt-3 border-top d-flex gap-2 justify-content-between align-items-center">
                        <button class="saas-btn saas-btn-outline w-100 py-1" style="font-size: 0.78rem;" onclick="abrirHorarios(<?= (int)$emp['id'] ?>, '<?= htmlspecialchars($emp['nombre_completo'], ENT_QUOTES) ?>')">
                            <i class="bi bi-calendar-week text-primary"></i>
                            <span>Turnos &amp; Horarios</span>
                        </button>
                        <button class="saas-btn saas-btn-outline px-2 py-1" title="Editar Perfil" onclick='editarEmpleado(<?= json_encode($emp, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) ?>)'>
                            <i class="bi bi-pencil-square"></i>
                        </button>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Estilos para selector de especialidades -->
<style>
.badge-choice {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 10px;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    font-size: 0.76rem;
    font-weight: 500;
    color: #1e293b;
    cursor: pointer;
    user-select: none;
    transition: all 0.15s ease-in-out;
}
.badge-choice:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}
.badge-choice:has(input:checked) {
    background: #1e3d34;
    color: #ffffff;
    border-color: #1e3d34;
}
.badge-choice input {
    margin: 0;
    accent-color: #c5a059;
}
</style>

<!-- Modal Empleado -->
<div class="modal fade" id="modalEmpleado" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form id="formEmpleado" class="modal-content modal-editorial">
            <div class="modal-header">
                <div>
                    <span class="modal-kicker">Equipo</span>
                    <h5 class="modal-title" id="tituloModalEmpleado">Registrar Empleado</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: calc(85vh - 130px); overflow-y: auto;">
                <input type="hidden" name="id" id="emp_id">
                <div class="mb-3">
                    <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nombre_completo" id="emp_nombre" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Correo <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="correo" id="emp_correo" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="telefono" id="emp_telefono" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cargo / Rol en el Sistema <span class="text-danger">*</span></label>
                    <select class="form-select" name="cargo" id="emp_cargo" required>
                        <option value="">-- Seleccionar Rol / Cargo --</option>
                        <?php if (!empty($roles)): ?>
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?= htmlspecialchars($rol['nombre']) ?>">
                                    <?= htmlspecialchars($rol['nombre']) ?><?php if (!empty($rol['descripcion'])): ?> — <?= htmlspecialchars($rol['descripcion']) ?><?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                        <i class="bi bi-shield-check me-1"></i>Roles leídos de la base de datos (determina funciones y acceso en el sistema).
                    </small>
                </div>
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label class="form-label mb-0">Especialidades &amp; Terapias</label>
                        <span class="badge bg-light text-muted border" style="font-size: 0.7rem;">Desde Catálogo BD</span>
                    </div>

                    <!-- Selector visual de Servicios / Especialidades desde la base de datos -->
                    <div class="border rounded p-2 bg-light mb-2" style="max-height: 180px; overflow-y: auto;">
                        <?php if (empty($servicios)): ?>
                            <small class="text-muted d-block text-center py-2">No hay servicios registrados en la base de datos.</small>
                        <?php else: ?>
                            <?php
                            $serviciosPorCat = [];
                            foreach ($servicios as $s) {
                                $cat = $s['categoria_nombre'] ?? 'General';
                                $serviciosPorCat[$cat][] = $s;
                            }
                            ?>
                            <?php foreach ($serviciosPorCat as $catNombre => $items): ?>
                                <div class="mb-2">
                                    <div class="fw-bold text-muted text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                        <i class="bi bi-tag-fill me-1" style="color: #c5a059;"></i><?= htmlspecialchars($catNombre) ?>
                                    </div>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach ($items as $srv): ?>
                                            <label class="badge-choice">
                                                <input type="checkbox" 
                                                       class="chk-especialidad" 
                                                       name="servicios_ids[]"
                                                       value="<?= $srv['id'] ?>" 
                                                       data-nombre="<?= htmlspecialchars($srv['nombre']) ?>"
                                                       onchange="actualizarEspecialidadesTexto()">
                                                <span><?= htmlspecialchars($srv['nombre']) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Campo de texto sincronizado -->
                    <textarea class="form-control form-control-sm" name="especialidades" id="emp_especialidades" rows="2" placeholder="Las especialidades seleccionadas se mostrarán aquí..." readonly></textarea>
                    <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                        <i class="bi bi-info-circle me-1"></i>Marca los tratamientos de la base de datos que este colaborador está capacitado para brindar.
                    </small>
                </div>
                <div class="mb-0">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado" id="emp_estado">
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-quiet btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="saas-btn saas-btn-primary px-4">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Horarios -->
<div class="modal fade" id="modalHorarios" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-editorial">
            <div class="modal-header">
                <div>
                    <span class="modal-kicker">Disponibilidad</span>
                    <h5 class="modal-title">Horarios de <span id="nombreEmpleadoHorario"></span></h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="horario_id_empleado">

                <!-- Formulario para agregar horario -->
                <div class="form-section">
                    <div class="form-section-head">
                        <span class="form-section-num">01</span>
                        <h6 class="form-section-title">Agregar turno</h6>
                    </div>
                    <form id="formHorario" class="row g-2 align-items-end">
                        <input type="hidden" name="id" id="horario_id">
                        <div class="col-md-3">
                            <label class="form-label">Día</label>
                            <select class="form-select form-select-sm" name="dia_semana" id="horario_dia" required>
                                <?php foreach ($dias as $num => $nombre): ?>
                                    <option value="<?= $num ?>"><?= $nombre ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Hora inicio</label>
                            <input type="time" class="form-control form-control-sm" name="hora_inicio" id="horario_inicio" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Hora fin</label>
                            <input type="time" class="form-control form-control-sm" name="hora_fin" id="horario_fin" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="saas-btn saas-btn-primary w-100 py-1" style="font-size: 0.82rem;">
                                <i class="bi bi-plus-lg me-1"></i> Agregar Turno
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabla de horarios -->
                <div class="form-section mt-4">
                    <div class="form-section-head">
                        <span class="form-section-num">02</span>
                        <h6 class="form-section-title">Turnos registrados</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="saas-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Día</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                    <th>Estado</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaHorarios">
                                <tr><td colspan="5" class="text-center text-muted">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const diasNombres = <?= json_encode($dias) ?>;

function actualizarEspecialidadesTexto() {
    const checked = Array.from(document.querySelectorAll('.chk-especialidad:checked'));
    const nombres = checked.map(c => c.getAttribute('data-nombre') || c.value);
    document.getElementById('emp_especialidades').value = nombres.join(', ');
}

function limpiarFormEmpleado() {
    document.getElementById('formEmpleado').reset();
    document.getElementById('emp_id').value = '';
    document.getElementById('emp_cargo').value = '';
    document.getElementById('emp_especialidades').value = '';
    document.querySelectorAll('.chk-especialidad').forEach(c => c.checked = false);
    document.getElementById('tituloModalEmpleado').textContent = 'Registrar Empleado';
}

function editarEmpleado(emp) {
    document.getElementById('emp_id').value = emp.id;
    document.getElementById('emp_nombre').value = emp.nombre_completo;
    document.getElementById('emp_correo').value = emp.correo;
    document.getElementById('emp_telefono').value = emp.telefono;

    // Seleccionar Cargo / Rol
    const selectCargo = document.getElementById('emp_cargo');
    let foundCargo = false;
    for (let opt of selectCargo.options) {
        if (opt.value.trim().toLowerCase() === (emp.cargo || '').trim().toLowerCase()) {
            opt.selected = true;
            foundCargo = true;
            break;
        }
    }
    if (!foundCargo && emp.cargo) {
        const customOpt = new Option(emp.cargo, emp.cargo, true, true);
        selectCargo.add(customOpt);
    }

    // Especialidades: sincronizar checkboxes con emp.especialidades
    const espTexto = emp.especialidades || '';
    document.getElementById('emp_especialidades').value = espTexto;
    const tags = espTexto.split(',').map(s => s.trim().toLowerCase()).filter(s => s.length > 0);

    document.querySelectorAll('.chk-especialidad').forEach(chk => {
        const srvNombre = (chk.getAttribute('data-nombre') || '').trim().toLowerCase();
        chk.checked = tags.includes(srvNombre);
    });

    document.getElementById('emp_estado').value = emp.estado;
    document.getElementById('tituloModalEmpleado').textContent = 'Editar Empleado';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEmpleado')).show();
}

document.getElementById('formEmpleado').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const res = await fetch('index.php?c=empleados&a=guardar', { method: 'POST', body: formData });
    const data = await res.json();
    alert(data.message);
    if (data.success) location.reload();
});

function abrirHorarios(idEmpleado, nombre) {
    document.getElementById('horario_id_empleado').value = idEmpleado;
    document.getElementById('nombreEmpleadoHorario').textContent = nombre;
    document.getElementById('formHorario').reset();
    document.getElementById('horario_id').value = '';
    cargarHorarios(idEmpleado);
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalHorarios')).show();
}

async function cargarHorarios(idEmpleado) {
    const res = await fetch(`index.php?c=empleados&a=disponibilidad&id_empleado=${idEmpleado}`);
    const data = await res.json();
    const tbody = document.getElementById('tablaHorarios');
    if (!data.success || data.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Sin horarios registrados.</td></tr>';
        return;
    }
    tbody.innerHTML = data.data.map(h => `
        <tr>
            <td class="fw-semibold">${esc(diasNombres[h.dia_semana] || h.dia_semana)}</td>
            <td>${esc(h.hora_inicio.substring(0,5))}</td>
            <td>${esc(h.hora_fin.substring(0,5))}</td>
            <td><span class="saas-badge ${h.estado === 'activo' ? 'saas-badge-success' : 'saas-badge-neutral'}">${esc(h.estado)}</span></td>
            <td class="text-end">
                <button class="saas-btn saas-btn-outline px-2 py-1 text-danger" title="Eliminar Turno" onclick="eliminarHorario(${parseInt(h.id, 10)})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

document.getElementById('formHorario').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('id_empleado', document.getElementById('horario_id_empleado').value);
    const res = await fetch('index.php?c=empleados&a=guardarHorario', { method: 'POST', body: formData });
    const data = await res.json();
    alert(data.message);
    if (data.success) {
        this.reset();
        cargarHorarios(document.getElementById('horario_id_empleado').value);
    }
});

async function eliminarHorario(id) {
    if (!confirm('¿Eliminar este horario?')) return;
    const formData = new FormData();
    formData.append('id', id);
    const res = await fetch('index.php?c=empleados&a=eliminarHorario', { method: 'POST', body: formData });
    const data = await res.json();
    alert(data.message);
    if (data.success) cargarHorarios(document.getElementById('horario_id_empleado').value);
}

function filtrarTerapeutas() {
    const input = document.getElementById('busquedaTerapeuta').value.toLowerCase();
    const cards = document.querySelectorAll('#contenedorTerapeutas .tarjeta-terapeuta-item');
    let visibleCount = 0;

    cards.forEach(c => {
        const text = c.innerText.toLowerCase();
        const matches = text.includes(input);
        c.style.display = matches ? '' : 'none';
        if (matches) visibleCount++;
    });

    const counter = document.getElementById('contadorTerapeutasVisible');
    if (counter) counter.textContent = visibleCount;
}
</script>
