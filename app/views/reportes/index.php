<!-- Encabezado Editorial -->
<div class="page-head">
    <div>
        <span class="kicker">Analítica</span>
        <h1 class="display-title">Reportes <em>& Métricas de Rendimiento</em></h1>
    </div>
</div>

<!-- Filtro Personalizado por Rango de Fechas -->
<div class="panel panel--feature mb-4">
    <div class="panel-head">
        <h2 class="panel-title">Filtrar Transacciones por Periodo</h2>
    </div>
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
            <div class="modal-footer d-flex justify-content-between">
                <div class="small text-muted">
                    <i class="bi bi-shield-check text-success me-1"></i> Formato oficial apto para contabilidad y auditorías tributarias.
                </div>
                <div class="d-flex flex-wrap justify-content-end gap-2">
                    <button type="button" class="btn btn-quiet btn-sm px-3" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm px-3" onclick="descargarExcel()">
                        <i class="bi bi-file-earmark-excel me-1"></i> Exportar a Excel
                    </button>
                    <button type="button" class="btn btn-outline-dark btn-sm px-3" onclick="imprimirPdf()">
                        <i class="bi bi-printer me-1"></i> Imprimir
                    </button>
                    <button type="button" class="btn btn-spa-primary btn-sm px-3" onclick="descargarPdf()">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Descargar
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
                    <td><span class="badge bg-light text-dark border">${esc(item.tipo_pago)}</span></td>
                    <td class="text-end fw-bold text-success fs-6" style="font-family: var(--font-serif);">C$${parseFloat(item.monto).toFixed(2)}</td>
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
