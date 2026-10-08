<!-- Header del Módulo -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--spa-primary); font-family: var(--font-serif);">
            <i class="bi bi-database-fill-gear text-warning me-2"></i> Respaldos & Mantenimiento del Sistema
        </h4>
        <p class="text-muted small mb-0">Gestión de copias de seguridad de la base de datos MySQL (spa_db), optimización de tablas y restauración.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="index.php?c=respaldos&a=descargar" class="btn btn-spa-primary btn-sm px-3 d-inline-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-download"></i>
            <span>Descargar Respaldo SQL</span>
        </a>
        <button class="btn btn-outline-success btn-sm px-3 d-inline-flex align-items-center gap-2" onclick="guardarRespaldoLocal()">
            <i class="bi bi-hdd-network"></i>
            <span>Guardar en Servidor</span>
        </button>
        <button class="btn btn-outline-warning btn-sm px-3 d-inline-flex align-items-center gap-2 text-dark" onclick="optimizarTablas()">
            <i class="bi bi-speedometer2"></i>
            <span>Optimizar Tablas</span>
        </button>
        <button class="btn btn-outline-danger btn-sm px-3 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalRestaurar">
            <i class="bi bi-database-up"></i>
            <span>Restaurar Copia</span>
        </button>
    </div>
</div>

<!-- Alertas de Sesión -->
<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['mensaje_tipo'] ?? 'info') ?> alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <?= htmlspecialchars($_SESSION['mensaje']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?unset($_SESSION['mensaje'], $_SESSION['mensaje_tipo']); ?>
<?php endif; ?>

<!-- Tarjetas KPI de Estado de la Base de Datos -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 text-white" style="background: var(--spa-primary);">
                <i class="bi bi-database fs-3"></i>
            </div>
            <div>
                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem;">Base de Datos</small>
                <span class="fs-6 fw-bold text-dark">spa_db</span>
                <small class="d-block text-success" style="font-size: 0.75rem;"><i class="bi bi-check-circle me-1"></i>En línea</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 bg-success text-white">
                <i class="bi bi-hdd-stack fs-3"></i>
            </div>
            <div>
                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem;">Tamaño Total Almacenado</small>
                <span class="fs-6 fw-bold text-dark"><?= number_format($tamanoTotalMB, 2) ?> MB</span>
                <small class="d-block text-muted" style="font-size: 0.75rem;"><?= count($tablas) ?> tablas activas</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 bg-warning text-dark">
                <i class="bi bi-list-columns-reverse fs-3"></i>
            </div>
            <div>
                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem;">Registros Totales</small>
                <span class="fs-6 fw-bold text-dark"><?= number_format($totalFilas) ?> filas</span>
                <small class="d-block text-muted" style="font-size: 0.75rem;">Consolidado general</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 bg-info text-white">
                <i class="bi bi-shield-check fs-3"></i>
            </div>
            <div>
                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem;">Integridad & Salud</small>
                <span class="fs-6 fw-bold text-dark">100% Saludable</span>
                <small class="d-block text-success" style="font-size: 0.75rem;"><i class="bi bi-patch-check me-1"></i>Sin desbordes</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Tabla de Estado de Tablas de la BD -->
    <div class="col-lg-7">
        <div class="spa-card p-4 h-100">
            <h5 class="fw-bold mb-3" style="color: var(--spa-primary); font-family: var(--font-serif);">
                <i class="bi bi-table text-warning me-2"></i> Estructura y Salud de Tablas (spa_db)
            </h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr style="font-size: 0.78rem; text-transform: uppercase; color: #556b61;">
                            <th>NOMBRE TABLA</th>
                            <th>MOTOR</th>
                            <th>REGISTROS</th>
                            <th class="text-end">TAMAÑO (KB)</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.88rem;">
                        <?php foreach ($tablas as $t): ?>
                            <tr>
                                <td class="fw-bold text-dark">
                                    <i class="bi bi-grid-3x3-gap me-2 text-warning"></i><?= htmlspecialchars($t['nombre']) ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= htmlspecialchars($t['motor']) ?></span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-secondary"><?= number_format($t['filas']) ?></span>
                                </td>
                                <td class="text-end fw-bold text-dark" style="font-family: var(--font-serif);">
                                    <?= number_format($t['tamano_kb'], 2) ?> KB
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Lista de Copias Guardadas en Servidor -->
    <div class="col-lg-5">
        <div class="spa-card p-4 h-100">
            <h5 class="fw-bold mb-3" style="color: var(--spa-primary); font-family: var(--font-serif);">
                <i class="bi bi-folder-check text-warning me-2"></i> Historial de Copias en Servidor
            </h5>
            <?php if (empty($respaldos)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-hdd-none fs-1 d-block mb-2 text-muted"></i>
                    No hay copias guardadas en el almacenamiento interno aún.<br>
                    <small>Haz clic en "Guardar en Servidor" para crear una.</small>
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($respaldos as $r): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3 px-0">
                            <div>
                                <div class="fw-bold text-dark small">
                                    <i class="bi bi-filetype-sql text-primary me-2"></i><?= htmlspecialchars($r['nombre']) ?>
                                </div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">
                                    <i class="bi bi-clock me-1"></i><?= $r['fecha'] ?> • <span class="fw-semibold"><?= $r['tamano_kb'] ?> KB</span>
                                </small>
                            </div>
                            <button class="btn btn-outline-danger btn-sm p-1 px-2" title="Eliminar copia" onclick="eliminarRespaldo('<?= htmlspecialchars($r['nombre']) ?>')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Restaurar Copia de Seguridad -->
<div class="modal fade" id="modalRestaurar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" style="font-family: var(--font-serif);">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> Restaurar Base de Datos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?c=respaldos&a=restaurar" method="POST" enctype="multipart/form-data" onsubmit="return confirm('ATENCIÓN: Esta acción reemplazará los datos actuales de spa_db por los del archivo SQL seleccionado. ¿Deseas continuar?');">
                <div class="modal-body p-4">
                    <div class="alert alert-warning py-2 px-3 small mb-3">
                        <i class="bi bi-info-circle me-1"></i> Selecciona un archivo de respaldo con extensión <strong>.sql</strong> generado previamente por este sistema.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Archivo de Respaldo (.sql) <span class="text-danger">*</span></label>
                        <input type="file" name="archivo_sql" class="form-control" accept=".sql" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4">
                        <i class="bi bi-upload me-1"></i> Iniciar Restauración
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function guardarRespaldoLocal() {
    if (!confirm('¿Deseas generar y guardar una copia de seguridad completa en el almacenamiento interno del servidor?')) return;

    fetch('index.php?c=respaldos&a=generarLocal')
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            alert(res.mensaje);
            location.reload();
        } else {
            alert('Error: ' + res.mensaje);
        }
    });
}

function optimizarTablas() {
    fetch('index.php?c=respaldos&a=optimizar')
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            alert('¡Las tablas de la base de datos se han optimizado correctamente!');
            location.reload();
        }
    });
}

function eliminarRespaldo(filename) {
    if (!confirm(`¿Deseas eliminar de forma permanente la copia de respaldo '${filename}'?`)) return;

    const fd = new FormData();
    fd.append('filename', filename);

    fetch('index.php?c=respaldos&a=eliminar', {
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
</script>
