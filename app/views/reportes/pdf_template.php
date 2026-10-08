<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte_Contable_ValleSereno_<?= htmlspecialchars($inicio) ?>_<?= htmlspecialchars($fin) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #2c3e35;
            background: #fff;
            font-size: 13px;
        }

        .header-title {
            font-family: 'Playfair Display', serif;
            color: #1b3b2b;
            font-weight: 700;
        }

        .kpi-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            background-color: #f8faf9;
        }

        .table-custom th {
            background-color: #1b3b2b !important;
            color: #ffffff !important;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 12px;
        }

        .table-custom td {
            padding: 8px 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .signature-line {
            border-top: 1px solid #2c3e35;
            width: 200px;
            margin-top: 50px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 15mm;
            }
        }
    </style>
</head>
<body class="p-4">

    <!-- Acciones de Impresión (Solo pantalla) -->
    <div class="no-print d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded-3 border">
        <div>
            <strong><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Vista Previa de Documento Contable (PDF)</strong>
            <div class="small text-muted">Haz clic en Imprimir o Guardar como PDF para generar el archivo tributario oficial.</div>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm px-3">
                <i class="bi bi-printer me-1"></i> Imprimir / Descargar PDF
            </button>
            <button onclick="window.close()" class="btn btn-outline-secondary btn-sm">
                Cerrar
            </button>
        </div>
    </div>

    <!-- Encabezado Corporativo & Datos Fiscales -->
    <div class="row align-items-center mb-4 pb-3 border-bottom">
        <div class="col-7">
            <h2 class="header-title mb-1">VALLE SERENO SPA S.A.</h2>
            <div class="fw-semibold text-success small mb-1">Centro de Relajación, Salud Terapéutica & Bienestar</div>
            <div class="text-muted small">
                <strong>RUC:</strong> <?= htmlspecialchars($resumen['ruc_empresa'] ?? 'J031000029384') ?><br>
                <strong>Dirección:</strong> Km 14.5 Carretera a Masaya, Managua, Nicaragua<br>
                <strong>Teléfono:</strong> +505 2255-8899 | <strong>Correo:</strong> contabilidad@vallesereno.com
            </div>
        </div>
        <div class="col-5 text-end">
            <div class="p-3 bg-light rounded border text-start">
                <h6 class="fw-bold mb-2 text-uppercase text-dark" style="font-size: 11px; letter-spacing: 0.5px;">DATOS DEL INFORME FISCAL</h6>
                <div class="small"><strong>Periodo:</strong> Del <?= date('d/m/Y', strtotime($inicio)) ?> al <?= date('d/m/Y', strtotime($fin)) ?></div>
                <div class="small"><strong>Fecha Emisión:</strong> <?= date('d/m/Y h:i A') ?></div>
                <div class="small"><strong>Moneda:</strong> Córdobas (C$ NIO)</div>
                <div class="small"><strong>Estado:</strong> Liquidado & Verificado</div>
            </div>
        </div>
    </div>

    <!-- Título del Reporte -->
    <div class="text-center my-3">
        <h4 class="fw-bold text-uppercase header-title mb-1">REPORTE CONTABLE Y LIBRO AUXILIAR DE RECAUDACIÓN</h4>
        <div class="text-muted small">Consolidado de ingresos por prestación de servicios y tratamientos de Spa</div>
    </div>

    <!-- Resumen Contable KPIS -->
    <div class="row g-3 mb-4">
        <div class="col-3">
            <div class="kpi-card text-center">
                <div class="text-muted small fw-bold text-uppercase">Total Recaudado</div>
                <div class="fs-5 fw-bold text-success">C$<?= number_format($resumen['total_recaudado'] ?? 0, 2) ?></div>
            </div>
        </div>
        <div class="col-3">
            <div class="kpi-card text-center">
                <div class="text-muted small fw-bold text-uppercase">En Efectivo</div>
                <div class="fs-6 fw-bold text-dark">C$<?= number_format($resumen['total_efectivo'] ?? 0, 2) ?></div>
            </div>
        </div>
        <div class="col-3">
            <div class="kpi-card text-center">
                <div class="text-muted small fw-bold text-uppercase">En Tarjeta (Pos)</div>
                <div class="fs-6 fw-bold text-dark">C$<?= number_format($resumen['total_tarjeta'] ?? 0, 2) ?></div>
            </div>
        </div>
        <div class="col-3">
            <div class="kpi-card text-center">
                <div class="text-muted small fw-bold text-uppercase">En Transferencia</div>
                <div class="fs-6 fw-bold text-dark">C$<?= number_format($resumen['total_transferencia'] ?? 0, 2) ?></div>
            </div>
        </div>
    </div>

    <!-- Tabla Detallada -->
    <table class="table table-custom align-middle w-100 mb-4">
        <thead>
            <tr>
                <th>N° PAGO</th>
                <th>FECHA Y HORA</th>
                <th>CLIENTE / PACIENTE</th>
                <th>TERAPEUTA</th>
                <th>MÉTODO PAGO</th>
                <th class="text-end">MONTO (C$)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data)): ?>
                <tr>
                    <td colspan="6" class="text-center py-3 text-muted">No existen transacciones liquidadas registradas en este periodo.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td class="fw-bold">#<?= sprintf('%05d', $row['pago_id']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['fecha_pago'])) ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($row['cliente_nombre']) ?></td>
                        <td><?= htmlspecialchars($row['terapeuta_nombre']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['tipo_pago']) ?></span></td>
                        <td class="text-end fw-bold">C$<?= number_format($row['monto'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="fw-bold table-light">
                <td colspan="5" class="text-end text-uppercase" style="padding: 10px;">TOTAL GENERAL INGRESOS LIQUIDADOS (C$):</td>
                <td class="text-end text-success fs-6" style="padding: 10px;">C$<?= number_format($resumen['total_recaudado'] ?? 0, 2) ?></td>
            </tr>
        </tfoot>
    </table>

    <!-- Firmas y Notas Legales -->
    <div class="row mt-5 pt-3">
        <div class="col-6 text-center">
            <div class="signature-line mx-auto"></div>
            <div class="fw-bold mt-2">Elaborado por</div>
            <div class="small text-muted">Administración Valle Sereno Spa</div>
        </div>
        <div class="col-6 text-center">
            <div class="signature-line mx-auto"></div>
            <div class="fw-bold mt-2">Revisado & Aprobado</div>
            <div class="small text-muted">Departamento Contable & Auditoría</div>
        </div>
    </div>

    <div class="mt-5 text-center text-muted small border-top pt-3">
        <em>Este informe contiene datos tributarios y contables oficiales generados automáticamente por el sistema de gestión de Valle Sereno Spa.</em>
    </div>

    <script>
        // Disparar ventana de impresión automáticamente en 500ms
        window.onload = () => {
            setTimeout(() => {
                window.print();
            }, 400);
        };
    </script>
</body>
</html>
