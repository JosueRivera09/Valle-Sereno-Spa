<!-- SaaS Page Header -->
<div class="saas-page-header">
    <div>
        <div class="saas-breadcrumb">
            <i class="bi bi-bar-chart-line-fill"></i>
            <span>Analítica Financiera</span>
            <span class="sep">/</span>
            <span>Reportes &amp; Rendimiento</span>
        </div>
        <h1 class="saas-title">Reportes &amp; Métricas Contables</h1>
        <p class="saas-subtitle">Informes consolidados de facturación, recaudación por periodo fiscal y ranking de productividad terapéutica.</p>
    </div>
</div>

<!-- SaaS Filter Card por Rango de Fechas -->
<div class="saas-card mb-3">
    <div class="py-2 px-3">
        <form id="formFiltroReporte" class="row g-2 align-items-end" onsubmit="filtrarReportes(event)">
            <div class="col-sm-5 col-md-4">
                <label class="form-label fw-semibold small text-dark mb-1">
                    <i class="bi bi-calendar-event me-1 text-primary"></i>Fecha Inicial <span class="text-danger">*</span>
                </label>
                <input type="date" id="fecha_inicio" class="form-control form-control-sm" value="<?= date('Y-m-01') ?>" required>
            </div>
            <div class="col-sm-5 col-md-4">
                <label class="form-label fw-semibold small text-dark mb-1">
                    <i class="bi bi-calendar-check me-1 text-primary"></i>Fecha Final <span class="text-danger">*</span>
                </label>
                <input type="date" id="fecha_fin" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-sm-2 col-md-4">
                <button type="submit" class="saas-btn saas-btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-funnel-fill"></i>
                    <span>Generar Informe Fiscal</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <!-- Tabla de Ventas Mensuales -->
    <div class="col-lg-7">
        <div class="saas-card h-100">
            <div class="saas-card-header">
                <div>
                    <h5 class="saas-card-title">Recaudación Consolidada por Mes</h5>
                    <p class="saas-card-subtitle">Histórico mensual de liquidaciones y volumen de atenciones</p>
                </div>
                <span class="saas-badge saas-badge-gold">
                    <i class="bi bi-calendar2-range me-1"></i>Mensual
                </span>
            </div>
            <div class="table-responsive">
                <table class="saas-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Mes y Año</th>
                            <th>Transacciones Liquidadas</th>
                            <th class="text-end">Total Recaudado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ventasMes)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No se registran datos acumulados de ventas.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ventasMes as $v): ?>
                                <tr>
                                    <td class="fw-bold text-dark">
                                        <i class="bi bi-calendar-event me-2 text-primary"></i><?= date('F Y', strtotime($v['mes'] . '-01')) ?>
                                    </td>
                                    <td>
                                        <span class="saas-badge saas-badge-neutral">
                                            <?= (int)$v['total_transacciones'] ?> cobros
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-dark font-monospace" style="font-size: 0.95rem;">
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
        <div class="saas-card h-100">
            <div class="saas-card-header">
                <div>
                    <h5 class="saas-card-title">Top Terapeutas por Atenciones</h5>
                    <p class="saas-card-subtitle">Rendimiento por sesiones completadas y monto</p>
                </div>
                <span class="saas-badge saas-badge-success">
                    <i class="bi bi-trophy me-1"></i>Líderes
                </span>
            </div>
            <div class="table-responsive">
                <table class="saas-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Terapeuta</th>
                            <th>Citas</th>
                            <th class="text-end">Recaudación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rankingTerapeutas)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Sin datos de atenciones asignadas.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rankingTerapeutas as $index => $t): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge rounded-circle <?= $index === 0 ? 'bg-warning text-dark' : 'bg-light text-muted border' ?>" style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700;">
                                                <?= $index + 1 ?>
                                            </span>
                                            <span class="fw-semibold text-dark"><?= htmlspecialchars($t['terapeuta']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="saas-badge saas-badge-success">
                                            <?= (int)$t['total_citas'] ?> ses.
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold font-monospace text-dark" style="font-size: 0.9rem;">
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
<div class="modal fade" id="modalResultadoFiltro" tabindex="-1" aria-labelledby="modalResultadoFiltroTitulo" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-editorial">
            <div class="modal-header">
                <div>
                    <span class="modal-kicker">Reportes &amp; Fiscal</span>
                    <h5 class="modal-title" id="modalResultadoFiltroTitulo">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Informe Consolidado Contable &amp; Fiscal
                    </h5>
                    <p class="modal-sub">Valle Sereno Spa S.A. • RUC J031000029384</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <!-- Tarjetas resumen de métricas contables -->
                <div class="row g-3 mb-4" id="contenedorResumenContable" style="display: none;">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <small class="text-muted fw-bold d-block text-uppercase" style="font-size: 0.72rem;">Total Recaudado (C$)</small>
                            <span class="fs-4 fw-bold text-success font-monospace" id="lblTotalRecaudado">C$0.00</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <small class="text-muted fw-bold d-block text-uppercase" style="font-size: 0.72rem;">Total en Efectivo</small>
                            <span class="fs-5 fw-bold text-dark font-monospace" id="lblTotalEfectivo">C$0.00</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <small class="text-muted fw-bold d-block text-uppercase" style="font-size: 0.72rem;">Total Tarjeta (POS)</small>
                            <span class="fs-5 fw-bold text-dark font-monospace" id="lblTotalTarjeta">C$0.00</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <small class="text-muted fw-bold d-block text-uppercase" style="font-size: 0.72rem;">Total Transferencia</small>
                            <span class="fs-5 fw-bold text-dark font-monospace" id="lblTotalTransferencia">C$0.00</span>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="saas-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Fecha y Hora</th>
                                <th>Cliente / Paciente</th>
                                <th>Terapeuta</th>
                                <th>Método Pago</th>
                                <th class="text-end">Monto (C$)</th>
                            </tr>
                        </thead>
                        <tbody id="bodyFiltroReporte">
                            <!-- Dinámico JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div class="small text-muted">
                    <i class="bi bi-shield-check text-success me-1"></i> Formato oficial apto para contabilidad y auditorías tributarias.
                </div>
                <div class="d-flex flex-wrap justify-content-end gap-2">
                    <button type="button" class="btn btn-quiet btn-sm px-3" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                    <button type="button" class="saas-btn saas-btn-outline px-3" onclick="descargarExcel()">
                        <i class="bi bi-file-earmark-excel text-success me-1"></i> Exportar a Excel
                    </button>
                    <button type="button" class="saas-btn saas-btn-outline px-3" onclick="imprimirPdf()">
                        <i class="bi bi-printer me-1"></i> Imprimir
                    </button>
                    <button type="button" class="saas-btn saas-btn-primary px-3" onclick="descargarPdf()">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Descargar PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let modalResultadoBs;
let reporteActual = null;

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
        reporteActual = res.success ? { ...res, inicio, fin } : null;

        if (res.success && res.data.length > 0) {
            const resumen = res.resumen_contable || {};
            document.getElementById('lblTotalRecaudado').innerText = `C$${parseFloat(resumen.total_recaudado || 0).toFixed(2)}`;
            document.getElementById('lblTotalEfectivo').innerText = `C$${parseFloat(resumen.total_efectivo || 0).toFixed(2)}`;
            document.getElementById('lblTotalTarjeta').innerText = `C$${parseFloat(resumen.total_tarjeta || 0).toFixed(2)}`;
            document.getElementById('lblTotalTransferencia').innerText = `C$${parseFloat(resumen.total_transferencia || 0).toFixed(2)}`;
            containerResumen.style.display = 'flex';

            body.innerHTML = res.data.map(item => `
                <tr>
                    <td><span class="fw-semibold text-dark">${esc(item.fecha_pago)}</span></td>
                    <td><div class="fw-bold text-dark">${esc(item.cliente_nombre)}</div></td>
                    <td><small class="text-muted"><i class="bi bi-person me-1"></i>${esc(item.terapeuta_nombre)}</small></td>
                    <td><span class="saas-badge saas-badge-neutral">${esc(item.tipo_pago)}</span></td>
                    <td class="text-end fw-bold text-success font-monospace fs-6">C$${parseFloat(item.monto).toFixed(2)}</td>
                </tr>
            `).join('');
        } else {
            body.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No existen transacciones liquidadas registradas en el periodo seleccionado.</td></tr>';
        }
    })
    .catch(() => {
        reporteActual = null;
        body.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-danger">No se pudo generar el informe. Intente de nuevo.</td></tr>';
    });
}

function descargarExcel() {
    const inicio = document.getElementById('fecha_inicio').value;
    const fin = document.getElementById('fecha_fin').value;
    window.location.href = `index.php?c=reportes&a=exportarExcel&inicio=${inicio}&fin=${fin}`;
}

function formatearFechaHora(valor) {
    if (!valor) return '';
    const fecha = new Date(String(valor).replace(' ', 'T'));
    if (isNaN(fecha.getTime())) return valor;
    const dd = String(fecha.getDate()).padStart(2, '0');
    const mm = String(fecha.getMonth() + 1).padStart(2, '0');
    const yyyy = fecha.getFullYear();
    const hh = String(fecha.getHours()).padStart(2, '0');
    const mi = String(fecha.getMinutes()).padStart(2, '0');
    return `${dd}/${mm}/${yyyy} ${hh}:${mi}`;
}

function formatearMonto(valor) {
    const numero = parseFloat(valor) || 0;
    return 'C$' + numero.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function construirDocumentoPdf() {
    if (!reporteActual || !reporteActual.success) {
        alert('Primero genera el informe para poder exportarlo.');
        return null;
    }

    if (!window.jspdf || !window.jspdf.jsPDF) {
        alert('No se pudo cargar la librería de PDF. Verifica tu conexión e intenta de nuevo.');
        return null;
    }

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });

    const pageW = doc.internal.pageSize.getWidth();
    const pageH = doc.internal.pageSize.getHeight();
    const margen = 14;
    const verde = [27, 59, 43];
    const gris = [100, 116, 139];
    const borde = [226, 232, 240];

    const inicio = reporteActual.inicio;
    const fin = reporteActual.fin;
    const resumen = reporteActual.resumen_contable || {};
    const datos = Array.isArray(reporteActual.data) ? reporteActual.data : [];

    const fechaInicio = new Date(inicio + 'T00:00:00');
    const fechaFin = new Date(fin + 'T00:00:00');
    const periodo = `Del ${String(fechaInicio.getDate()).padStart(2, '0')}/${String(fechaInicio.getMonth() + 1).padStart(2, '0')}/${fechaInicio.getFullYear()} al ${String(fechaFin.getDate()).padStart(2, '0')}/${String(fechaFin.getMonth() + 1).padStart(2, '0')}/${fechaFin.getFullYear()}`;

    // ---------------------------------------------------------------
    // Encabezado corporativo
    // ---------------------------------------------------------------
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(16);
    doc.setTextColor(...verde);
    doc.text('VALLE SERENO SPA S.A.', margen, 18);

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(8);
    doc.setTextColor(46, 125, 50);
    doc.text('Centro de Relajación, Salud Terapéutica & Bienestar', margen, 24);

    doc.setFont('helvetica', 'normal');
    doc.setFontSize(7.5);
    doc.setTextColor(...gris);
    doc.text(`RUC: ${resumen.ruc_empresa || 'J031000029384'}`, margen, 30);
    doc.text('Dirección: Rivas, Nicaragua', margen, 34.5);
    doc.text('Teléfono: +505 2255-8899 | Correo: contabilidad@vallesereno.com', margen, 39);

    // Recuadro de datos fiscales
    const boxX = pageW - margen - 72;
    doc.setDrawColor(...borde);
    doc.setFillColor(248, 250, 249);
    doc.roundedRect(boxX, 13, 72, 30, 1.5, 1.5, 'FD');

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(6.8);
    doc.setTextColor(44, 62, 53);
    doc.text('DATOS DEL INFORME FISCAL', boxX + 4, 19);

    doc.setFont('helvetica', 'normal');
    doc.setFontSize(7);
    doc.setTextColor(...gris);
    doc.text(`Periodo: ${periodo}`, boxX + 4, 25);
    doc.text(`Fecha emisión: ${new Date().toLocaleDateString('es-NI')} ${new Date().toLocaleTimeString('es-NI', { hour: '2-digit', minute: '2-digit' })}`, boxX + 4, 30);
    doc.text('Moneda: Córdobas (C$ NIO)', boxX + 4, 35);
    doc.text('Estado: Liquidado & Verificado', boxX + 4, 40);

    doc.setDrawColor(...verde);
    doc.setLineWidth(0.4);
    doc.line(margen, 46, pageW - margen, 46);

    // Título del reporte
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10.5);
    doc.setTextColor(...verde);
    doc.text('REPORTE CONTABLE Y LIBRO AUXILIAR DE RECAUDACIÓN', pageW / 2, 54, { align: 'center' });

    doc.setFont('helvetica', 'normal');
    doc.setFontSize(7.5);
    doc.setTextColor(...gris);
    doc.text('Consolidado de ingresos por prestación de servicios y tratamientos de Spa', pageW / 2, 59, { align: 'center' });

    // ---------------------------------------------------------------
    // Resumen contable (KPIs)
    // ---------------------------------------------------------------
    doc.autoTable({
        startY: 64,
        head: [['TOTAL RECAUDADO', 'EN EFECTIVO', 'EN TARJETA (POS)', 'EN TRANSFERENCIA']],
        body: [[
            formatearMonto(resumen.total_recaudado),
            formatearMonto(resumen.total_efectivo),
            formatearMonto(resumen.total_tarjeta),
            formatearMonto(resumen.total_transferencia)
        ]],
        theme: 'grid',
        margin: { left: margen, right: margen },
        styles: {
            font: 'helvetica',
            cellPadding: 2.5,
            lineColor: borde,
            lineWidth: 0.15
        },
        headStyles: {
            fillColor: [248, 250, 249],
            textColor: gris,
            fontStyle: 'bold',
            fontSize: 6.5,
            halign: 'center'
        },
        bodyStyles: {
            fontSize: 9,
            fontStyle: 'bold',
            textColor: verde,
            halign: 'center'
        }
    });

    // ---------------------------------------------------------------
    // Detalle de transacciones
    // ---------------------------------------------------------------
    const filas = datos.map(item => [
        '#' + String(item.pago_id).padStart(5, '0'),
        formatearFechaHora(item.fecha_pago),
        String(item.cliente_nombre || ''),
        String(item.terapeuta_nombre || ''),
        String(item.tipo_pago || ''),
        formatearMonto(item.monto)
    ]);

    if (filas.length === 0) {
        filas.push([{ content: 'No existen transacciones liquidadas registradas en este periodo.', colSpan: 6, styles: { halign: 'center', textColor: gris } }]);
    }

    doc.autoTable({
        startY: doc.lastAutoTable.finalY + 8,
        head: [['N° PAGO', 'FECHA Y HORA', 'CLIENTE / PACIENTE', 'TERAPEUTA', 'MÉTODO PAGO', 'MONTO (C$)']],
        body: filas,
        theme: 'grid',
        margin: { left: margen, right: margen, bottom: 22 },
        styles: {
            font: 'helvetica',
            fontSize: 7.5,
            cellPadding: 2,
            textColor: [44, 62, 53],
            lineColor: borde,
            lineWidth: 0.1,
            overflow: 'linebreak'
        },
        headStyles: {
            fillColor: verde,
            textColor: [255, 255, 255],
            fontStyle: 'bold',
            fontSize: 7,
            halign: 'left'
        },
        alternateRowStyles: {
            fillColor: [248, 250, 249]
        },
        columnStyles: {
            0: { cellWidth: 18 },
            1: { cellWidth: 29 },
            2: { cellWidth: 'auto' },
            3: { cellWidth: 'auto' },
            4: { cellWidth: 25 },
            5: { cellWidth: 23, halign: 'right', fontStyle: 'bold' }
        },
        foot: [[
            { content: 'TOTAL GENERAL INGRESOS LIQUIDADOS (C$):', colSpan: 5, styles: { halign: 'right', fontStyle: 'bold', fillColor: [241, 245, 243], textColor: verde } },
            { content: formatearMonto(resumen.total_recaudado), styles: { halign: 'right', fontStyle: 'bold', fillColor: [241, 245, 243], textColor: verde } }
        ]],
        showFoot: 'lastPage'
    });

    // ---------------------------------------------------------------
    // Firmas, nota legal y pie de página en todas las hojas
    // ---------------------------------------------------------------
    let firmaY = doc.lastAutoTable.finalY + 22;

    if (firmaY + 20 > pageH - 20) {
        doc.addPage();
        firmaY = 40;
    }

    const centroA = margen + (pageW - margen * 2) * 0.25;
    const centroB = margen + (pageW - margen * 2) * 0.75;

    doc.setDrawColor(44, 62, 53);
    doc.setLineWidth(0.3);
    doc.line(centroA - 25, firmaY, centroA + 25, firmaY);
    doc.line(centroB - 25, firmaY, centroB + 25, firmaY);

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(8);
    doc.setTextColor(...verde);
    doc.text('Elaborado por', centroA, firmaY + 5, { align: 'center' });
    doc.text('Revisado & Aprobado', centroB, firmaY + 5, { align: 'center' });

    doc.setFont('helvetica', 'normal');
    doc.setFontSize(7);
    doc.setTextColor(...gris);
    doc.text('Administración Valle Sereno Spa', centroA, firmaY + 9.5, { align: 'center' });
    doc.text('Departamento Contable & Auditoría', centroB, firmaY + 9.5, { align: 'center' });

    doc.setFontSize(6.8);
    doc.setTextColor(...gris);
    doc.text(
        'Este informe contiene datos tributarios y contables oficiales generados automáticamente por el sistema de gestión de Valle Sereno Spa.',
        pageW / 2,
        firmaY + 17,
        { align: 'center', maxWidth: pageW - margen * 2 }
    );

    const totalPaginas = doc.getNumberOfPages();
    for (let i = 1; i <= totalPaginas; i++) {
        doc.setPage(i);
        doc.setDrawColor(...borde);
        doc.setLineWidth(0.2);
        doc.line(margen, pageH - 13, pageW - margen, pageH - 13);

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(6.5);
        doc.setTextColor(...gris);
        doc.text(`Valle Sereno Spa S.A. • RUC ${resumen.ruc_empresa || 'J031000029384'}`, margen, pageH - 8);
        doc.text(`Página ${i} de ${totalPaginas}`, pageW - margen, pageH - 8, { align: 'right' });
    }

    return doc;
}

function descargarPdf() {
    const doc = construirDocumentoPdf();
    if (!doc) return;

    const { inicio, fin } = reporteActual;
    doc.save(`Reporte_Contable_ValleSereno_${inicio}_al_${fin}.pdf`);
}

function imprimirPdf() {
    const doc = construirDocumentoPdf();
    if (!doc) return;

    const pdfUrl = doc.output('bloburl');
    const iframe = document.createElement('iframe');
    iframe.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;';
    iframe.src = pdfUrl;

    iframe.onload = () => {
        try {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        } catch (error) {
            window.open(pdfUrl, '_blank');
        }

        // Liberar el iframe después de que el usuario cierre el diálogo de impresión.
        window.setTimeout(() => {
            iframe.remove();
            URL.revokeObjectURL(pdfUrl);
        }, 120000);
    };

    document.body.appendChild(iframe);
}
</script>
