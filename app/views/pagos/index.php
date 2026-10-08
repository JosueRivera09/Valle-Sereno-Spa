<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--spa-primary); font-family: var(--font-serif);">
            <i class="bi bi-receipt text-warning me-2"></i> Caja & Conciliación de Pagos
        </h4>
        <p class="text-muted small mb-0">Emisión de recibos, registro de cobros por cita y arqueo diario por método de pago.</p>
    </div>
    <button class="btn btn-spa-primary btn-sm px-3 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalCobro" onclick="prepararNuevoCobro()">
        <i class="bi bi-cash-stack"></i>
        <span>Registrar Cobro en Caja</span>
    </button>
</div>

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
                <h3 class="fw-bold mb-0 text-dark" style="font-family: var(--font-serif);">$<?= number_format($totalGeneralHoy, 2) ?></h3>
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
                <h3 class="fw-bold mb-0 text-success" style="font-family: var(--font-serif);">$<?= number_format($totalEfectivo, 2) ?></h3>
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
                <h3 class="fw-bold mb-0 text-primary" style="font-family: var(--font-serif);">$<?= number_format($totalTarjeta, 2) ?></h3>
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
                <h3 class="fw-bold mb-0 text-warning" style="font-family: var(--font-serif);">$<?= number_format($totalTransf, 2) ?></h3>
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
            <i class="bi bi-journal-check text-warning me-2"></i> Historial de Transacciones
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
                </tr>
            </thead>
            <tbody style="font-size: 0.88rem;">
                <?php if (empty($pagos)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
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
                                    $<?= number_format($p['monto'], 2) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                    <i class="bi bi-check-circle-fill me-1"></i>Liquidado
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
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
                                    Cita #<?= $cp['id'] ?> - <?= htmlspecialchars($cp['cliente_nombre']) ?> ($<?= number_format($cp['total'], 2) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Método de Pago <span class="text-danger">*</span></label>
                        <select id="tipo_pago" name="tipo_pago" class="form-select" required>
                            <option value="Efectivo">Efectivo (Caja Chica)</option>
                            <option value="Tarjeta">Tarjeta Débito / Crédito</option>
                            <option value="Transferencia">Transferencia Bancaria SPEI</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Monto a Cobrar ($ MXN) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
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
            location.reload();
        } else {
            alert('Error: ' + res.mensaje);
        }
    });
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
