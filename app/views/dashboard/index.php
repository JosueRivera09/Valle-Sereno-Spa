<?php
/**
 * Vista Dinámica del Dashboard Principal
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
?>

<!-- Banner de Bienvenida y Accesos Rápidos de Cabecera -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--spa-primary); font-family: var(--font-serif);">
            <i class="bi bi-brightness-alt-high text-warning me-2"></i>Panel de Control Operativo
        </h4>
        <p class="text-muted small mb-0">Visión global de agenda terapéutica, ingresos, pacientes e indicadores de rendimiento.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?c=citas&a=index" class="btn btn-spa-primary btn-sm px-3 d-inline-flex align-items-center gap-2">
            <i class="bi bi-calendar-plus"></i>
            <span>Nueva Cita</span>
        </a>
        <a href="index.php?c=pagos&a=index" class="btn btn-outline-secondary btn-sm px-3 d-inline-flex align-items-center gap-2">
            <i class="bi bi-receipt"></i>
            <span>Caja y Cobro</span>
        </a>
    </div>
</div>

<!-- Tarjetas de Estadísticas / KPIs Dinámicos -->
<div class="row g-3 mb-4">
    <!-- Citas de Hoy -->
    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 h-100 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">Citas de Hoy</small>
                <h3 class="fw-bold mb-0 text-dark" style="font-family: var(--font-serif);">
                    <?= (int)$metricas['citas_hoy'] ?>
                </h3>
                <small class="text-success" style="font-size: 0.78rem;">
                    <i class="bi bi-check2-circle"></i> <?= (int)$metricas['citas_confirmadas'] ?> confirmadas
                    <?php if ($metricas['citas_pendientes'] > 0): ?>
                        &bull; <span class="text-warning"><?= (int)$metricas['citas_pendientes'] ?> pend.</span>
                    <?php endif; ?>
                </small>
            </div>
            <div class="stat-icon" style="background: rgba(30, 61, 52, 0.1); color: var(--spa-primary);">
                <i class="bi bi-calendar2-check"></i>
            </div>
        </div>
    </div>

    <!-- Terapeutas en Turno -->
    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 h-100 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">Terapeutas en Turno</small>
                <h3 class="fw-bold mb-0 text-dark" style="font-family: var(--font-serif);">
                    <?= (int)$metricas['terapeutas_activos'] ?>
                </h3>
                <small class="text-primary" style="font-size: 0.78rem;">
                    <i class="bi bi-person-check"></i> Personal activo
                </small>
            </div>
            <div class="stat-icon" style="background: rgba(197, 160, 89, 0.15); color: #997838;">
                <i class="bi bi-person-badge"></i>
            </div>
        </div>
    </div>

    <!-- Clientes Registrados -->
    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 h-100 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">Clientes Registrados</small>
                <h3 class="fw-bold mb-0 text-dark" style="font-family: var(--font-serif);">
                    <?= (int)$metricas['clientes_registrados'] ?>
                </h3>
                <small class="text-success" style="font-size: 0.78rem;">
                    <i class="bi bi-people"></i> Base fidelizada
                </small>
            </div>
            <div class="stat-icon" style="background: #eef6f3; color: #3b735c;">
                <i class="bi bi-heart-pulse"></i>
            </div>
        </div>
    </div>

    <!-- Ingresos del Día -->
    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 h-100 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">Ingresos de Hoy</small>
                <h3 class="fw-bold mb-0 text-dark" style="font-family: var(--font-serif);">
                    $ <?= number_format($metricas['ingresos_hoy'], 2) ?>
                </h3>
                <small class="text-muted" style="font-size: 0.78rem;">
                    <i class="bi bi-wallet2"></i> <?= (int)$metricas['transacciones_hoy'] ?> cobros liquidados
                </small>
            </div>
            <div class="stat-icon" style="background: #fdf5ea; color: #c5a059;">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
    </div>
</div>

<!-- Sección Gráfica Analítica (Visuales Nuevos) -->
<div class="row g-4 mb-4">
    <!-- Gráfico de Tendencia de Ingresos -->
    <div class="col-lg-8">
        <div class="spa-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0" style="color: var(--spa-primary); font-family: var(--font-serif);">
                        <i class="bi bi-graph-up text-warning me-2"></i> Evolución de Facturación Diaria
                    </h5>
                    <small class="text-muted">Total de recaudación por servicios en los últimos días</small>
                </div>
                <span class="badge bg-light text-dark border">
                    <i class="bi bi-currency-dollar text-success"></i> Ingresos
                </span>
            </div>
            <div style="height: 250px; position: relative;">
                <canvas id="chartIngresos"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico de Distribución de Servicios / Categorías -->
    <div class="col-lg-4">
        <div class="spa-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" style="color: var(--spa-primary); font-family: var(--font-serif);">
                    <i class="bi bi-pie-chart text-warning me-2"></i> Preferencias
                </h5>
                <small class="text-muted">Por Categoría</small>
            </div>
            <div style="height: 250px; position: relative;">
                <canvas id="chartCategorias"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Secciones Operativas: Tabla de Citas de Hoy y Métodos de Pago -->
<div class="row g-4 mb-4">
    <!-- Citas del Día (Alimentadas de la BD) -->
    <div class="col-lg-8">
        <div class="spa-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0" style="color: var(--spa-primary); font-family: var(--font-serif);">
                        <i class="bi bi-clock-history text-warning me-2"></i> Agenda & Citas Programadas para Hoy
                    </h5>
                    <small class="text-muted">Seguimiento en cabinas y tiempos de atención</small>
                </div>
                <a href="index.php?c=citas&a=index" class="btn btn-sm btn-link text-decoration-none fw-semibold" style="color: var(--spa-primary);">
                    Ver agenda completa <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #556b61;">
                            <th>HORA</th>
                            <th>CLIENTE</th>
                            <th>TERAPIA / TRATAMIENTO</th>
                            <th>TERAPEUTA</th>
                            <th>MONTO</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.88rem;">
                        <?php if (empty($citasHoy)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-calendar-x fs-3 d-block mb-1 text-muted"></i>
                                    No hay citas programadas para el día de hoy.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($citasHoy as $cita): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark border fw-medium">
                                            <?= date('h:i A', strtotime($cita['hora_inicio'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($cita['cliente_nombre']) ?></div>
                                        <small class="text-muted" style="font-size: 0.74rem;">
                                            <i class="bi bi-telephone"></i> <?= htmlspecialchars($cita['cliente_telefono'] ?? '') ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-medium"><?= htmlspecialchars($cita['servicio_nombre']) ?></span>
                                    </td>
                                    <td>
                                        <small class="text-secondary fw-semibold">
                                            <i class="bi bi-person"></i> <?= htmlspecialchars($cita['terapeuta_nombre']) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark" style="font-family: var(--font-serif);">$<?= number_format($cita['total'], 2) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                            $estadoClass = match($cita['estado']) {
                                                'Confirmada' => 'bg-success-subtle text-success border border-success-subtle',
                                                'Pendiente'  => 'bg-warning-subtle text-warning border border-warning-subtle',
                                                'Completada' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                                'Cancelada'  => 'bg-danger-subtle text-danger border border-danger-subtle',
                                                default      => 'bg-secondary-subtle text-secondary'
                                            };
                                        ?>
                                        <span class="badge <?= $estadoClass ?> rounded-pill px-2 py-1">
                                            <?= htmlspecialchars($cita['estado']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Columna Lateral: Resumen de Medios de Pago y Accesos Rápidos -->
    <div class="col-lg-4">
        <div class="d-flex flex-column gap-4 h-100">
            <!-- Arqueo Rápido de Caja -->
            <div class="spa-card p-4">
                <h5 class="fw-bold mb-3" style="color: var(--spa-primary); font-family: var(--font-serif);">
                    <i class="bi bi-cash-stack text-warning me-2"></i> Recaudación por Medio de Pago
                </h5>
                <?php if (empty($resumenPagos)): ?>
                    <p class="text-muted small mb-0">Sin transacciones registradas.</p>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($resumenPagos as $p): ?>
                            <?php 
                                $icono = match($p['tipo_pago']) {
                                    'Efectivo' => 'bi-cash-coin text-success',
                                    'Tarjeta' => 'bi-credit-card text-primary',
                                    'Transferencia' => 'bi-arrow-left-right text-warning',
                                    default => 'bi-wallet text-secondary'
                                };
                            ?>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-2 bg-light border">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi <?= $icono ?> fs-5"></i>
                                    <div>
                                        <div class="fw-semibold small text-dark"><?= htmlspecialchars($p['tipo_pago']) ?></div>
                                        <small class="text-muted" style="font-size: 0.72rem;"><?= (int)$p['cantidad'] ?> recibo(s)</small>
                                    </div>
                                </div>
                                <div class="fw-bold text-dark" style="font-family: var(--font-serif);">
                                    $<?= number_format($p['total'], 2) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Accesos Rápidos -->
            <div class="spa-card p-4 flex-grow-1">
                <h5 class="fw-bold mb-3" style="color: var(--spa-primary); font-family: var(--font-serif);">
                    <i class="bi bi-compass text-warning me-2"></i> Accesos Directos
                </h5>
                <div class="d-flex flex-column gap-2">
                    <a href="index.php?c=citas&a=index" class="btn btn-outline-secondary text-start d-flex align-items-center justify-content-between p-2 rounded-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-calendar-plus text-success fs-5"></i>
                            <div>
                                <div class="fw-bold text-dark small">Agendar Nueva Cita</div>
                                <div class="text-muted" style="font-size:0.72rem;">Cabina, horario y terapeuta</div>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>

                    <a href="index.php?c=clientes&a=index" class="btn btn-outline-secondary text-start d-flex align-items-center justify-content-between p-2 rounded-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-person-plus text-primary fs-5"></i>
                            <div>
                                <div class="fw-bold text-dark small">Registrar Cliente</div>
                                <div class="text-muted" style="font-size:0.72rem;">Expediente y preferencias</div>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>

                    <a href="index.php?c=pagos&a=index" class="btn btn-outline-secondary text-start d-flex align-items-center justify-content-between p-2 rounded-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-receipt text-warning fs-5"></i>
                            <div>
                                <div class="fw-bold text-dark small">Cobro en Caja</div>
                                <div class="text-muted" style="font-size:0.72rem;">Emitir recibo y conciliar</div>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fila Inferior: Catálogo de Servicios Populares / Destacados -->
<div class="row g-3">
    <div class="col-12">
        <div class="spa-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0" style="color: var(--spa-primary); font-family: var(--font-serif);">
                        <i class="bi bi-stars text-warning me-2"></i> Servicios & Terapias más Solicitadas
                    </h5>
                    <small class="text-muted">Tratamientos preferidos por los clientes en Valle Sereno</small>
                </div>
                <a href="index.php?c=servicios&a=index" class="btn btn-sm btn-link text-decoration-none fw-semibold" style="color: var(--spa-primary);">
                    Explorar catálogo completo <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="row g-3">
                <?php if (empty($serviciosPopulares)): ?>
                    <div class="col-12 text-center text-muted py-3">No hay servicios registrados.</div>
                <?php else: ?>
                    <?php foreach ($serviciosPopulares as $sp): ?>
                        <div class="col-md-6 col-xl-4">
                            <div class="p-3 rounded-3 border bg-white h-100 d-flex flex-column justify-content-between shadow-sm">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <span class="badge bg-secondary-subtle text-dark border small" style="font-size: 0.7rem;">
                                            <?= htmlspecialchars($sp['categoria_nombre'] ?? 'Terapia') ?>
                                        </span>
                                        <span class="badge bg-success-subtle text-success small">
                                            <i class="bi bi-clock"></i> <?= (int)$sp['duracion_minutos'] ?> min
                                        </span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1 mt-2"><?= htmlspecialchars($sp['nombre']) ?></h6>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                    <small class="text-muted">
                                        <i class="bi bi-bookmark-check text-success"></i> <?= (int)$sp['total_solicitudes'] ?> agendada(s)
                                    </small>
                                    <span class="fw-bold text-dark fs-6" style="font-family: var(--font-serif); color: var(--spa-primary);">
                                        $<?= number_format($sp['costo'], 2) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
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
                            callback: function(value) { return '$' + value; }
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
