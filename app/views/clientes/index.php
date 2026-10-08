<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--spa-primary); font-family: var(--font-serif);">
            <i class="bi bi-people text-warning me-2"></i> Directorio de Clientes & Expedientes
        </h4>
        <p class="text-muted small mb-0">Gestión integral de la comunidad de clientes, datos de contacto e historial de atenciones.</p>
    </div>
    <button class="btn btn-spa-primary btn-sm px-3 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalCliente" onclick="prepararNuevoCliente()">
        <i class="bi bi-person-plus-fill"></i>
        <span>Nuevo Cliente</span>
    </button>
</div>

<!-- Tarjetas Resumen -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-4">
        <div class="spa-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">Total Registrados</small>
                <h3 class="fw-bold mb-0 text-dark" style="font-family: var(--font-serif);"><?= count($clientes ?? []) ?></h3>
            </div>
            <div class="stat-icon" style="background: rgba(30, 61, 52, 0.1); color: var(--spa-primary);">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="spa-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">Clientes Activos</small>
                <h3 class="fw-bold mb-0 text-success" style="font-family: var(--font-serif);">
                    <?= count(array_filter($clientes ?? [], fn($c) => $c['estado'] === 'activo')) ?>
                </h3>
            </div>
            <div class="stat-icon" style="background: #eef6f3; color: #3b735c;">
                <i class="bi bi-person-check-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="spa-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">Inactivos</small>
                <h3 class="fw-bold mb-0 text-secondary" style="font-family: var(--font-serif);">
                    <?= count(array_filter($clientes ?? [], fn($c) => $c['estado'] === 'inactivo')) ?>
                </h3>
            </div>
            <div class="stat-icon" style="background: #f4f4f4; color: #6c757d;">
                <i class="bi bi-person-dash"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Clientes -->
<div class="spa-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0" style="color: var(--spa-primary); font-family: var(--font-serif);">
            <i class="bi bi-journal-bookmark text-warning me-2"></i> Listado General
        </h5>
        <div class="input-group input-group-sm" style="max-width: 280px;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="busquedaCliente" class="form-control border-start-0" placeholder="Buscar cliente..." onkeyup="filtrarTablaClientes()">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tablaClientes">
            <thead class="table-light">
                <tr style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #556b61;">
                    <th>ID</th>
                    <th>NOMBRE COMPLETO</th>
                    <th>FECHA NAC.</th>
                    <th>TELÉFONO</th>
                    <th>CORREO</th>
                    <th>ESTADO</th>
                    <th class="text-end">ACCIONES</th>
                </tr>
            </thead>
            <tbody style="font-size: 0.88rem;">
                <?php if (empty($clientes)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-person-exclamation fs-3 d-block mb-1 text-muted"></i>
                            No hay clientes registrados en el sistema.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($clientes as $c): ?>
                        <tr>
                            <td class="fw-bold text-muted">#<?= $c['id'] ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($c['nombre_completo']) ?></div>
                                <small class="text-muted" style="font-size: 0.72rem;">Registrado: <?= date('d/m/Y', strtotime($c['creado_en'])) ?></small>
                            </td>
                            <td>
                                <span class="text-secondary">
                                    <i class="bi bi-cake2 me-1"></i><?= date('d/m/Y', strtotime($c['fecha_nacimiento'])) ?>
                                </span>
                            </td>
                            <td>
                                <a href="tel:<?= htmlspecialchars($c['telefono']) ?>" class="text-decoration-none text-dark fw-medium">
                                    <i class="bi bi-telephone text-success me-1"></i><?= htmlspecialchars($c['telefono']) ?>
                                </a>
                            </td>
                            <td>
                                <?php if (!empty($c['correo'])): ?>
                                    <a href="mailto:<?= htmlspecialchars($c['correo']) ?>" class="text-decoration-none text-muted">
                                        <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($c['correo']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted italic">Sin correo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($c['estado'] === 'activo'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-1">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Ver Historial" onclick="verHistorialCliente(<?= $c['id'] ?>, '<?= htmlspecialchars($c['nombre_completo'], ENT_QUOTES) ?>')">
                                        <i class="bi bi-clock-history"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Editar Expediente" 
                                            onclick='editarCliente(<?= json_encode($c) ?>)'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-<?= $c['estado'] === 'activo' ? 'warning' : 'success' ?>" 
                                            title="<?= $c['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?>" 
                                            onclick="cambiarEstadoCliente(<?= $c['id'] ?>, '<?= $c['estado'] === 'activo' ? 'inactivo' : 'activo' ?>')">
                                        <i class="bi bi-toggle-<?= $c['estado'] === 'activo' ? 'on' : 'off' ?>"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Registro / Edición de Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--spa-primary);">
                <h5 class="modal-title fw-bold" id="modalClienteLabel" style="font-family: var(--font-serif);">
                    <i class="bi bi-person-lines-fill text-warning me-2"></i> Expediente de Cliente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCliente" onsubmit="guardarCliente(event)">
                <div class="modal-body p-4">
                    <input type="hidden" id="cliente_id" name="id">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" id="nombre_completo" name="nombre_completo" class="form-control" placeholder="Ej. María Elena Torres" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-dark">Fecha Nacimiento <span class="text-danger">*</span></label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-dark">Teléfono <span class="text-danger">*</span></label>
                            <input type="tel" id="telefono" name="telefono" class="form-control" placeholder="Ej. 555-0192" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Correo Electrónico</label>
                        <input type="email" id="correo" name="correo" class="form-control" placeholder="cliente@ejemplo.com">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Estado del Cliente</label>
                        <select id="estado" name="estado" class="form-select">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spa-primary btn-sm px-4">
                        <i class="bi bi-save me-1"></i> Guardar Expediente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Historial de Citas -->
<div class="modal fade" id="modalHistorial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--spa-primary);">
                <h5 class="modal-title fw-bold" style="font-family: var(--font-serif);">
                    <i class="bi bi-clock-history text-warning me-2"></i> Historial del Cliente: <span id="historialNombreCliente"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr style="font-size: 0.78rem; text-transform: uppercase;">
                                <th>FECHA</th>
                                <th>HORARIO</th>
                                <th>TERAPEUTA</th>
                                <th>MONTO</th>
                                <th>ESTADO</th>
                            </tr>
                        </thead>
                        <tbody id="bodyHistorial" style="font-size: 0.88rem;">
                            <!-- Dinámico por JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let modalClienteBs, modalHistorialBs;

document.addEventListener('DOMContentLoaded', () => {
    modalClienteBs = new bootstrap.Modal(document.getElementById('modalCliente'));
    modalHistorialBs = new bootstrap.Modal(document.getElementById('modalHistorial'));
});

function prepararNuevoCliente() {
    document.getElementById('formCliente').reset();
    document.getElementById('cliente_id').value = '';
    document.getElementById('modalClienteLabel').innerHTML = '<i class="bi bi-person-plus-fill text-warning me-2"></i> Registrar Nuevo Cliente';
}

function editarCliente(c) {
    document.getElementById('cliente_id').value = c.id;
    document.getElementById('nombre_completo').value = c.nombre_completo;
    document.getElementById('fecha_nacimiento').value = c.fecha_nacimiento;
    document.getElementById('telefono').value = c.telefono;
    document.getElementById('correo').value = c.correo || '';
    document.getElementById('estado').value = c.estado;
    document.getElementById('modalClienteLabel').innerHTML = '<i class="bi bi-pencil-square text-warning me-2"></i> Editar Expediente de Cliente';
    modalClienteBs.show();
}

function guardarCliente(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('formCliente'));

    fetch('index.php?c=clientes&a=guardar', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            modalClienteBs.hide();
            location.reload();
        } else {
            alert('Error: ' + res.mensaje);
        }
    })
    .catch(() => alert('Error procesando la solicitud.'));
}

function cambiarEstadoCliente(id, nuevoEstado) {
    if (!confirm(`¿Deseas cambiar el estado a '${nuevoEstado}'?`)) return;

    const fd = new FormData();
    fd.append('id', id);
    fd.append('estado', nuevoEstado);

    fetch('index.php?c=clientes&a=cambiarEstado', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            location.reload();
        } else {
            alert('Error: ' + res.mensaje);
        }
    });
}

function verHistorialCliente(id, nombre) {
    document.getElementById('historialNombreCliente').innerText = nombre;
    const body = document.getElementById('bodyHistorial');
    body.innerHTML = '<tr><td colspan="5" class="text-center py-3"><div class="spinner-border text-success spinner-border-sm"></div> Cargando historial...</td></tr>';
    modalHistorialBs.show();

    fetch(`index.php?c=clientes&a=historial&id=${id}`)
    .then(r => r.json())
    .then(res => {
        if (res.success && res.data.length > 0) {
            body.innerHTML = res.data.map(item => `
                <tr>
                    <td><span class="fw-semibold">${item.fecha}</span></td>
                    <td>${item.hora_inicio} - ${item.hora_fin}</td>
                    <td>${item.terapeuta_nombre || 'N/A'}</td>
                    <td class="fw-bold">$${parseFloat(item.total).toFixed(2)}</td>
                    <td><span class="badge bg-secondary rounded-pill">${item.estado}</span></td>
                </tr>
            `).join('');
        } else {
            body.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-muted">El cliente no registra historial de citas previo.</td></tr>';
        }
    });
}

function filtrarTablaClientes() {
    const input = document.getElementById('busquedaCliente').value.toLowerCase();
    const rows = document.querySelectorAll('#tablaClientes tbody tr');
    rows.forEach(r => {
        const text = r.innerText.toLowerCase();
        r.style.display = text.includes(input) ? '' : 'none';
    });
}
</script>
