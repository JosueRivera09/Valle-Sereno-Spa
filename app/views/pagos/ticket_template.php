<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante_Pago_REC-<?= sprintf('%05d', $pagoDetails['pago_id']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1f2924;
            background: #f4f6f5;
            font-size: 13px;
        }

        .ticket-box {
            max-width: 450px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }

        .ticket-title {
            font-family: 'Playfair Display', serif;
            color: #1b3b2b;
            font-weight: 700;
        }

        .dashed-line {
            border-top: 1px dashed #cbd5e1;
            margin: 15px 0;
        }

        @media print {
            body {
                background: #fff !important;
            }
            .no-print {
                display: none !important;
            }
            .ticket-box {
                margin: 0 auto;
                box-shadow: none !important;
                border: none !important;
                padding: 10px;
            }
            @page {
                size: 80mm auto;
                margin: 5mm;
            }
        }
    </style>
</head>
<body>

    <!-- Botones Flotantes No Imprimibles -->
    <div class="no-print text-center my-3">
        <button onclick="window.print()" class="btn btn-primary btn-sm px-4 shadow-sm">
            <i class="bi bi-printer me-1"></i> Imprimir / Guardar Recibo (PDF)
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary btn-sm px-3 ms-2">
            Cerrar
        </button>
    </div>

    <!-- Ticket de Pago Oficial -->
    <div class="ticket-box">
        
        <!-- Header del Comprobante -->
        <div class="text-center mb-3">
            <h4 class="ticket-title mb-1"><i class="bi bi-flower1 text-warning me-1"></i>VALLE SERENO SPA S.A.</h4>
            <div class="small text-muted fw-semibold">Centro de Relajación & Terapias</div>
            <div class="small text-muted" style="font-size: 0.75rem;">
                RUC: J031000029384<br>
                Km 14.5 Carretera a Masaya, Managua, Nicaragua<br>
                Tel: +505 2255-8899 | info@vallesereno.com
            </div>
        </div>

        <div class="dashed-line"></div>

        <!-- Información del Recibo -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold text-uppercase text-dark" style="font-size: 0.75rem;">N° COMPROBANTE:</span>
            <span class="badge bg-dark text-white font-monospace fs-6">REC-<?= sprintf('%05d', $pagoDetails['pago_id']) ?></span>
        </div>
        <div class="d-flex justify-content-between text-muted small mb-1">
            <span>Fecha y Hora:</span>
            <span class="fw-semibold text-dark"><?= date('d/m/Y h:i A', strtotime($pagoDetails['fecha_pago'])) ?></span>
        </div>
        <div class="d-flex justify-content-between text-muted small mb-1">
            <span>Cita Asociada:</span>
            <span class="fw-semibold text-dark">#<?= $pagoDetails['cita_id'] ?></span>
        </div>

        <div class="dashed-line"></div>

        <!-- Datos del Cliente -->
        <div class="mb-3">
            <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem;">DATOS DEL CLIENTE / PACIENTE</small>
            <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($pagoDetails['cliente_nombre']) ?></div>
            <div class="small text-muted"><i class="bi bi-telephone me-1"></i>Tel: <?= htmlspecialchars($pagoDetails['cliente_telefono']) ?></div>
            <div class="small text-muted"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($pagoDetails['cliente_correo']) ?></div>
        </div>

        <!-- Atendido Por -->
        <div class="mb-3 p-2 rounded bg-light" style="font-size: 0.8rem;">
            <span class="text-muted">Terapeuta Asignado:</span>
            <strong class="text-dark d-block"><?= htmlspecialchars($pagoDetails['terapeuta_nombre']) ?></strong>
        </div>

        <div class="dashed-line"></div>

        <!-- Desglose Financiero -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted">Método de Pago:</span>
            <span class="badge bg-light text-dark border px-2 py-1"><?= htmlspecialchars($pagoDetails['tipo_pago']) ?></span>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted">Estado Fiscal:</span>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">PAGADO & LIQUIDADO</span>
        </div>

        <div class="dashed-line"></div>

        <!-- Total Pagado -->
        <div class="d-flex justify-content-between align-items-center my-3">
            <span class="fw-bold fs-6 text-dark text-uppercase">TOTAL PAGADO:</span>
            <span class="fw-bold fs-4 text-success" style="font-family: 'Playfair Display', serif;">
                C$<?= number_format((float)$pagoDetails['monto'], 2) ?>
            </span>
        </div>

        <div class="dashed-line"></div>

        <!-- Pie del Recibo -->
        <div class="text-center text-muted small mt-4">
            <p class="mb-1 fw-semibold">¡Gracias por elegir Valle Sereno Spa!</p>
            <small style="font-size: 0.7rem;">Conserve este comprobante para cualquier consulta. Documento fiscal oficial emitido por el sistema.</small>
        </div>

    </div>

    <script>
        window.onload = () => {
            setTimeout(() => {
                window.print();
            }, 300);
        };
    </script>
</body>
</html>
