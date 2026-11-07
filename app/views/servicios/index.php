<?php $esTerapeuta = (($_SESSION['rol_nombre'] ?? '') === 'Terapeuta'); ?>
<!-- Encabezado Editorial -->
<div class="page-head">
    <div>
        <span class="kicker">Catálogo</span>
        <h1 class="display-title">
            <?= $esTerapeuta ? 'Mis Servicios <em>& Especialidades</em>' : 'Servicios <em>& Terapias</em>' ?>
        </h1>
        <?php if ($esTerapeuta): ?>
            <p class="page-lede">Selecciona los tratamientos que estás capacitado para ofrecer. El sistema te asignará citas basándose en tu menú personal.</p>
        <?php endif; ?>
    </div>
    <?php if (!$esTerapeuta): ?>
    <div class="page-head-actions">
        <button class="btn btn-quiet btn-sm px-3 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalCategoria">
            <i class="bi bi-folder-plus"></i>
            <span>Nueva Categoría</span>
        </button>
        <button class="btn btn-spa-primary btn-sm px-3 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalServicio" onclick="prepararNuevoServicio()">
            <i class="bi bi-plus-circle"></i>
            <span>Nuevo Servicio</span>
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- Métricas Libro Mayor -->
<div class="metrics">
    <div class="metric">
        <span class="metric-label"><?= $esTerapeuta ? 'Especialidades Mías' : 'Servicios en Menú' ?></span>
        <span class="metric-value"><?= $esTerapeuta ? count($misEspecialidades ?? []) : count($servicios ?? []) ?></span>
    </div>
    <div class="metric">
        <span class="metric-label">Categorías Activas</span>
        <span class="metric-value"><?= count($categorias ?? []) ?></span>
    </div>
    <div class="metric">
        <span class="metric-label">Servicios Activos Spa</span>
        <span class="metric-value"><?= count(array_filter($servicios ?? [], fn($s) => $s['estado'] === 'activo')) ?></span>
        <span class="metric-note"><strong>Disponibles en cabinas</strong></span>
    </div>
</div>

<!-- Tabla de Servicios -->
<div class="spa-card p-4">
    <div class="panel-head">
        <h2 class="panel-title">
            <?= $esTerapeuta ? 'Menú de Terapias Habilitadas' : 'Tratamientos Registrados' ?>
        </h2>
        <div class="input-group input-group-sm" style="max-width: 280px;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="busquedaServicio" class="form-control border-start-0" placeholder="Buscar tratamiento..." onkeyup="filtrarTablaServicios()">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tablaServicios">
            <thead class="table-light">
                <tr style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #556b61;">
                    <th>TRATAMIENTO / SERVICIO</th>
                    <th>CATEGORÍA</th>
                    <th>DURACIÓN</th>
                    <th>COSTO</th>
                    <?php if ($esTerapeuta): ?>
                        <th class="text-center">MI ESPECIALIDAD</th>
                    <?php else: ?>
                        <th>ESTADO</th>
                        <th class="text-end">ACCIONES</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody style="font-size: 0.88rem;">
                <?php if (empty($servicios)): ?>
                    <tr>
                        <td colspan="<?= $esTerapeuta ? '5' : '6' ?>" class="text-center py-4 text-muted">
                            <i class="bi bi-card-heading fs-3 d-block mb-1 text-muted"></i>
                            No hay servicios o terapias registradas en el catálogo.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($servicios as $s): ?>
                        <?php $ofreceEstaTerapia = in_array($s['id'], $misEspecialidades ?? []); ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($s['nombre']) ?></div>
                                <small class="text-muted d-block" style="font-size: 0.75rem; max-width: 320px;">
                                    <?= htmlspecialchars($s['descripcion'] ?? 'Sin descripción disponible') ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-dark border px-2 py-1">
                                    <i class="bi bi-tag me-1"></i><?= htmlspecialchars($s['categoria_nombre'] ?? 'Sin categoría') ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-medium text-dark">
                                    <i class="bi bi-clock text-warning me-1"></i><?= (int)$s['duracion_minutos'] ?> min
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-dark fs-6" style="font-family: var(--font-serif); color: var(--spa-primary);">
                                    C$<?= number_format($s['costo'], 2) ?>
                                </span>
                            </td>
                            <?php if ($esTerapeuta): ?>
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input" type="checkbox" role="switch" style="cursor: pointer; width: 2.3em; height: 1.2em;"
                                               <?= $ofreceEstaTerapia ? 'checked' : '' ?>
                                               onchange="toggleMiEspecialidad(<?= $s['id'] ?>, this)">
                                    </div>
                                    <small class="d-block text-muted style-lbl-esp" style="font-size: 0.72rem;">
                                        <?= $ofreceEstaTerapia ? '<span class="text-success fw-semibold">Presto esta terapia</span>' : '<span class="text-muted">No ofertado</span>' ?>
                                    </small>
                                </td>
                            <?php else: ?>
                                <td>
                                    <?php if ($s['estado'] === 'activo'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">Disponible</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-1">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-secondary" title="Editar Servicio" onclick='editarServicio(<?= json_encode($s, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) ?>)'>
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-<?= $s['estado'] === 'activo' ? 'warning' : 'success' ?>" 
                                                title="<?= $s['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?>" 
                                                onclick="cambiarEstadoServicio(<?= $s['id'] ?>, '<?= $s['estado'] === 'activo' ? 'inactivo' : 'activo' ?>')">
                                            <i class="bi bi-toggle-<?= $s['estado'] === 'activo' ? 'on' : 'off' ?>"></i>
                                        </button>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Alta / Edición Servicio -->
<div class="modal fade" id="modalServicio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-editorial">
            <div class="modal-header">
                <div>
                    <span class="modal-kicker">Catálogo</span>
                    <h5 class="modal-title" id="modalServicioLabel">
                        <i class="bi bi-plus-circle me-2"></i>Registrar Nuevo Servicio
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formServicio" onsubmit="guardarServicio(event)">
                <div class="modal-body">
                    <input type="hidden" id="servicio_id" name="id">

                    <div class="mb-3">
                        <label class="form-label">Categoría <span class="text-danger">*</span></label>
                        <select id="id_categoria" name="id_categoria" class="form-select" required>
                            <option value="">-- Seleccionar Categoría --</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre del servicio <span class="text-danger">*</span></label>
                        <input type="text" id="nombre_servicio" name="nombre" class="form-control" placeholder="Ej. Masaje Descontracturante" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Costo (C$) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" id="costo" name="costo" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Duración (min) <span class="text-danger">*</span></label>
                            <input type="number" id="duracion_minutos" name="duracion_minutos" class="form-control" placeholder="60" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea id="descripcion_servicio" name="descripcion" class="form-control" rows="3" placeholder="Detalles de la experiencia o protocolo..."></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Estado</label>
                        <select id="estado_servicio" name="estado" class="form-select">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-quiet btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spa-primary btn-sm px-4">
                        <i class="bi bi-save me-1"></i> Guardar en Catálogo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Nueva Categoría -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-editorial">
            <div class="modal-header">
                <div>
                    <span class="modal-kicker">Catálogo</span>
                    <h5 class="modal-title">
                        <i class="bi bi-folder-plus me-2"></i>Crear Nueva Categoría
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCategoria" onsubmit="guardarCategoria(event)">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la categoría <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej. Hidroterapia & Saunas" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Descripción breve</label>
                        <textarea name="descripcion" class="form-control" rows="2" placeholder="Área terapéutica..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-quiet btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spa-primary btn-sm px-4">
                        <i class="bi bi-check2-circle me-1"></i> Crear Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let modalServicioBs, modalCategoriaBs;

document.addEventListener('DOMContentLoaded', () => {
    modalServicioBs = new bootstrap.Modal(document.getElementById('modalServicio'));
    modalCategoriaBs = new bootstrap.Modal(document.getElementById('modalCategoria'));
});

function prepararNuevoServicio() {
    document.getElementById('formServicio').reset();
    document.getElementById('servicio_id').value = '';
    document.getElementById('modalServicioLabel').innerHTML = '<i class="bi bi-plus-circle text-warning me-2"></i> Registrar Nuevo Servicio';
}

function editarServicio(s) {
    document.getElementById('servicio_id').value = s.id;
    document.getElementById('id_categoria').value = s.id_categoria;
    document.getElementById('nombre_servicio').value = s.nombre;
    document.getElementById('costo').value = s.costo;
    document.getElementById('duracion_minutos').value = s.duracion_minutos;
    document.getElementById('descripcion_servicio').value = s.descripcion || '';
    document.getElementById('estado_servicio').value = s.estado;
    document.getElementById('modalServicioLabel').innerHTML = '<i class="bi bi-pencil-square text-warning me-2"></i> Editar Servicio / Tratamiento';
    modalServicioBs.show();
}

function guardarServicio(e) {
    e.preventDefault();
    const fd = new FormData(document.getElementById('formServicio'));

    fetch('index.php?c=servicios&a=guardar', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            modalServicioBs.hide();
            location.reload();
        } else {
            alert('Error: ' + res.mensaje);
        }
    });
}

function guardarCategoria(e) {
    e.preventDefault();
    const fd = new FormData(document.getElementById('formCategoria'));

    fetch('index.php?c=servicios&a=guardarCategoria', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            modalCategoriaBs.hide();
            location.reload();
        } else {
            alert('Error: ' + res.mensaje);
        }
    });
}

function cambiarEstadoServicio(id, nuevoEstado) {
    if (!confirm(`¿Deseas cambiar el estado del servicio a '${nuevoEstado}'?`)) return;

    const fd = new FormData();
    fd.append('id', id);
    fd.append('estado', nuevoEstado);

    fetch('index.php?c=servicios&a=cambiarEstado', {
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

function toggleMiEspecialidad(idServicio, checkbox) {
    const fd = new FormData();
    fd.append('id_servicio', idServicio);

    checkbox.disabled = true;
    fetch('index.php?c=servicios&a=toggleEspecialidad', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        checkbox.disabled = false;
        if (!res.success) {
            checkbox.checked = !checkbox.checked;
            alert('Error: ' + res.mensaje);
        }
    })
    .catch(err => {
        checkbox.disabled = false;
        checkbox.checked = !checkbox.checked;
        alert('Ocurrió un error de conexión');
    });
}
</script>
