<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--spa-primary); font-family: var(--font-serif);">
            <i class="bi bi-graph-up-arrow text-warning me-2"></i> Reportes Operativos & Métricas de Rendimiento
        </h4>
        <p class="text-muted small mb-0">Análisis financiero de recaudación, productividad del equipo terapéutico y consultas por rango de fechas.</p>
    </div>
</div>

<!-- Filtro Personalizado por Rango de Fechas -->
<div class="spa-card p-4 mb-4">
    <h5 class="fw-bold mb-3" style="color: var(--spa-primary); font-family: var(--font-serif);">
        <i class="bi bi-funnel text-warning me-2"></i> Filtrar Transacciones por Periodo
    </h5>
    <form id="formFiltroReporte" class="row g-3 align-items-end" onsubmit="filtrarReportes(event)">
        <div class="col-sm-5 col-md-4">
            <label class="form-label fw-bold small text-dark">Fecha Inicial <span class="text-danger">*</span></label>
            <input type="date" id="fecha_inicio" class="form-control" value="<?= date('Y-m-01') ?>" required>
        </div>
        <div class="col-sm-5 col-md-4">
            <label class="form-label fw-bold small text-dark">Fecha Final <span class="text-danger">*</span></label>
            <input type="date" id="fecha_fin" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="col-sm-2 col-md-4">
            <button type="submit" class="btn btn-spa-primary btn-sm px-4 py-2 w-100 d-inline-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-search"></i>
                <span>Generar Informe</span>
            </button>
        </div>
    </form>
</div>

<div class="row g-4 mb-4">
    <!-- Tabla de Ventas Mensuales -->
    <div class="col-lg-7">
        <div class="spa-card p-4 h-100">
            <h5 class="fw-bold mb-3" style="color: var(--spa-primary); font-family: var(--font-serif);">
                <i class="bi bi-calendar2-range text-warning me-2"></i> Recaudación Consolidada por Mes
            </h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr style="font-size: 0.78rem; text-transform: uppercase;">
                            <th>MES Y AÑO</th>
                            <th>TRANSACCIONES LIQUIDADAS</th>
                            <th class="text-end">TOTAL RECAUDADO</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.88rem;">
                        <?php if (empty($ventasMes)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No se registran datos acumulados de ventas.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ventasMes as $v): ?>
                                <tr>
                                    <td class="fw-bold text-dark">
                                        <i class="bi bi-calendar-event me-2 text-warning"></i><?= date('F Y', strtotime($v['mes'] . '-01')) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1">
                                            <?= (int)$v['total_transacciones'] ?> cobros
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold fs-6" style="font-family: var(--font-serif); color: var(--spa-primary);">
                                        C$<?= number_format($v['total_ventas'], 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Ranking de Productividad Terapéutica -->
    <div class="col-lg-5">
        <div class="spa-card p-4 h-100">
            <h5 class="fw-bold mb-3" style="color: var(--spa-primary); font-family: var(--font-serif);">
                <i class="bi bi-trophy text-warning me-2"></i> Top Terapeutas por Atenciones
            </h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr style="font-size: 0.78rem; text-transform: uppercase;">
                            <th>TERAPEUTA</th>
                            <th>CITAS</th>
                            <th class="text-end">RECAUDACIÓN</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.88rem;">
                        <?php if (empty($rankingTerapeutas)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Sin datos de atenciones asignadas.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rankingTerapeutas as $index => $t): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            <span class="badge rounded-circle <?= $index === 0 ? 'bg-warning text-dark' : 'bg-secondary' ?> me-1"><?= $index + 1 ?></span>
                                            <?= htmlspecialchars($t['terapeuta']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <?= (int)$t['total_citas'] ?> ses.
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-dark" style="font-family: var(--font-serif);">
                                        C$<?= number_format($t['total_generado'], 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Resultado Filtrado & Exportación Fiscal -->
<div class="modal fade" id="modalResultadoFiltro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: var(--spa-primary);">
                <div>
                    <h5 class="modal-title fw-bold mb-0" style="font-family: var(--font-serif);">
                        <i class="bi bi-file-earmark-spreadsheet text-warning me-2"></i> Informe Consolidado Contable & Fiscal
                    </h5>
                    <small class="text-white-50" style="font-size: 0.78rem;">Valle Sereno Spa S.A. • RUC J031000029384</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                
                <!-- Tarjetas resumen de métricas contables -->
                <div class="row g-3 mb-4" id="contenedorResumenContable" style="display: none;">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <small class="text-muted fw-bold d-block text-uppercase" style="font-size: 0.72rem;">Total Recaudado (C$)</small>
                            <span class="fs-4 fw-bold text-success" id="lblTotalRecaudado">C$0.00</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <small class="text-muted fw-bold d-block text-uppercase" style="font-size: 0.72rem;">Total en Efectivo</small>
                            <span class="fs-5 fw-bold text-dark" id="lblTotalEfectivo">C$0.00</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <small class="text-muted fw-bold d-block text-uppercase" style="font-size: 0.72rem;">Total Tarjeta (POS)</small>
                            <span class="fs-5 fw-bold text-dark" id="lblTotalTarjeta">C$0.00</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <small class="text-muted fw-bold d-block text-uppercase" style="font-size: 0.72rem;">Total Transferencia</small>
                            <span class="fs-5 fw-bold text-dark" id="lblTotalTransferencia">C$0.00</span>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr style="font-size: 0.78rem; text-transform: uppercase; color: #556b61;">
                                <th>FECHA Y HORA</th>
                                <th>CLIENTE / PACIENTE</th>
                                <th>TERAPEUTA</th>
                                <th>MÉTODO PAGO</th>
                                <th class="text-end">MONTO (C$)</th>
                            </tr>
                        </thead>
                        <tbody id="bodyFiltroReporte" style="font-size: 0.88rem;">
                            <!-- Dinámico JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light d-flex justify-content-between">
                <div class="small text-muted">
                    <i class="bi bi-shield-check text-success me-1"></i> Formato oficial apto para contabilidad y auditorías tributarias.
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-success btn-sm px-3" onclick="descargarExcel()">
                        <i class="bi bi-file-earmark-excel me-1"></i> Exportar a Excel (.CSV)
                    </button>
                    <button type="button" class="btn btn-spa-primary btn-sm px-3" onclick="descargarPdf()">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Descargar Documento PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let modalResultadoBs;

document.addEventListener('DOMContentLoaded', () => {
    modalResultadoBs = new bootstrap.Modal(document.getElementById('modalResultadoFiltro'));
});

function filtrarReportes(e) {
    e.preventDefault();
    const inicio = document.getElementById('fecha_inicio').value;
    const fin = document.getElementById('fecha_fin').value;
    const body = document.getElementById('bodyFiltroReporte');
    const containerResumen = document.getElementById('contenedorResumenContable');

    containerResumen.style.display = 'none';
    body.innerHTML = '<tr><td colspan="5" class="text-center py-4"><div class="spinner-border text-success spinner-border-sm me-2"></div> Generando informe contable y tributario...</td></tr>';
    modalResultadoBs.show();

    fetch(`index.php?c=reportes&a=filtrar&inicio=${inicio}&fin=${fin}`)
    .then(r => r.json())
    .then(res => {
        if (res.success && res.data.length > 0) {
            const resumen = res.resumen_contable || {};
            document.getElementById('lblTotalRecaudado').innerText = `C$${parseFloat(resumen.total_recaudado || 0).toFixed(2)}`;
            document.getElementById('lblTotalEfectivo').innerText = `C$${parseFloat(resumen.total_efectivo || 0).toFixed(2)}`;
            document.getElementById('lblTotalTarjeta').innerText = `C$${parseFloat(resumen.total_tarjeta || 0).toFixed(2)}`;
            document.getElementById('lblTotalTransferencia').innerText = `C$${parseFloat(resumen.total_transferencia || 0).toFixed(2)}`;
            containerResumen.style.display = 'flex';

            body.innerHTML = res.data.map(item => `
                <tr>
                    <td><span class="fw-semibold text-dark">${item.fecha_pago}</span></td>
                    <td><div class="fw-bold text-dark">${item.cliente_nombre}</div></td>
                    <td><small class="text-muted"><i class="bi bi-person me-1"></i>${item.terapeuta_nombre}</small></td>
                    <td><span class="badge bg-light text-dark border">${item.tipo_pago}</span></td>
                    <td class="text-end fw-bold text-success fs-6" style="font-family: var(--font-serif);">C$${parseFloat(item.monto).toFixed(2)}</td>
                </tr>
            `).join('');
        } else {
            body.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No existen transacciones liquidadas registradas en el periodo seleccionado.</td></tr>';
        }
    });
}

function descargarExcel() {
    const inicio = document.getElementById('fecha_inicio').value;
    const fin = document.getElementById('fecha_fin').value;
    window.location.href = `index.php?c=reportes&a=exportarExcel&inicio=${inicio}&fin=${fin}`;
}

function descargarPdf() {
    const inicio = document.getElementById('fecha_inicio').value;
    const fin = document.getElementById('fecha_fin').value;
    window.open(`index.php?c=reportes&a=exportarPdf&inicio=${inicio}&fin=${fin}`, '_blank');
}
</script>
