<?php $esTerapeuta = (($_SESSION['rol_nombre'] ?? '') === 'Terapeuta'); ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--spa-primary); font-family: var(--font-serif);">
            <i class="bi bi-stars text-warning me-2"></i>
            <?= $esTerapeuta ? 'Mis Servicios & Especialidades Terapéuticas' : 'Catálogo de Servicios & Terapias' ?>
        </h4>
        <p class="text-muted small mb-0">
            <?= $esTerapeuta 
                ? 'Selecciona los tratamientos que estás capacitado para ofrecer a los clientes. El sistema te asignará citas basándose en tu menú personal.' 
                : 'Gestión de tratamientos de bienestar, duraciones, precios por cabina y categorías.' ?>
        </p>
    </div>
    <?php if (!$esTerapeuta): ?>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm px-3 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalCategoria">
            <i class="bi bi-folder-plus text-warning"></i>
            <span>Nueva Categoría</span>
        </button>
        <button class="btn btn-spa-primary btn-sm px-3 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalServicio" onclick="prepararNuevoServicio()">
            <i class="bi bi-plus-circle"></i>
            <span>Nuevo Servicio</span>
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- Tarjetas Resumen -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-4">
        <div class="spa-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">
                    <?= $esTerapeuta ? 'Especialidades Mías' : 'Servicios en Menú' ?>
                </small>
                <h3 class="fw-bold mb-0 text-dark" style="font-family: var(--font-serif);">
                    <?= $esTerapeuta ? count($misEspecialidades ?? []) : count($servicios ?? []) ?>
                </h3>
            </div>
            <div class="stat-icon" style="background: rgba(30, 61, 52, 0.1); color: var(--spa-primary);">
                <i class="bi bi-flower1"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="spa-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">Categorías Activas</small>
                <h3 class="fw-bold mb-0 text-success" style="font-family: var(--font-serif);"><?= count($categorias ?? []) ?></h3>
            </div>
            <div class="stat-icon" style="background: rgba(197, 160, 89, 0.15); color: #997838;">
                <i class="bi bi-tags-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="spa-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">Servicios Activos Spa</small>
                <h3 class="fw-bold mb-0 text-primary" style="font-family: var(--font-serif);">
                    <?= count(array_filter($servicios ?? [], fn($s) => $s['estado'] === 'activo')) ?>
                </h3>
            </div>
            <div class="stat-icon" style="background: #eef6f3; color: #3b735c;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Servicios -->
<div class="spa-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0" style="color: var(--spa-primary); font-family: var(--font-serif);">
            <i class="bi bi-card-checklist text-warning me-2"></i> 
            <?= $esTerapeuta ? 'Menú de Terapias Habilitadas' : 'Tratamientos Registrados' ?>
        </h5>
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
                                        <button class="btn btn-outline-secondary" title="Editar Servicio" onclick='editarServicio(<?= json_encode($s) ?>)'>
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
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--spa-primary);">
                <h5 class="modal-title fw-bold" id="modalServicioLabel" style="font-family: var(--font-serif);">
                    <i class="bi bi-plus-circle text-warning me-2"></i> Registrar Nuevo Servicio
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formServicio" onsubmit="guardarServicio(event)">
                <div class="modal-body p-4">
                    <input type="hidden" id="servicio_id" name="id">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Categoría <span class="text-danger">*</span></label>
                        <select id="id_categoria" name="id_categoria" class="form-select" required>
                            <option value="">-- Seleccionar Categoría --</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Nombre del Servicio <span class="text-danger">*</span></label>
                        <input type="text" id="nombre_servicio" name="nombre" class="form-control" placeholder="Ej. Masaje Descontracturante" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-dark">Costo (C$ Córdobas) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" id="costo" name="costo" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-dark">Duración (Minutos) <span class="text-danger">*</span></label>
                            <input type="number" id="duracion_minutos" name="duracion_minutos" class="form-control" placeholder="60" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Descripción</label>
                        <textarea id="descripcion_servicio" name="descripcion" class="form-control" rows="3" placeholder="Detalles de la experiencia o protocolo..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Estado</label>
                        <select id="estado_servicio" name="estado" class="form-select">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
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
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--spa-primary);">
                <h5 class="modal-title fw-bold" style="font-family: var(--font-serif);">
                    <i class="bi bi-folder-plus text-warning me-2"></i> Crear Nueva Categoría
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCategoria" onsubmit="guardarCategoria(event)">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Nombre de la Categoría <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej. Hidroterapia & Saunas" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Descripción Breve</label>
                        <textarea name="descripcion" class="form-control" rows="2" placeholder="Área terapéutica..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
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
