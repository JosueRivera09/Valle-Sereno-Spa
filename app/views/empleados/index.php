<?php
$dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];

// Contadores de Resumen
$totalActivos = count(array_filter($empleados, fn($e) => $e['estado'] === 'activo'));
$totalInactivos = count($empleados) - $totalActivos;
?>

<!-- Encabezado Editorial -->
<div class="page-head">
    <div>
        <span class="kicker">Equipo</span>
        <h1 class="display-title">Personal Terapéutico <em>& Especialistas</em></h1>
    </div>
    <div class="page-head-actions">
        <button class="btn btn-spa-primary btn-sm px-3 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalEmpleado" onclick="limpiarFormEmpleado()">
            <i class="bi bi-person-plus-fill fs-6"></i>
            <span>Nuevo Colaborador</span>
        </button>
    </div>
</div>

<!-- Métricas Libro Mayor -->
<div class="metrics">
    <div class="metric">
        <span class="metric-label">Equipo de Trabajo</span>
        <span class="metric-value"><?= count($empleados) ?></span>
        <span class="metric-note">Especialistas registrados</span>
    </div>
    <div class="metric">
        <span class="metric-label">Activos en Cabina</span>
        <span class="metric-value"><?= $totalActivos ?></span>
        <span class="metric-note"><strong>Disponibles para agenda</strong></span>
    </div>
    <div class="metric">
        <span class="metric-label">Inactivos</span>
        <span class="metric-value"><?= $totalInactivos ?></span>
    </div>
</div>

<!-- Buscador & Filtro Rápido -->
<div class="spa-card p-3 mb-4">
    <div class="row g-3 align-items-center">
        <div class="col-md-8">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="busquedaTerapeuta" class="form-control border-start-0" placeholder="Buscar especialista por nombre, cargo o especialidad..." onkeyup="filtrarTerapeutas()">
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Mostrando <?= count($empleados) ?> colaboradores registrados</small>
        </div>
    </div>
</div>

<!-- Galería Grid de Tarjetas de Terapeutas -->
<div class="row g-3 mb-4" id="contenedorTerapeutas">
    <?php if (empty($empleados)): ?>
        <div class="col-12 text-center py-5 text-muted">
            <i class="bi bi-person-x fs-1 d-block mb-2 text-muted"></i>
            No hay personal terapéutico registrado en la base de datos.<br>
            <small>Haz clic en "Nuevo Colaborador" para agregar uno.</small>
        </div>
    <?php else: ?>
        <?php foreach ($empleados as $emp): 
            $initials = strtoupper(substr($emp['nombre_completo'], 0, 1) . (strpos($emp['nombre_completo'], ' ') !== false ? substr($emp['nombre_completo'], strpos($emp['nombre_completo'], ' ') + 1, 1) : ''));
            $tagsEspecialidades = array_filter(array_map('trim', explode(',', $emp['especialidades'] ?? '')));
        ?>
            <div class="col-sm-6 col-lg-4 col-xl-3 tarjeta-terapeuta-item">
                <div class="spa-card p-4 h-100 position-relative d-flex flex-column justify-content-between border-top border-4 border-success shadow-sm hover-shadow transition-all">
                    
                    <div>
                        <!-- Top Avatar & Estado -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                                     style="width: 52px; height: 52px; font-size: 1.1rem; background: linear-gradient(135deg, var(--spa-primary), #2c5341);">
                                    <?= htmlspecialchars($initials) ?>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0 fs-6"><?= htmlspecialchars($emp['nombre_completo']) ?></h6>
                                    <small class="badge bg-light text-primary border" style="font-size: 0.72rem;"><?= htmlspecialchars($emp['cargo']) ?></small>
                                </div>
                            </div>
                            <span class="badge rounded-pill <?= $emp['estado'] === 'activo' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary' ?> px-2 py-1" style="font-size: 0.7rem;">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i><?= htmlspecialchars(ucfirst($emp['estado'])) ?>
                            </span>
                        </div>

                        <!-- Información de Contacto -->
                        <div class="p-2 px-3 rounded-3 mb-3" style="background: #f8faf9; font-size: 0.8rem;">
                            <div class="text-dark mb-1">
                                <i class="bi bi-telephone text-success me-2"></i><?= htmlspecialchars($emp['telefono']) ?>
                            </div>
                            <div class="text-muted text-truncate" title="<?= htmlspecialchars($emp['correo']) ?>">
                                <i class="bi bi-envelope text-primary me-2"></i><?= htmlspecialchars($emp['correo']) ?>
                            </div>
                        </div>

                        <!-- Tags de Especialidades -->
                        <div class="mb-3">
                            <small class="text-muted fw-semibold d-block mb-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Especialidades Ofertadas:</small>
                            <?php if (empty($tagsEspecialidades)): ?>
                                <small class="text-muted italic">Sin especialidades especificadas</small>
                            <?php else: ?>
                                <div class="d-flex flex-wrap gap-1">
                                    <?php foreach ($tagsEspecialidades as $tag): ?>
                                        <span class="badge bg-white text-dark border px-2 py-1 font-normal" style="font-size: 0.75rem; font-weight: 500;">
                                            <i class="bi bi-sparkles text-warning me-1"></i><?= htmlspecialchars($tag) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Footer Acciones -->
                    <div class="pt-3 border-top d-flex gap-2 justify-content-between align-items-center">
                        <button class="btn btn-outline-success btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1" onclick="abrirHorarios(<?= $emp['id'] ?>, '<?= htmlspecialchars($emp['nombre_completo'], ENT_QUOTES) ?>')">
                            <i class="bi bi-calendar-week"></i>
                            <span>Turnos & Horarios</span>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm px-2" title="Editar Perfil" onclick='editarEmpleado(<?= json_encode($emp, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) ?>)'>
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
    border: 1px solid #d4ded9;
    background: #ffffff;
    font-size: 0.76rem;
    font-weight: 500;
    color: #2c4238;
    cursor: pointer;
    user-select: none;
    transition: all 0.15s ease-in-out;
}
.badge-choice:hover {
    border-color: #9cb5a8;
    background: #f4f7f5;
}
.badge-choice:has(input:checked) {
    background: #233d34;
    color: #ffffff;
    border-color: #233d34;
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
                <button type="submit" class="btn btn-spa-primary btn-sm px-4">Guardar</button>
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
                            <button type="submit" class="btn btn-spa-primary btn-sm w-100">
                                <i class="bi bi-plus-lg me-1"></i> Agregar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabla de horarios -->
                <div class="form-section">
                    <div class="form-section-head">
                        <span class="form-section-num">02</span>
                        <h6 class="form-section-title">Turnos registrados</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Día</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                    <th>Estado</th>
                                    <th></th>
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
            <td>${esc(diasNombres[h.dia_semana] || h.dia_semana)}</td>
            <td>${esc(h.hora_inicio.substring(0,5))}</td>
            <td>${esc(h.hora_fin.substring(0,5))}</td>
            <td><span class="badge ${h.estado === 'activo' ? 'bg-success' : 'bg-secondary'}">${esc(h.estado)}</span></td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-danger" onclick="eliminarHorario(${parseInt(h.id, 10)})">
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

    cards.forEach(c => {
        const text = c.innerText.toLowerCase();
        c.style.display = text.includes(input) ? '' : 'none';
    });
}
</script>