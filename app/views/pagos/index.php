<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--spa-primary); font-family: var(--font-serif);">
            <i class="bi bi-receipt text-warning me-2"></i> Caja & Conciliación de Pagos
        </h4>
        <p class="text-muted small mb-0">Emisión de recibos, registro de cobros por cita, apertura de turno y arqueo diario con cuadre de efectivo.</p>
    </div>
    <div class="d-flex gap-2">
        <?php if (!empty($estadoCaja['abierta'])): ?>
            <button class="btn btn-outline-warning btn-sm px-3 text-dark d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalCerrarCaja">
                <i class="bi bi-lock-fill"></i>
                <span>Arqueo & Cierre de Caja</span>
            </button>
            <button class="btn btn-spa-primary btn-sm px-3 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalCobro" onclick="prepararNuevoCobro()">
                <i class="bi bi-cash-stack"></i>
                <span>Registrar Cobro</span>
            </button>
        <?php else: ?>
            <button class="btn btn-danger btn-sm px-3 d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAbrirCaja">
                <i class="bi bi-unlock-fill"></i>
                <span>Abrir Turno de Caja</span>
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Banner Estado de Caja -->
<?php if (empty($estadoCaja['abierta'])): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>LA CAJA SE ENCUENTRA CERRADA</h6>
            <small class="mb-0">Debes realizar la <strong>Apertura de Caja</strong> ingresando el fondo inicial de efectivo para comenzar a registrar cobros.</small>
        </div>
        <button class="btn btn-danger btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalAbrirCaja">
            <i class="bi bi-key-fill me-1"></i> Abrir Caja Ahora
        </button>
    </div>
<?php else: ?>
    <div class="alert alert-success border-0 shadow-sm mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" style="background: #eef8f3; color: #1b3b2b;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-success rounded-pill px-2 py-1"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>Caja Abierta</span>
                <span class="small text-muted">Apertura: <?= date('d/m/Y h:i A', strtotime($estadoCaja['caja']['fecha_apertura'])) ?> por <strong><?= htmlspecialchars($estadoCaja['caja']['nombre_completo']) ?></strong></span>
            </div>
            <div class="small text-dark">
                <strong>Fondo Inicial:</strong> C$<?= number_format($estadoCaja['monto_inicial'], 2) ?> &bull; 
                <strong>Cobrado Efectivo:</strong> C$<?= number_format($estadoCaja['efectivo_cobrado'], 2) ?> &bull; 
                <strong>Efectivo Esperado en Caja:</strong> <span class="fw-bold text-success fs-6">C$<?= number_format($estadoCaja['monto_esperado_efectivo'], 2) ?></span>
            </div>
        </div>
        <div>
            <button class="btn btn-outline-success btn-sm px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalCerrarCaja">
                <i class="bi bi-calculator me-1"></i> Arqueo de Turno
            </button>
        </div>
    </div>
<?php endif; ?>

<!-- Tarjetas Resumen de Caja del Día -->
<div class="row g-3 mb-4">
    <?php
        $totalEfectivo = 0;
        $totalTarjeta = 0;
        $totalTransf = 0;
        foreach ($resumenCaja ?? [] as $r) {
            if ($r['tipo_pago'] === 'Efectivo') $totalEfectivo = $r['total'];
            if ($r['tipo_pago'] === 'Tarjeta') $totalTarjeta = $r['total'];
            if ($r['tipo_pago'] === 'Transferencia') $totalTransf = $r['total'];
        }
        $totalGeneralHoy = $totalEfectivo + $totalTarjeta + $totalTransf;
    ?>
    <div class="col-sm-6 col-md-3">
        <div class="spa-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">Recaudación Hoy</small>
                <h3 class="fw-bold mb-0 text-dark" style="font-family: var(--font-serif);">C$<?= number_format($totalGeneralHoy, 2) ?></h3>
            </div>
            <div class="stat-icon" style="background: rgba(30, 61, 52, 0.1); color: var(--spa-primary);">
                <i class="bi bi-wallet2"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="spa-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">Efectivo en Caja</small>
                <h3 class="fw-bold mb-0 text-success" style="font-family: var(--font-serif);">C$<?= number_format($totalEfectivo, 2) ?></h3>
            </div>
            <div class="stat-icon" style="background: #eef6f3; color: #3b735c;">
                <i class="bi bi-cash-coin"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="spa-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">Tarjetas</small>
                <h3 class="fw-bold mb-0 text-primary" style="font-family: var(--font-serif);">C$<?= number_format($totalTarjeta, 2) ?></h3>
            </div>
            <div class="stat-icon" style="background: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                <i class="bi bi-credit-card-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="spa-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">Transferencias</small>
                <h3 class="fw-bold mb-0 text-warning" style="font-family: var(--font-serif);">C$<?= number_format($totalTransf, 2) ?></h3>
            </div>
            <div class="stat-icon" style="background: rgba(255, 193, 7, 0.15); color: #997838;">
                <i class="bi bi-arrow-left-right"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Pagos Realizados -->
<div class="spa-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0" style="color: var(--spa-primary); font-family: var(--font-serif);">
            <i class="bi bi-journal-check text-warning me-2"></i> Historial de Transacciones & Recibos
        </h5>
        <div class="input-group input-group-sm" style="max-width: 280px;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="busquedaPago" class="form-control border-start-0" placeholder="Buscar por cliente..." onkeyup="filtrarTablaPagos()">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tablaPagos">
            <thead class="table-light">
                <tr style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #556b61;">
                    <th>RECIBO ID</th>
                    <th>FECHA & HORA</th>
                    <th>CLIENTE</th>
                    <th>CITA ASOCIADA</th>
                    <th>MÉTODO DE PAGO</th>
                    <th>MONTO COBRADO</th>
                    <th>ESTADO</th>
                    <th class="text-end">TICKET</th>
                </tr>
            </thead>
            <tbody style="font-size: 0.88rem;">
                <?php if (empty($pagos)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-1 text-muted"></i>
                            No hay transacciones ni cobros liquidados aún.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pagos as $p): ?>
                        <tr>
                            <td class="fw-bold text-dark">#REC-<?= str_pad($p['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td>
                                <span class="text-secondary">
                                    <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y H:i', strtotime($p['fecha_pago'])) ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($p['cliente_nombre']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    Cita #<?= $p['id_cita'] ?> (<?= date('d/m/Y', strtotime($p['cita_fecha'])) ?>)
                                </span>
                            </td>
                            <td>
                                <?php
                                    $iconMethod = match($p['tipo_pago']) {
                                        'Efectivo' => 'bi-cash-coin text-success',
                                        'Tarjeta' => 'bi-credit-card text-primary',
                                        'Transferencia' => 'bi-arrow-left-right text-warning',
                                        default => 'bi-wallet'
                                    };
                                ?>
                                <span class="fw-medium text-dark">
                                    <i class="bi <?= $iconMethod ?> me-1"></i><?= htmlspecialchars($p['tipo_pago']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-dark fs-6" style="font-family: var(--font-serif); color: var(--spa-primary);">
                                    C$<?= number_format($p['monto'], 2) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                    <i class="bi bi-check-circle-fill me-1"></i>Liquidado
                                </span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-outline-primary btn-sm py-0 px-2" title="Imprimir Ticket de Recibo" onclick="imprimirTicket(<?= $p['id'] ?>)">
                                    <i class="bi bi-printer me-1"></i> Recibo PDF
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Apertura de Caja -->
<div class="modal fade" id="modalAbrirCaja" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--spa-primary);">
                <h5 class="modal-title fw-bold" style="font-family: var(--font-serif);">
                    <i class="bi bi-key-fill text-warning me-2"></i> Apertura de Turno de Caja
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAbrirCaja" onsubmit="procesarAbrirCaja(event)">
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="bi bi-info-circle me-1"></i> Ingresa el monto del fondo base de efectivo entregado para dar inicio a la jornada.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Monto Inicial en Fondo (C$ Córdobas) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">C$</span>
                            <input type="number" step="0.01" name="monto_inicial" class="form-control" placeholder="0.00" value="1000.00" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Observaciones de Apertura</label>
                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Fondo inicial entregado por administración..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spa-primary btn-sm px-4">
                        <i class="bi bi-unlock me-1"></i> Confirmar Apertura
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Arqueo y Cierre de Caja -->
<div class="modal fade" id="modalCerrarCaja" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold" style="font-family: var(--font-serif);">
                    <i class="bi bi-calculator me-2"></i> Arqueo & Cierre de Turno
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCerrarCaja" onsubmit="procesarCerrarCaja(event)">
                <div class="modal-body p-4">
                    
                    <div class="p-3 mb-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Fondo Inicial:</span>
                            <span class="fw-bold">C$<?= number_format($estadoCaja['monto_inicial'] ?? 0, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Efectivo Cobrado:</span>
                            <span class="fw-bold text-success">+ C$<?= number_format($estadoCaja['efectivo_cobrado'] ?? 0, 2) ?></span>
                        </div>
                        <div class="dashed-line my-2 border-top"></div>
                        <div class="d-flex justify-content-between font-monospace">
                            <span class="fw-bold text-dark">EFECTIVO ESPERADO EN CAJA:</span>
                            <span class="fw-bold text-success fs-6" id="valEsperadoEfectivo">C$<?= number_format($estadoCaja['monto_esperado_efectivo'] ?? 0, 2) ?></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Efectivo Físico Contado (C$) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text fw-bold">C$</span>
                            <input type="number" step="0.01" id="monto_final_efectivo" name="monto_final_efectivo" class="form-control" placeholder="0.00" onkeyup="calcularCuadreCaja()" onchange="calcularCuadreCaja()" required>
                        </div>
                    </div>

                    <div class="p-3 mb-3 rounded-3 text-center border" id="boxCuadreCaja" style="background: #f8faf9;">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.72rem;">Resultado del Arqueo</small>
                        <div class="fs-5 fw-bold" id="lblResultadoDiferencia">Ingresa el efectivo contado</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Observaciones del Arqueo</label>
                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Justificación en caso de sobrante o faltante..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning btn-sm px-4 fw-bold">
                        <i class="bi bi-lock-fill me-1"></i> Cerrar Caja Oficialmente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Registrar Cobro -->
<div class="modal fade" id="modalCobro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--spa-primary);">
                <h5 class="modal-title fw-bold" style="font-family: var(--font-serif);">
                    <i class="bi bi-cash-stack text-warning me-2"></i> Registrar Cobro en Caja
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCobro" onsubmit="procesarCobro(event)">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Seleccionar Cita por Cobrar <span class="text-danger">*</span></label>
                        <select id="id_cita" name="id_cita" class="form-select" onchange="actualizarMontoPorCita(this)" required>
                            <option value="">-- Seleccionar Cita Pendiente --</option>
                            <?php foreach ($citasPendientes as $cp): ?>
                                <option value="<?= $cp['id'] ?>" data-monto="<?= $cp['total'] ?>">
                                    Cita #<?= $cp['id'] ?> - <?= htmlspecialchars($cp['cliente_nombre']) ?> (C$<?= number_format($cp['total'], 2) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Método de Pago <span class="text-danger">*</span></label>
                        <select id="tipo_pago" name="tipo_pago" class="form-select" required>
                            <option value="Efectivo">Efectivo (Caja Chica)</option>
                            <option value="Tarjeta">Tarjeta Débito / Crédito (POS)</option>
                            <option value="Transferencia">Transferencia Bancaria</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Monto a Cobrar (C$ Córdobas) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">C$</span>
                            <input type="number" step="0.01" id="monto" name="monto" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spa-primary btn-sm px-4">
                        <i class="bi bi-printer me-1"></i> Procesar & Emitir Recibo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let modalCobroBs;
const MONTO_ESPERADO_EFECTIVO = <?= (float)($estadoCaja['monto_esperado_efectivo'] ?? 0) ?>;

document.addEventListener('DOMContentLoaded', () => {
    modalCobroBs = new bootstrap.Modal(document.getElementById('modalCobro'));
});

function prepararNuevoCobro() {
    document.getElementById('formCobro').reset();
}

function actualizarMontoPorCita(select) {
    const option = select.options[select.selectedIndex];
    const monto = option.getAttribute('data-monto');
    if (monto) {
        document.getElementById('monto').value = monto;
    }
}

function procesarCobro(e) {
    e.preventDefault();
    const fd = new FormData(document.getElementById('formCobro'));

    fetch('index.php?c=pagos&a=cobrar', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            modalCobroBs.hide();
            alert('¡Pago registrado correctamente!');
            location.reload();
        } else {
            alert('Error: ' + res.mensaje);
        }
    });
}

function procesarAbrirCaja(e) {
    e.preventDefault();
    const fd = new FormData(document.getElementById('formAbrirCaja'));

    fetch('index.php?c=pagos&a=abrirCaja', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            alert(res.mensaje);
            location.reload();
        } else {
            alert('Error: ' + res.mensaje);
        }
    });
}

function calcularCuadreCaja() {
    const contado = parseFloat(document.getElementById('monto_final_efectivo').value || 0);
    const dif = contado - MONTO_ESPERADO_EFECTIVO;
    const box = document.getElementById('boxCuadreCaja');
    const lbl = document.getElementById('lblResultadoDiferencia');

    if (isNaN(contado)) return;

    if (dif === 0) {
        box.style.background = '#eef8f3';
        lbl.className = 'fs-5 fw-bold text-success';
        lbl.innerHTML = '<i class="bi bi-check-circle me-1"></i> Cuadre Perfecto (Sin Diferencia)';
    } else if (dif > 0) {
        box.style.background = '#fffbeb';
        lbl.className = 'fs-5 fw-bold text-warning';
        lbl.innerHTML = `<i class="bi bi-plus-circle me-1"></i> Sobrante: +C$${dif.toFixed(2)}`;
    } else {
        box.style.background = '#fef2f2';
        lbl.className = 'fs-5 fw-bold text-danger';
        lbl.innerHTML = `<i class="bi bi-dash-circle me-1"></i> Faltante: -C$${Math.abs(dif).toFixed(2)}`;
    }
}

function procesarCerrarCaja(e) {
    e.preventDefault();
    const fd = new FormData(document.getElementById('formCerrarCaja'));

    fetch('index.php?c=pagos&a=cerrarCaja', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            alert(res.mensaje);
            location.reload();
        } else {
            alert('Error: ' + res.mensaje);
        }
    });
}

function imprimirTicket(idPago) {
    window.open(`index.php?c=pagos&a=ticket&id=${idPago}`, '_blank');
}

function filtrarTablaPagos() {
    const input = document.getElementById('busquedaPago').value.toLowerCase();
    const rows = document.querySelectorAll('#tablaPagos tbody tr');
    rows.forEach(r => {
        const text = r.innerText.toLowerCase();
        r.style.display = text.includes(input) ? '' : 'none';
    });
}
</script>
