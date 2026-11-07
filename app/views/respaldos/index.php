<!-- Encabezado Editorial -->
<div class="page-head">
    <div>
        <span class="kicker">Sistema</span>
        <h1 class="display-title">Respaldos <em>& Mantenimiento</em></h1>
    </div>
    <div class="page-head-actions">
        <a href="index.php?c=respaldos&a=descargar" class="btn btn-spa-primary btn-sm px-3 d-inline-flex align-items-center gap-2">
            <i class="bi bi-download"></i>
            <span>Descargar Respaldo </span>
        </a>
        <button class="btn btn-quiet btn-sm px-3 d-inline-flex align-items-center gap-2" onclick="guardarRespaldoLocal()">
            <i class="bi bi-hdd-network"></i>
            <span>Guardar en Servidor</span>
        </button>
        <button class="btn btn-quiet btn-sm px-3 d-inline-flex align-items-center gap-2" onclick="optimizarTablas()">
            <i class="bi bi-speedometer2"></i>
            <span>Optimizar Tablas</span>
        </button>
        <button class="btn btn-danger btn-sm px-3 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalRestaurar">
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
    <?php unset($_SESSION['mensaje'], $_SESSION['mensaje_tipo']); ?>
<?php endif; ?>

<!-- Métricas Libro Mayor de la Base de Datos -->
<div class="metrics">
    <div class="metric">
        <span class="metric-label">Base de Datos</span>
        <span class="metric-value">spa_db</span>
        <span class="metric-note"><strong>En línea</strong></span>
    </div>
    <div class="metric">
        <span class="metric-label">Tamaño Total Almacenado</span>
        <span class="metric-value"><?= number_format($tamanoTotalMB, 2) ?> MB</span>
        <span class="metric-note"><?= count($tablas) ?> tablas activas</span>
    </div>
    <div class="metric">
        <span class="metric-label">Registros Totales</span>
        <span class="metric-value"><?= number_format($totalFilas) ?></span>
        <span class="metric-note">Filas consolidadas</span>
    </div>
</div>

<div class="row g-4">
    <!-- Tabla de Estado de Tablas de la BD -->
    <div class="col-lg-7">
        <div class="panel h-100">
            <div class="panel-head">
                <h2 class="panel-title">Estructura y Salud de Tablas (spa_db)</h2>
            </div>
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
        <div class="panel h-100">
            <div class="panel-head">
                <h2 class="panel-title">Historial de Copias en Servidor</h2>
            </div>
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
                            <button class="btn btn-outline-danger btn-sm p-1 px-2" title="Eliminar copia" onclick="eliminarRespaldo('<?= htmlspecialchars($r['nombre'], ENT_QUOTES) ?>')">
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
        <div class="modal-content modal-editorial is-danger">
            <div class="modal-header">
                <div>
                    <span class="modal-kicker">Mantenimiento</span>
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Restaurar Base de Datos
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?c=respaldos&a=restaurar" method="POST" enctype="multipart/form-data" onsubmit="return confirm('ATENCIÓN: Esta acción reemplazará los datos actuales de spa_db por los del archivo SQL seleccionado. ¿Deseas continuar?');">
                <input type="hidden" name="csrf_token" value="<?= AuthHelper::csrfToken() ?>">
                <div class="modal-body">
                    <div class="modal-note is-danger">
                        <i class="bi bi-info-circle"></i>
                        <span>Selecciona un archivo de respaldo con extensión <strong>.sql</strong> generado previamente por este sistema.</span>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Archivo de respaldo (.sql) <span class="text-danger">*</span></label>
                        <input type="file" name="archivo_sql" class="form-control" accept=".sql" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-quiet btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
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

    fetch('index.php?c=respaldos&a=generarLocal', { method: 'POST' })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            alert(res.mensaje);
            location.reload();
        } else {
            alert('Error: ' + res.mensaje);
        }
    })
    .catch(() => alert('Ocurrió un error de conexión al generar el respaldo.'));
}

function optimizarTablas() {
    fetch('index.php?c=respaldos&a=optimizar', { method: 'POST' })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            alert('¡Las tablas de la base de datos se han optimizado correctamente!');
            location.reload();
        } else {
            alert('Error: ' + (res.mensaje || 'No se pudieron optimizar las tablas.'));
        }
    })
    .catch(() => alert('Ocurrió un error de conexión al optimizar las tablas.'));
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
    })
    .catch(() => alert('Ocurrió un error de conexión al eliminar la copia.'));
}
</script>
