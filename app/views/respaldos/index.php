<!-- SaaS Page Header -->
<div class="saas-page-header">
    <div>
        <div class="saas-breadcrumb">
            <i class="bi bi-hdd-stack-fill"></i>
            <span>Infraestructura &amp; Base de Datos</span>
            <span class="sep">/</span>
            <span>Respaldos &amp; Mantenimiento</span>
        </div>
        <h1 class="saas-title">Respaldos &amp; Salud del Sistema</h1>
        <p class="saas-subtitle">Copias de seguridad SQL, monitoreo de almacenamiento de tablas InnoDB, optimización de índices y restauración segura.</p>
    </div>
    <div class="saas-header-actions d-flex flex-wrap gap-2">
        <a href="index.php?c=respaldos&a=descargar" class="saas-btn saas-btn-primary text-decoration-none">
            <i class="bi bi-download"></i>
            <span>Descargar Respaldo</span>
        </a>
        <button class="saas-btn saas-btn-outline" onclick="guardarRespaldoLocal()">
            <i class="bi bi-hdd-network text-primary"></i>
            <span>Guardar en Servidor</span>
        </button>
        <button class="saas-btn saas-btn-outline" onclick="optimizarTablas()">
            <i class="bi bi-speedometer2 text-success"></i>
            <span>Optimizar Tablas</span>
        </button>
        <button class="saas-btn saas-btn-outline text-danger border-danger-subtle" data-bs-toggle="modal" data-bs-target="#modalRestaurar">
            <i class="bi bi-database-up"></i>
            <span>Restaurar Copia</span>
        </button>
    </div>
</div>

<!-- Alertas de Sesión -->
<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['mensaje_tipo'] ?? 'info') ?> alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
        <i class="bi bi-info-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['mensaje']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['mensaje'], $_SESSION['mensaje_tipo']); ?>
<?php endif; ?>

<!-- SaaS KPI Stats Grid -->
<div class="saas-stats-grid mb-4">
    <div class="saas-stat-card">
        <div class="saas-stat-header">
            <span class="saas-stat-label">Motor de Base de Datos</span>
            <div class="saas-stat-icon icon-emerald">
                <i class="bi bi-database-fill-check"></i>
            </div>
        </div>
        <div class="saas-stat-value">spa_db</div>
        <div class="saas-stat-change">
            <span class="badge-positive"><i class="bi bi-activity"></i> En línea</span>
            MySQL / MariaDB Activo
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-header">
            <span class="saas-stat-label">Tamaño Almacenado</span>
            <div class="saas-stat-icon icon-teal">
                <i class="bi bi-hdd-fill"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= number_format($tamanoTotalMB, 2) ?> <small class="fs-6 fw-normal text-muted">MB</small></div>
        <div class="saas-stat-change">
            <span class="badge-neutral"><i class="bi bi-table"></i> <?= count($tablas) ?> tablas</span>
            Espacio físico e índices
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-header">
            <span class="saas-stat-label">Registros Totales</span>
            <div class="saas-stat-icon icon-gold">
                <i class="bi bi-layers-fill"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= number_format($totalFilas) ?></div>
        <div class="saas-stat-change">
            <span class="badge-neutral">Consolidados</span>
            Filas en todas las entidades
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-header">
            <span class="saas-stat-label">Copias en Servidor</span>
            <div class="saas-stat-icon icon-indigo">
                <i class="bi bi-archive-fill"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= count($respaldos ?? []) ?></div>
        <div class="saas-stat-change">
            <span class="badge-neutral"><i class="bi bi-filetype-sql"></i> Archivos .SQL</span>
            Almacenamiento local
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <!-- Tabla de Estado de Tablas de la BD -->
    <div class="col-lg-7">
        <div class="saas-card h-100">
            <div class="saas-card-header">
                <div>
                    <h5 class="saas-card-title">Estructura y Salud de Tablas (spa_db)</h5>
                    <p class="saas-card-subtitle">Desglose de motor de almacenamiento, volumen de registros y peso por entidad</p>
                </div>
                <span class="saas-badge saas-badge-success">
                    <i class="bi bi-check-circle me-1"></i>Verificado
                </span>
            </div>
            <div class="table-responsive">
                <table class="saas-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nombre Tabla</th>
                            <th>Motor</th>
                            <th>Registros</th>
                            <th class="text-end">Tamaño (KB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tablas as $t): ?>
                            <tr>
                                <td class="fw-bold text-dark font-monospace" style="font-size: 0.85rem;">
                                    <i class="bi bi-table me-2 text-primary"></i><?= htmlspecialchars($t['nombre']) ?>
                                </td>
                                <td>
                                    <span class="saas-badge saas-badge-neutral"><?= htmlspecialchars($t['motor']) ?></span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-secondary"><?= number_format($t['filas']) ?></span>
                                </td>
                                <td class="text-end fw-bold font-monospace text-dark">
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
        <div class="saas-card h-100">
            <div class="saas-card-header">
                <div>
                    <h5 class="saas-card-title">Historial de Copias en Servidor</h5>
                    <p class="saas-card-subtitle">Instantáneas SQL almacenadas localmente</p>
                </div>
                <span class="saas-badge saas-badge-gold">
                    <i class="bi bi-hdd-stack me-1"></i>Snapshots
                </span>
            </div>
            <div class="p-3">
                <?php if (empty($respaldos)): ?>
                    <div class="text-center py-5 text-muted">
                        <div class="saas-stat-icon icon-teal mx-auto mb-3" style="width: 48px; height: 48px; font-size: 1.3rem;">
                            <i class="bi bi-hdd"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Sin copias locales registradas</h6>
                        <small class="d-block mb-3">Haz clic en "Guardar en Servidor" para generar un punto de restauración inmediato.</small>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($respaldos as $r): ?>
                            <div class="d-flex justify-content-between align-items-center p-2 px-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <div style="min-width: 0;">
                                    <div class="fw-bold text-dark small text-truncate" title="<?= htmlspecialchars($r['nombre']) ?>">
                                        <i class="bi bi-filetype-sql text-primary me-2"></i><?= htmlspecialchars($r['nombre']) ?>
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 0.74rem;">
                                        <i class="bi bi-clock me-1"></i><?= $r['fecha'] ?> • <span class="fw-semibold font-monospace"><?= $r['tamano_kb'] ?> KB</span>
                                    </small>
                                </div>
                                <button class="saas-btn saas-btn-outline px-2 py-1 text-danger" title="Eliminar copia" onclick="eliminarRespaldo('<?= htmlspecialchars($r['nombre'], ENT_QUOTES) ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
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
