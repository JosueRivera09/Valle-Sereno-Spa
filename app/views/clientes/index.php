<?php
$totalClientes = count($clientes ?? []);
$clientesActivos = count(array_filter($clientes ?? [], fn($c) => $c['estado'] === 'activo'));
$clientesInactivos = count(array_filter($clientes ?? [], fn($c) => $c['estado'] === 'inactivo'));
?>

<!-- Encabezado de Página SaaS -->
<div class="saas-page-header">
    <div class="saas-header-info">
        <span class="saas-breadcrumb"><i class="bi bi-people me-1"></i>Comunidad & Pacientes</span>
        <h1 class="saas-title">Directorio de Clientes & Expedientes</h1>
        <p class="saas-subtitle">Gestión integral de expedientes clínicos, datos de contacto e historial de atenciones.</p>
    </div>
    <div class="saas-header-actions">
        <button class="btn-saas-primary" data-bs-toggle="modal" data-bs-target="#modalCliente" onclick="prepararNuevoCliente()">
            <i class="bi bi-person-plus-fill"></i>
            <span>Nuevo Paciente</span>
        </button>
    </div>
</div>

<!-- Grid de KPIs / Estadísticas SaaS -->
<div class="saas-stats-grid">
    <div class="saas-stat-card">
        <div class="saas-stat-top">
            <span class="saas-stat-label">Total en Directorio</span>
            <div class="saas-stat-icon">
                <i class="bi bi-people"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= $totalClientes ?></div>
        <div class="saas-stat-meta">
            <span class="text-muted">Pacientes registrados</span>
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-top">
            <span class="saas-stat-label">Pacientes Activos</span>
            <div class="saas-stat-icon is-blue">
                <i class="bi bi-person-check-fill"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= $clientesActivos ?></div>
        <div class="saas-stat-meta">
            <span class="saas-badge saas-badge-success py-0 px-2"><?= $totalClientes > 0 ? round(($clientesActivos / $totalClientes) * 100) : 0 ?>% del total</span>
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-top">
            <span class="saas-stat-label">Inactivos</span>
            <div class="saas-stat-icon is-gold">
                <i class="bi bi-person-dash"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= $clientesInactivos ?></div>
        <div class="saas-stat-meta">
            <span class="saas-badge saas-badge-neutral py-0 px-2">Sin actividad reciente</span>
        </div>
    </div>
</div>

<!-- Tarjeta Contenedor con Tabla de Clientes -->
<div class="saas-card">
    <div class="saas-card-head">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <h2 class="saas-card-title me-3">
                <i class="bi bi-person-vcard text-primary"></i>
                <span>Listado de Pacientes</span>
            </h2>
            <div class="saas-filter-tabs">
                <button type="button" class="saas-tab-btn is-active" onclick="filtrarClientesPorEstado('todos', this)">Todos</button>
                <button type="button" class="saas-tab-btn" onclick="filtrarClientesPorEstado('activo', this)">Activos</button>
                <button type="button" class="saas-tab-btn" onclick="filtrarClientesPorEstado('inactivo', this)">Inactivos</button>
            </div>
        </div>

        <div class="saas-search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="busquedaCliente" class="saas-search-input" placeholder="Buscar por nombre, teléfono, correo..." onkeyup="filtrarTablaClientes()">
        </div>
    </div>

    <div class="saas-card-body p-0">
        <div class="table-responsive">
            <table class="saas-table" id="tablaClientes">
                <thead>
                    <tr>
                        <th>PACIENTE / EXPEDIENTE</th>
                        <th>FECHA NACIMIENTO</th>
                        <th>TELÉFONO</th>
                        <th>CORREO ELECTRÓNICO</th>
                        <th>ESTADO</th>
                        <th class="text-end">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clientes)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-person-exclamation fs-1 text-muted d-block mb-2"></i>
                                No hay clientes registrados en el sistema.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($clientes as $c): ?>
                            <?php
                                $initials = strtoupper(mb_substr($c['nombre_completo'], 0, 1, 'UTF-8'));
                            ?>
                            <tr class="fila-cliente" data-estado="<?= htmlspecialchars($c['estado']) ?>">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="saas-avatar is-primary"><?= $initials ?></span>
                                        <div>
                                            <div class="fw-bold text-dark">
                                                <span class="text-muted fw-normal me-1">#<?= $c['id'] ?></span>
                                                <?= htmlspecialchars($c['nombre_completo']) ?>
                                            </div>
                                            <small class="text-muted" style="font-size: 0.72rem;">Alta: <?= date('d/m/Y', strtotime($c['creado_en'])) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-secondary small">
                                        <i class="bi bi-cake2 me-1"></i><?= date('d/m/Y', strtotime($c['fecha_nacimiento'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="tel:<?= htmlspecialchars($c['telefono']) ?>" class="text-decoration-none text-dark fw-medium small">
                                        <i class="bi bi-telephone text-success me-1"></i><?= htmlspecialchars($c['telefono']) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if (!empty($c['correo'])): ?>
                                        <a href="mailto:<?= htmlspecialchars($c['correo']) ?>" class="text-decoration-none text-muted small">
                                            <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($c['correo']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small fst-italic">Sin correo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($c['estado'] === 'activo'): ?>
                                        <span class="saas-badge saas-badge-success">
                                            <span class="saas-badge-dot"></span>
                                            Activo
                                        </span>
                                    <?php else: ?>
                                        <span class="saas-badge saas-badge-neutral">
                                            <span class="saas-badge-dot"></span>
                                            Inactivo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-sm btn-outline-primary py-1 px-2" title="Ver Historial" onclick="verHistorialCliente(<?= $c['id'] ?>, '<?= htmlspecialchars($c['nombre_completo'], ENT_QUOTES) ?>')">
                                            <i class="bi bi-clock-history"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary py-1 px-2" title="Editar Expediente" 
                                                onclick='editarCliente(<?= json_encode($c, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) ?>)'>
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-<?= $c['estado'] === 'activo' ? 'warning' : 'success' ?> py-1 px-2" 
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
</div>

<script>
function filtrarClientesPorEstado(estado, btn) {
    document.querySelectorAll('.saas-tab-btn').forEach(b => b.classList.remove('is-active'));
    if (btn) btn.classList.add('is-active');

    const rows = document.querySelectorAll('.fila-cliente');
    rows.forEach(r => {
        const rEstado = r.getAttribute('data-estado');
        if (estado === 'todos' || rEstado === estado) {
            r.style.display = '';
        } else {
            r.style.display = 'none';
        }
    });
}
</script>

<!-- Modal Registro / Edición de Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-editorial">
            <div class="modal-header">
                <div>
                    <span class="modal-kicker">Expediente</span>
                    <h5 class="modal-title" id="modalClienteLabel">
                        <i class="bi bi-person-lines-fill me-2"></i>Expediente de Cliente
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCliente" onsubmit="guardarCliente(event)">
                <div class="modal-body">
                    <input type="hidden" id="cliente_id" name="id">

                    <div class="mb-3">
                        <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                        <input type="text" id="nombre_completo" name="nombre_completo" class="form-control" placeholder="Ej. María Elena Torres" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Fecha nacimiento <span class="text-danger">*</span></label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                            <input type="tel" id="telefono" name="telefono" class="form-control" placeholder="Ej. 555-0192" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" id="correo" name="correo" class="form-control" placeholder="cliente@ejemplo.com">
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Estado del cliente</label>
                        <select id="estado" name="estado" class="form-select">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-quiet btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
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
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-editorial">
            <div class="modal-header">
                <div>
                    <span class="modal-kicker">Historial clínico</span>
                    <h5 class="modal-title">
                        <i class="bi bi-clock-history me-2"></i>Historial del Cliente: <span id="historialNombreCliente"></span>
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
                    <td><span class="fw-semibold">${esc(item.fecha)}</span></td>
                    <td>${esc(item.hora_inicio)} - ${esc(item.hora_fin)}</td>
                    <td>${esc(item.terapeuta_nombre || 'N/A')}</td>
                    <td class="fw-bold">C$${parseFloat(item.total).toFixed(2)}</td>
                    <td><span class="badge bg-secondary rounded-pill">${esc(item.estado)}</span></td>
                </tr>
            `).join('');
        } else {
            body.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-muted">El cliente no registra historial de citas previo.</td></tr>';
        }
    })
    .catch(() => {
        body.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-danger">No se pudo cargar el historial. Intente de nuevo.</td></tr>';
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
