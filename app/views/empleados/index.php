<?php
$dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--spa-primary); font-family: var(--font-serif);">
            <i class="bi bi-person-badge text-warning me-2"></i> Personal Terapéutico & Colaboradores
        </h4>
        <p class="text-muted small mb-0">Especialidades, turnos de trabajo y disponibilidad semanal.</p>
    </div>
    <button class="btn btn-spa-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalEmpleado" onclick="limpiarFormEmpleado()">
        <i class="bi bi-person-plus"></i> Registrar Empleado
    </button>
</div>

<!-- Lista de empleados -->
<div class="spa-card mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Cargo</th>
                    <th>Especialidades</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($empleados)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No hay empleados registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($empleados as $emp): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($emp['nombre_completo']) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($emp['correo']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($emp['cargo']) ?></td>
                            <td><small><?= htmlspecialchars($emp['especialidades'] ?? '—') ?></small></td>
                            <td><?= htmlspecialchars($emp['telefono']) ?></td>
                            <td>
                                <span class="badge <?= $emp['estado'] === 'activo' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= ucfirst($emp['estado']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" onclick='editarEmpleado(<?= json_encode($emp) ?>)'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" onclick="abrirHorarios(<?= $emp['id'] ?>, '<?= htmlspecialchars($emp['nombre_completo'], ENT_QUOTES) ?>')">
                                    <i class="bi bi-calendar-week"></i> Horarios
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Empleado -->
<div class="modal fade" id="modalEmpleado" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEmpleado">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModalEmpleado">Registrar Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="emp_id">
                    <div class="mb-3">
                        <label class="form-label">Nombre completo *</label>
                        <input type="text" class="form-control" name="nombre_completo" id="emp_nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo *</label>
                        <input type="email" class="form-control" name="correo" id="emp_correo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono *</label>
                        <input type="text" class="form-control" name="telefono" id="emp_telefono" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cargo *</label>
                        <input type="text" class="form-control" name="cargo" id="emp_cargo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Especialidades</label>
                        <textarea class="form-control" name="especialidades" id="emp_especialidades" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado" id="emp_estado">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spa-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Horarios -->
<div class="modal fade" id="modalHorarios" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Horarios de <span id="nombreEmpleadoHorario"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="horario_id_empleado">

                <!-- Formulario para agregar horario -->
                <form id="formHorario" class="row g-2 mb-4 align-items-end">
                    <input type="hidden" name="id" id="horario_id">
                    <div class="col-md-3">
                        <label class="form-label small">Día</label>
                        <select class="form-select form-select-sm" name="dia_semana" id="horario_dia" required>
                            <?php foreach ($dias as $num => $nombre): ?>
                                <option value="<?= $num ?>"><?= $nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Hora inicio</label>
                        <input type="time" class="form-control form-control-sm" name="hora_inicio" id="horario_inicio" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Hora fin</label>
                        <input type="time" class="form-control form-control-sm" name="hora_fin" id="horario_fin" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-spa-primary btn-sm w-100">
                            <i class="bi bi-plus-lg"></i> Agregar
                        </button>
                    </div>
                </form>

                <!-- Tabla de horarios -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
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

<script>
const diasNombres = <?= json_encode($dias) ?>;

function limpiarFormEmpleado() {
    document.getElementById('formEmpleado').reset();
    document.getElementById('emp_id').value = '';
    document.getElementById('tituloModalEmpleado').textContent = 'Registrar Empleado';
}

function editarEmpleado(emp) {
    document.getElementById('emp_id').value = emp.id;
    document.getElementById('emp_nombre').value = emp.nombre_completo;
    document.getElementById('emp_correo').value = emp.correo;
    document.getElementById('emp_telefono').value = emp.telefono;
    document.getElementById('emp_cargo').value = emp.cargo;
    document.getElementById('emp_especialidades').value = emp.especialidades || '';
    document.getElementById('emp_estado').value = emp.estado;
    document.getElementById('tituloModalEmpleado').textContent = 'Editar Empleado';
    new bootstrap.Modal(document.getElementById('modalEmpleado')).show();
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
    new bootstrap.Modal(document.getElementById('modalHorarios')).show();
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
            <td>${diasNombres[h.dia_semana] || h.dia_semana}</td>
            <td>${h.hora_inicio.substring(0,5)}</td>
            <td>${h.hora_fin.substring(0,5)}</td>
            <td><span class="badge ${h.estado === 'activo' ? 'bg-success' : 'bg-secondary'}">${h.estado}</span></td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-danger" onclick="eliminarHorario(${h.id})">
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
</script>