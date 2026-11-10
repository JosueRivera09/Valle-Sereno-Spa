<?php 
$esTerapeuta = (($_SESSION['rol_nombre'] ?? '') === 'Terapeuta'); 
$totalSrv = count($servicios ?? []);
$srvActivos = count(array_filter($servicios ?? [], fn($s) => $s['estado'] === 'activo'));
$totalCat = count($categorias ?? []);
?>

<!-- Encabezado de Página SaaS -->
<div class="saas-page-header">
    <div class="saas-header-info">
        <span class="saas-breadcrumb"><i class="bi bi-sparkles me-1"></i>Catálogo & Terapias</span>
        <h1 class="saas-title">
            <?= $esTerapeuta ? 'Mis Especialidades & Terapias Habilitadas' : 'Catálogo de Servicios & Terapias' ?>
        </h1>
        <p class="saas-subtitle">
            <?= $esTerapeuta 
                ? 'Activa los tratamientos que estás capacitado para brindar; el sistema te asignará citas basándose en este menú.' 
                : 'Definición de tratamientos, tiempos de sesión, precios de catálogo y estructura de categorías.' ?>
        </p>
    </div>
    <?php if (!$esTerapeuta): ?>
    <div class="saas-header-actions">
        <button class="btn-saas-secondary" data-bs-toggle="modal" data-bs-target="#modalCategoria">
            <i class="bi bi-folder-plus"></i>
            <span>Nueva Categoría</span>
        </button>
        <button class="btn-saas-primary" data-bs-toggle="modal" data-bs-target="#modalServicio" onclick="prepararNuevoServicio()">
            <i class="bi bi-plus-circle"></i>
            <span>Nuevo Servicio</span>
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- Grid de KPIs / Estadísticas SaaS -->
<div class="saas-stats-grid">
    <div class="saas-stat-card">
        <div class="saas-stat-top">
            <span class="saas-stat-label"><?= $esTerapeuta ? 'Mis Especialidades' : 'Total en Catálogo' ?></span>
            <div class="saas-stat-icon">
                <i class="bi bi-card-checklist"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= $esTerapeuta ? count($misEspecialidades ?? []) : $totalSrv ?></div>
        <div class="saas-stat-meta">
            <span class="text-muted"><?= $esTerapeuta ? 'Habilitadas para tu perfil' : 'Tratamientos registrados' ?></span>
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-top">
            <span class="saas-stat-label">Categorías del Menú</span>
            <div class="saas-stat-icon is-gold">
                <i class="bi bi-tags-fill"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= $totalCat ?></div>
        <div class="saas-stat-meta">
            <span class="text-muted">Líneas de tratamiento</span>
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-top">
            <span class="saas-stat-label">Servicios Activos</span>
            <div class="saas-stat-icon is-blue">
                <i class="bi bi-check2-circle"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= $srvActivos ?></div>
        <div class="saas-stat-meta">
            <span class="saas-badge saas-badge-success py-0 px-2">Disponibles en cabina</span>
        </div>
    </div>
</div>

<!-- Tarjeta Contenedor con Tabla de Servicios -->
<div class="saas-card">
    <div class="saas-card-head">
        <h2 class="saas-card-title">
            <i class="bi bi-menu-button-wide text-primary"></i>
            <span><?= $esTerapeuta ? 'Menú de Terapias Habilitadas' : 'Catálogo de Tratamientos' ?></span>
        </h2>
        <div class="saas-search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="busquedaServicio" class="saas-search-input" placeholder="Buscar por tratamiento o categoría..." onkeyup="filtrarTablaServicios()">
        </div>
    </div>

    <div class="saas-card-body p-0">
        <div class="table-responsive">
            <table class="saas-table" id="tablaServicios">
                <thead>
                    <tr>
                        <th>TRATAMIENTO / SERVICIO</th>
                        <th>CATEGORÍA</th>
                        <th>DURACIÓN</th>
                        <th>PRECIO BASE</th>
                        <?php if ($esTerapeuta): ?>
                            <th class="text-center">MI DISPONIBILIDAD</th>
                        <?php else: ?>
                            <th>ESTADO</th>
                            <th class="text-end">ACCIONES</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($servicios)): ?>
                        <tr>
                            <td colspan="<?= $esTerapeuta ? '5' : '6' ?>" class="text-center py-5 text-muted">
                                <i class="bi bi-card-heading fs-1 text-muted d-block mb-2"></i>
                                No hay servicios o terapias registradas en el catálogo.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($servicios as $s): ?>
                            <?php $ofreceEstaTerapia = in_array($s['id'], $misEspecialidades ?? []); ?>
                            <tr class="fila-servicio">
                                <td>
                                    <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($s['nombre']) ?></div>
                                    <small class="text-muted d-block text-truncate" style="max-width: 380px;">
                                        <?= htmlspecialchars($s['descripcion'] ?? 'Sin descripción disponible') ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="saas-badge saas-badge-neutral">
                                        <i class="bi bi-tag me-1" style="color: #c5a059;"></i>
                                        <?= htmlspecialchars($s['categoria_nombre'] ?? 'General') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-dark fw-medium small">
                                        <i class="bi bi-clock text-warning me-1"></i><?= (int)$s['duracion_minutos'] ?> min
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-6">
                                        C$ <?= number_format($s['costo'], 2) ?>
                                    </span>
                                </td>
                                <?php if ($esTerapeuta): ?>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" role="switch" style="cursor: pointer; width: 2.2em; height: 1.15em;"
                                                   <?= $ofreceEstaTerapia ? 'checked' : '' ?>
                                                   onchange="toggleMiEspecialidad(<?= $s['id'] ?>, this)">
                                        </div>
                                        <small class="d-block text-muted style-lbl-esp" style="font-size: 0.72rem;">
                                            <?= $ofreceEstaTerapia ? '<span class="text-success fw-semibold">Habilitado</span>' : '<span class="text-muted">No ofertado</span>' ?>
                                        </small>
                                    </td>
                                <?php else: ?>
                                    <td>
                                        <?php if ($s['estado'] === 'activo'): ?>
                                            <span class="saas-badge saas-badge-success">
                                                <span class="saas-badge-dot"></span>
                                                Disponible
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
                                            <button class="btn btn-sm btn-outline-secondary py-1 px-2" title="Editar Servicio" onclick='editarServicio(<?= json_encode($s, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) ?>)'>
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-<?= $s['estado'] === 'activo' ? 'warning' : 'success' ?> py-1 px-2" 
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
