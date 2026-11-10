<?php
/**
 * Vista Dinámica del Dashboard Principal — Composición Editorial
 * Valle Sereno Spa
 */
$metricas = $metricas ?? [
    'citas_hoy' => 0,
    'citas_confirmadas' => 0,
    'citas_pendientes' => 0,
    'citas_completadas' => 0,
    'terapeutas_activos' => 0,
    'clientes_registrados' => 0,
    'ingresos_hoy' => 0.00,
    'transacciones_hoy' => 0,
    'servicios_activos' => 0
];
$citasHoy = $citasHoy ?? [];
$serviciosPopulares = $serviciosPopulares ?? [];
$resumenPagos = $resumenPagos ?? [];
$ingresosGrafico = $ingresosGrafico ?? [];
$citasCategorias = $citasCategorias ?? [];

// Preparar arrays para Chart.js
$labelsDias = [];
$totalesDias = [];
foreach ($ingresosGrafico as $item) {
    $labelsDias[] = date('d/m', strtotime($item['fecha']));
    $totalesDias[] = (float)$item['total'];
}

$labelsCat = [];
$totalesCat = [];
foreach ($citasCategorias as $cat) {
    $labelsCat[] = $cat['categoria'];
    $totalesCat[] = (int)$cat['total_citas'];
}

// Fecha y saludo
$fechaActual  = new DateTime();
$diasSemana   = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$mesesAnio    = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
$fechaLegible = $diasSemana[(int)$fechaActual->format('w')] . ', ' . (int)$fechaActual->format('j') . ' de ' . $mesesAnio[(int)$fechaActual->format('n') - 1] . ' de ' . $fechaActual->format('Y');

$nombreSession = trim($_SESSION['nombre'] ?? 'Equipo');
$partesNombre  = preg_split('/\s+/', $nombreSession);
$nombreSaludo  = $partesNombre[0] ?? 'Equipo';

$horaEntera = (int)$fechaActual->format('G');
if ($horaEntera < 12) {
    $saludo = 'Buenos días';
} elseif ($horaEntera < 19) {
    $saludo = 'Buenas tardes';
} else {
    $saludo = 'Buenas noches';
}

$tagClass = match (true) {
    default => 'tag--pendiente',
};
?>

<!-- Encabezado de Página SaaS -->
<div class="saas-page-header">
    <div class="saas-header-info">
        <span class="saas-breadcrumb"><i class="bi bi-calendar-event me-1"></i><?= htmlspecialchars($fechaLegible, ENT_QUOTES, 'UTF-8') ?></span>
        <h1 class="saas-title"><?= htmlspecialchars($saludo, ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($nombreSaludo, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="saas-subtitle">Panel de operaciones, flujo de agenda y conciliación diaria en tiempo real.</p>
    </div>
    <div class="saas-header-actions">
        <a href="index.php?c=citas&a=index&crear=1" class="btn-saas-primary">
            <i class="bi bi-calendar-plus"></i>
            <span>Nueva Cita</span>
        </a>
        <a href="index.php?c=pagos&a=index" class="btn-saas-secondary">
            <i class="bi bi-receipt"></i>
            <span>Caja y Cobros</span>
        </a>
    </div>
</div>

<!-- Grid de KPIs / Estadísticas SaaS -->
<div class="saas-stats-grid">
    <!-- Citas de Hoy -->
    <div class="saas-stat-card">
        <div class="saas-stat-top">
            <span class="saas-stat-label">Citas de Hoy</span>
            <div class="saas-stat-icon">
                <i class="bi bi-calendar-check"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= (int)$metricas['citas_hoy'] ?></div>
        <div class="saas-stat-meta">
            <span class="saas-badge saas-badge-success py-0 px-2"><?= (int)$metricas['citas_confirmadas'] ?> confirmadas</span>
            <?php if ((int)$metricas['citas_pendientes'] > 0): ?>
                <span class="saas-badge saas-badge-warning py-0 px-2"><?= (int)$metricas['citas_pendientes'] ?> pendientes</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Facturación del Día -->
    <div class="saas-stat-card">
        <div class="saas-stat-top">
            <span class="saas-stat-label">Ingresos de Hoy</span>
            <div class="saas-stat-icon is-gold">
                <i class="bi bi-currency-dollar"></i>
            </div>
        </div>
        <div class="saas-stat-value">C$ <?= number_format((float)$metricas['ingresos_hoy'], 2) ?></div>
        <div class="saas-stat-meta">
            <i class="bi bi-check-circle-fill text-success"></i>
            <span><?= (int)$metricas['transacciones_hoy'] ?> cobros liquidados</span>
        </div>
    </div>

    <!-- Terapeutas Activos -->
    <div class="saas-stat-card">
        <div class="saas-stat-top">
            <span class="saas-stat-label">Terapeutas en Turno</span>
            <div class="saas-stat-icon is-blue">
                <i class="bi bi-person-badge"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= (int)$metricas['terapeutas_activos'] ?></div>
        <div class="saas-stat-meta">
            <i class="bi bi-clock-history text-primary"></i>
            <span>Personal con turno activo</span>
        </div>
    </div>

    <!-- Clientes Registrados -->
    <div class="saas-stat-card">
        <div class="saas-stat-top">
            <span class="saas-stat-label">Pacientes en Sistema</span>
            <div class="saas-stat-icon is-purple">
                <i class="bi bi-people"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= (int)$metricas['clientes_registrados'] ?></div>
        <div class="saas-stat-meta">
            <i class="bi bi-shield-check text-purple"></i>
            <span>Base de clientes fidelizada</span>
        </div>
    </div>
</div>

<!-- Layout Principal de 2 Columnas -->
<div class="row g-3 mb-3">
    <!-- Columna Izquierda: Agenda del Día -->
    <div class="col-lg-8">
        <div class="saas-card h-100 mb-0">
            <div class="saas-card-head">
                <h2 class="saas-card-title">
                    <i class="bi bi-clock text-gold"></i>
                    <span>Agenda de Pacientes — Hoy</span>
                </h2>
                <a href="index.php?c=citas&a=index" class="btn btn-sm btn-outline-secondary px-3 py-1 font-semibold" style="font-size: 0.78rem;">
                    Ver agenda completa <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="saas-card-body">
                <?php if (empty($citasHoy)): ?>
                    <div class="text-center py-3">
                        <i class="bi bi-calendar-x fs-2 text-muted d-block mb-1"></i>
                        <h6 class="text-dark fw-bold mb-1">Sin citas programadas para hoy</h6>
                        <p class="text-muted small mb-2">La agenda se encuentra libre para nuevas atenciones o clientes sin cita previa.</p>
                        <a href="index.php?c=citas&a=index&crear=1" class="btn-saas-primary btn-sm mx-auto">
                            <i class="bi bi-calendar-plus"></i> Agendar Primera Cita
                        </a>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($citasHoy as $cita): ?>
                            <?php
                            $estadoCita = $cita['estado'] ?? '';
                            $badgeClass = match ($estadoCita) {
                                'Confirmada' => 'saas-badge-success',
                                'Pendiente'  => 'saas-badge-warning',
                                'Completada' => 'saas-badge-info',
                                'Cancelada'  => 'saas-badge-danger',
                                default      => 'saas-badge-neutral',
                            };
                            $horaCita = '';
                            if (!empty($cita['hora_inicio'])) {
                                $ts = strtotime($cita['hora_inicio']);
                                $horaCita = $ts ? date('g:i A', $ts) : $cita['hora_inicio'];
                            }
                            ?>
                            <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between p-2 px-3 rounded-3 border bg-white gap-2 shadow-xs">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex flex-column align-items-center justify-content-center bg-light rounded-2 px-2 py-1 border text-dark" style="min-width: 82px;">
                                        <span class="fw-bold" style="font-size: 0.82rem;"><?= htmlspecialchars($horaCita) ?></span>
                                        <small class="text-muted" style="font-size: 0.65rem;">Horario</small>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.9rem;"><?= htmlspecialchars($cita['cliente_nombre'] ?? 'Paciente') ?></div>
                                        <div class="text-muted" style="font-size: 0.78rem;">
                                            <span class="text-dark fw-medium"><?= htmlspecialchars($cita['servicio_nombre'] ?? 'Tratamiento') ?></span>
                                            &bull; Terapeuta: <span class="text-secondary"><?= htmlspecialchars($cita['terapeuta_nombre'] ?? 'Sin Asignar') ?></span>
                                            <?php if (!empty($cita['cliente_telefono'])): ?>
                                                &bull; <i class="bi bi-telephone ms-1"></i> <?= htmlspecialchars($cita['cliente_telefono']) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2 ms-auto ms-sm-0">
                                    <div class="text-end">
                                        <span class="fw-bold text-dark d-block" style="font-size: 0.88rem;">C$<?= number_format((float)($cita['total'] ?? 0), 2) ?></span>
                                        <span class="saas-badge <?= $badgeClass ?>">
                                            <span class="saas-badge-dot"></span>
                                            <?= htmlspecialchars($estadoCita) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Columna Derecha: Recaudación y Acciones -->
    <div class="col-lg-4">
        <div class="d-flex flex-column gap-3 h-100">
            <!-- Arqueo Rápido de Caja -->
            <div class="saas-card mb-0">
                <div class="saas-card-head">
                    <h2 class="saas-card-title">
                        <i class="bi bi-wallet2 text-primary"></i>
                        <span>Medios de Pago</span>
                    </h2>
                    <a href="index.php?c=pagos&a=index" class="text-muted small text-decoration-none">Ver Caja &rarr;</a>
                </div>
                <div class="saas-card-body">
                    <?php if (empty($resumenPagos)): ?>
                        <p class="text-muted small mb-0 text-center py-2">No hay cobros liquidados hoy.</p>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($resumenPagos as $p): ?>
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-semibold text-dark small">
                                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem; color: #c5a059;"></i>
                                            <?= htmlspecialchars($p['tipo_pago']) ?>
                                        </span>
                                        <span class="fw-bold text-dark small">C$ <?= number_format((float)$p['total'], 2) ?></span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <?php 
                                            $totalHoy = (float)($metricas['ingresos_hoy'] > 0 ? $metricas['ingresos_hoy'] : 1);
                                            $pct = round(((float)$p['total'] / $totalHoy) * 100);
                                        ?>
                                        <div class="progress-bar bg-success" style="width: <?= $pct ?>%;"></div>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;"><?= (int)$p['cantidad'] ?> transacción(es) (<?= $pct ?>% del total)</small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Accesos Directos SaaS -->
            <div class="saas-card flex-grow-1 mb-0">
                <div class="saas-card-head">
                    <h2 class="saas-card-title">
                        <i class="bi bi-grid text-gold"></i>
                        <span>Accesos Rápidos</span>
                    </h2>
                </div>
                <div class="saas-card-body p-2">
                    <div class="d-flex flex-column gap-1">
                        <a href="index.php?c=citas&a=index&crear=1" class="d-flex align-items-center justify-content-between p-2 rounded-3 text-decoration-none text-dark hover-bg-light">
                            <div class="d-flex align-items-center gap-2">
                                <div class="saas-stat-icon is-gold" style="width: 32px; height: 32px; font-size: 0.95rem;">
                                    <i class="bi bi-calendar-plus"></i>
                                </div>
                                <div>
                                    <div class="fw-bold small">Agendar Nueva Cita</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Cabina, horario y terapeuta</div>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted small"></i>
                        </a>
                        <a href="index.php?c=clientes&a=index" class="d-flex align-items-center justify-content-between p-2 rounded-3 text-decoration-none text-dark hover-bg-light">
                            <div class="d-flex align-items-center gap-2">
                                <div class="saas-stat-icon is-blue" style="width: 32px; height: 32px; font-size: 0.95rem;">
                                    <i class="bi bi-person-plus"></i>
                                </div>
                                <div>
                                    <div class="fw-bold small">Registrar Paciente</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Expediente y preferencias</div>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted small"></i>
                        </a>
                        <a href="index.php?c=pagos&a=index" class="d-flex align-items-center justify-content-between p-2 rounded-3 text-decoration-none text-dark hover-bg-light">
                            <div class="d-flex align-items-center gap-2">
                                <div class="saas-stat-icon" style="width: 32px; height: 32px; font-size: 0.95rem;">
                                    <i class="bi bi-receipt"></i>
                                </div>
                                <div>
                                    <div class="fw-bold small">Caja & Cobros</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Arqueo y comprobantes</div>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted small"></i>
                        </a>
                        <?php if (($_SESSION['rol_nombre'] ?? '') === 'Administrador'): ?>
                            <a href="index.php?c=reportes&a=index" class="d-flex align-items-center justify-content-between p-2 rounded-3 text-decoration-none text-dark hover-bg-light">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="saas-stat-icon is-purple" style="width: 32px; height: 32px; font-size: 0.95rem;">
                                        <i class="bi bi-graph-up"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold small">Reportes Ejecutivos</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">Análisis de facturación y PDF</div>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-right text-muted small"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos Analíticos -->
<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="saas-card h-100 mb-0">
            <div class="saas-card-head">
                <h2 class="saas-card-title">
                    <i class="bi bi-graph-up text-primary"></i>
                    <span>Evolución de Recaudación Diaria</span>
                </h2>
                <span class="badge bg-light text-muted border font-semibold">Últimos días</span>
            </div>
            <div class="saas-card-body">
                <div style="height: 210px; position: relative;">
                    <canvas id="chartIngresos"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="saas-card h-100 mb-0">
            <div class="saas-card-head">
                <h2 class="saas-card-title">
                    <i class="bi bi-pie-chart text-gold"></i>
                    <span>Demanda por Categoría</span>
                </h2>
            </div>
            <div class="saas-card-body">
                <div style="height: 210px; position: relative;">
                    <canvas id="chartCategorias"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Catálogo: Servicios Más Solicitados -->
<div class="saas-card mb-0">
    <div class="saas-card-head">
        <h2 class="saas-card-title">
            <i class="bi bi-trophy text-gold"></i>
            <span>Tratamientos Más Solicitados</span>
        </h2>
        <a href="index.php?c=servicios&a=index" class="btn btn-sm btn-outline-secondary px-3 py-1 font-semibold" style="font-size: 0.78rem;">
            Catálogo completo <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="saas-card-body p-0">
        <?php if (empty($serviciosPopulares)): ?>
            <p class="text-muted text-center py-4 mb-0">No hay registros de solicitudes aún.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="saas-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">RANK</th>
                            <th>TRATAMIENTO / SERVICIO</th>
                            <th>CATEGORÍA</th>
                            <th>DURACIÓN</th>
                            <th>CITAS REALIZADAS</th>
                            <th class="text-end">PRECIO BASE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $pos = 1; ?>
                        <?php foreach ($serviciosPopulares as $sp): ?>
                            <tr>
                                <td>
                                    <span class="badge <?= $pos === 1 ? 'bg-warning text-dark' : 'bg-light text-muted border' ?> rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; font-weight: 700;">
                                        <?= $pos ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($sp['nombre'], ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td>
                                    <span class="saas-badge saas-badge-neutral"><?= htmlspecialchars($sp['categoria_nombre'] ?? 'Terapia', ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td>
                                    <span class="text-muted"><i class="bi bi-clock me-1"></i><?= (int)$sp['duracion_minutos'] ?> min</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark"><?= (int)$sp['total_solicitudes'] ?></span> <span class="text-muted small">citas</span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold fs-6 text-dark">C$ <?= number_format((float)$sp['costo'], 2) ?></span>
                                </td>
                            </tr>
                            <?php $pos++; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Inicialización de Gráficos con Chart.js -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Gráfico de Barras / Línea de Ingresos
    const ctxIngresos = document.getElementById('chartIngresos');
    if (ctxIngresos) {
        new Chart(ctxIngresos, {
            type: 'line',
            data: {
                labels: <?= json_encode($labelsDias) ?>,
                datasets: [{
                    label: 'Recaudación ($)',
                    data: <?= json_encode($totalesDias) ?>,
                    borderColor: '#1e3d34',
                    backgroundColor: 'rgba(30, 61, 52, 0.1)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#c5a059',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return 'C$' + value; }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // 2. Gráfico de Dona: Distribución por Categorías
    const ctxCat = document.getElementById('chartCategorias');
    if (ctxCat) {
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($labelsCat) ?>,
                datasets: [{
                    data: <?= json_encode($totalesCat) ?>,
                    backgroundColor: [
                        '#1e3d34',
                        '#c5a059',
                        '#3b735c',
                        '#8fa89e',
                        '#d4b26f',
                        '#e0e7e4'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: { size: 11 }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }
});
</script>
