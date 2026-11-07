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

<!-- Banda de Bienvenida con Métricas Integradas -->
<section class="hero-band">
    <div class="hero-top">
        <div>
            <span class="kicker hero-kicker"><?= htmlspecialchars($fechaLegible, ENT_QUOTES, 'UTF-8') ?></span>
            <h1 class="hero-title"><?= htmlspecialchars($saludo, ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($nombreSaludo, ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="hero-sub">La agenda del día, los cobros y el equipo en una sola vista operativa.</p>
        </div>
        <div class="hero-actions">
            <a href="index.php?c=citas&a=index" class="btn btn-gold btn-sm px-3 d-inline-flex align-items-center gap-2">
                <i class="bi bi-calendar-plus"></i>
                <span>Nueva Cita</span>
            </a>
            <a href="index.php?c=pagos&a=index" class="btn btn-outline-light btn-sm px-3 d-inline-flex align-items-center gap-2">
                <i class="bi bi-receipt"></i>
                <span>Caja y Cobro</span>
            </a>
        </div>
    </div>

    <div class="hero-ledger">
        <div class="metric-h">
            <span class="metric-h-label">Citas de Hoy</span>
            <span class="metric-h-value"><?= (int)$metricas['citas_hoy'] ?></span>
            <span class="metric-h-note">
                <?= (int)$metricas['citas_confirmadas'] ?> confirmadas<?php if ((int)$metricas['citas_pendientes'] > 0): ?> · <?= (int)$metricas['citas_pendientes'] ?> pendientes<?php endif; ?>
            </span>
        </div>
        <div class="metric-h">
            <span class="metric-h-label">Terapeutas en Turno</span>
            <span class="metric-h-value"><?= (int)$metricas['terapeutas_activos'] ?></span>
            <span class="metric-h-note">Personal activo</span>
        </div>
        <div class="metric-h">
            <span class="metric-h-label">Clientes Registrados</span>
            <span class="metric-h-value"><?= (int)$metricas['clientes_registrados'] ?></span>
            <span class="metric-h-note">Base fidelizada</span>
        </div>
        <div class="metric-h">
            <span class="metric-h-label">Ingresos de Hoy</span>
            <span class="metric-h-value">C$ <?= number_format((float)$metricas['ingresos_hoy'], 2) ?></span>
            <span class="metric-h-note"><?= (int)$metricas['transacciones_hoy'] ?> cobros liquidados</span>
        </div>
    </div>
</section>

<!-- Agenda del Día (Elemento Protagonista) + Columna Lateral -->
<div class="row g-4 mb-4">
    <!-- Agenda Timeline del Día -->
    <div class="col-lg-8">
        <div class="panel panel--feature h-100">
            <div class="panel-head">
                <h2 class="panel-title">Agenda del día</h2>
                <a href="index.php?c=citas&a=index" class="panel-link">Ver agenda completa <i class="bi bi-arrow-right"></i></a>
            </div>

            <?php if (empty($citasHoy)): ?>
                <p class="text-muted mb-0 py-3 text-center"><i class="bi bi-calendar-x me-1"></i>No hay citas programadas para el día de hoy.</p>
            <?php else: ?>
                <div class="agenda-list">
                    <?php foreach ($citasHoy as $cita): ?>
                        <?php
                        $estadoCita = $cita['estado'] ?? '';
                        $estadoSlug = match ($estadoCita) {
                            'Confirmada' => 'confirmada',
                            'Pendiente'  => 'pendiente',
                            'Completada' => 'completada',
                            'Cancelada'  => 'cancelada',
                            default      => 'pendiente',
                        };
                        $horaCita = '';
                        if (!empty($cita['hora_inicio'])) {
                            $ts = strtotime($cita['hora_inicio']);
                            $horaCita = $ts ? date('g:i', $ts) : $cita['hora_inicio'];
                        }
                        $meridiano = '';
                        if (!empty($cita['hora_inicio'])) {
                            $ts = strtotime($cita['hora_inicio']);
                            $meridiano = $ts ? date('A', $ts) : '';
                        }
                        ?>
                        <article class="agenda-row">
                            <div class="agenda-hour">
                                <?= htmlspecialchars($horaCita, ENT_QUOTES, 'UTF-8') ?>
                                <small><?= htmlspecialchars($meridiano, ENT_QUOTES, 'UTF-8') ?></small>
                            </div>
                            <div class="agenda-card" data-estado="<?= htmlspecialchars($estadoCita, ENT_QUOTES, 'UTF-8') ?>">
                                <div class="agenda-name"><?= htmlspecialchars($cita['cliente_nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="agenda-meta">
                                    <?= htmlspecialchars($cita['servicio_nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?> · Con <?= htmlspecialchars($cita['terapeuta_nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    <?php if (!empty($cita['cliente_telefono'])): ?>
                                        · <i class="bi bi-telephone"></i> <?= htmlspecialchars($cita['cliente_telefono'], ENT_QUOTES, 'UTF-8') ?>
                                    <?php endif; ?>
                                </div>
                                <div class="agenda-foot">
                                    <span class="tag tag--<?= htmlspecialchars($estadoSlug, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($estadoCita, ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="agenda-price">C$<?= number_format((float)($cita['total'] ?? 0), 2) ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Columna Lateral: Recaudación y Accesos Rápidos -->
    <div class="col-lg-4">
        <div class="d-flex flex-column gap-4 h-100">
            <!-- Arqueo Rápido de Caja -->
            <div class="panel">
                <div class="panel-head">
                    <h2 class="panel-title">Recaudación por Medio de Pago</h2>
                </div>
                <?php if (empty($resumenPagos)): ?>
                    <p class="text-muted small mb-0">Sin transacciones registradas.</p>
                <?php else: ?>
                    <div class="rank-list">
                        <?php foreach ($resumenPagos as $p): ?>
                            <div class="rank-item">
                                <div class="rank-num"><i class="bi bi-wallet2"></i></div>
                                <div>
                                    <div class="rank-name"><?= htmlspecialchars($p['tipo_pago'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="rank-meta"><?= (int)$p['cantidad'] ?> recibo(s)</div>
                                </div>
                                <div class="rank-price">C$<?= number_format((float)$p['total'], 2) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Accesos Rápidos -->
            <div class="panel flex-grow-1">
                <div class="panel-head">
                    <h2 class="panel-title">Accesos Directos</h2>
                </div>
                <div class="quick-links">
                    <a href="index.php?c=citas&a=index" class="quick-link">
                        <div>
                            <b>Agendar Nueva Cita</b>
                            <span>Cabina, horario y terapeuta</span>
                        </div>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="index.php?c=clientes&a=index" class="quick-link">
                        <div>
                            <b>Registrar Cliente</b>
                            <span>Expediente y preferencias</span>
                        </div>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="index.php?c=pagos&a=index" class="quick-link">
                        <div>
                            <b>Cobro en Caja</b>
                            <span>Emitir recibo y conciliar</span>
                        </div>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <?php if (($_SESSION['rol_nombre'] ?? '') === 'Administrador'): ?>
                        <a href="index.php?c=reportes&a=index" class="quick-link">
                            <div>
                                <b>Reportes y Analítica</b>
                                <span>Ingresos, citas y servicios</span>
                            </div>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección Gráfica Analítica -->
<div class="row g-4 mb-4">
    <!-- Gráfico de Tendencia de Ingresos -->
    <div class="col-lg-8">
        <div class="panel h-100">
            <div class="panel-head">
                <h2 class="panel-title">Evolución de Facturación Diaria</h2>
                <span class="panel-link"><i class="bi bi-currency-dollar me-1"></i>Ingresos</span>
            </div>
            <div style="height: 250px; position: relative;">
                <canvas id="chartIngresos"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico de Distribución de Servicios / Categorías -->
    <div class="col-lg-4">
        <div class="panel h-100">
            <div class="panel-head">
                <h2 class="panel-title">Preferencias por Categoría</h2>
            </div>
            <div style="height: 250px; position: relative;">
                <canvas id="chartCategorias"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Catálogo: Servicios más Solicitados -->
<div class="row g-3">
    <div class="col-12">
        <div class="panel">
            <div class="panel-head">
                <h2 class="panel-title">Servicios &amp; Terapias más Solicitadas</h2>
                <a href="index.php?c=servicios&a=index" class="panel-link">Explorar catálogo completo <i class="bi bi-arrow-right"></i></a>
            </div>

            <?php if (empty($serviciosPopulares)): ?>
                <p class="text-muted text-center py-3 mb-0">No hay servicios registrados.</p>
            <?php else: ?>
                <div class="rank-list">
                    <?php $pos = 1; ?>
                    <?php foreach ($serviciosPopulares as $sp): ?>
                        <div class="rank-item">
                            <div class="rank-num"><?= str_pad((string)$pos, 2, '0', STR_PAD_LEFT) ?></div>
                            <div>
                                <div class="rank-name"><?= htmlspecialchars($sp['nombre'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="rank-meta">
                                    <?= htmlspecialchars($sp['categoria_nombre'] ?? 'Terapia', ENT_QUOTES, 'UTF-8') ?> · <i class="bi bi-clock"></i> <?= (int)$sp['duracion_minutos'] ?> min · <?= (int)$sp['total_solicitudes'] ?> agendada(s)
                                </div>
                            </div>
                            <div class="rank-price">
                                C$<?= number_format((float)$sp['costo'], 2) ?>
                            </div>
                        </div>
                        <?php $pos++; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
