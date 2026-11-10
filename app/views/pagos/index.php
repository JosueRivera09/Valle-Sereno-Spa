<!-- Encabezado de Página SaaS -->
<div class="saas-page-header">
    <div class="saas-header-info">
        <span class="saas-breadcrumb"><i class="bi bi-wallet2 me-1"></i>Finanzas & Tesorería</span>
        <h1 class="saas-title">Caja Diaria & Conciliación de Pagos</h1>
        <p class="saas-subtitle">Apertura de turno, liquidación en vivo de citas, emisión de comprobantes y arqueo.</p>
    </div>
    <div class="saas-header-actions">
        <?php if (!empty($estadoCaja['abierta'])): ?>
            <button class="btn-saas-secondary" data-bs-toggle="modal" data-bs-target="#modalCerrarCaja">
                <i class="bi bi-lock-fill"></i>
                <span>Arqueo & Cierre</span>
            </button>
            <button class="btn-saas-primary" data-bs-toggle="modal" data-bs-target="#modalCobro" onclick="prepararNuevoCobro()">
                <i class="bi bi-cash-stack"></i>
                <span>Registrar Cobro</span>
            </button>
        <?php else: ?>
            <button class="btn-saas-primary" style="background: #dc2626; border-color: #dc2626;" data-bs-toggle="modal" data-bs-target="#modalAbrirCaja">
                <i class="bi bi-unlock-fill"></i>
                <span>Abrir Turno de Caja</span>
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Banner Estado de Caja SaaS -->
<?php if (empty($estadoCaja['abierta'])): ?>
    <div class="py-2 px-3 mb-3 rounded-3 border d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2" style="background: #fef2f2; border-color: #fecaca !important; color: #991b1b;">
        <div class="d-flex align-items-center gap-2">
            <div class="saas-stat-icon is-red" style="background: #fee2e2; color: #dc2626; width: 34px; height: 34px;">
                <i class="bi bi-exclamation-octagon-fill"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0" style="font-size: 0.88rem;">Turno de Caja Cerrado</h6>
                <small style="color: #7f1d1d; font-size: 0.78rem;">Realiza la apertura de turno ingresando el fondo inicial de efectivo para habilitar el cobro de citas.</small>
            </div>
        </div>
        <button class="btn-saas-primary btn-sm px-3" style="background: #dc2626; border-color: #dc2626;" data-bs-toggle="modal" data-bs-target="#modalAbrirCaja">
            <i class="bi bi-key-fill me-1"></i> Abrir Caja Ahora
        </button>
    </div>
<?php else: ?>
    <div class="py-2 px-3 mb-3 rounded-3 border d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 shadow-xs" style="background: #f0fdf4; border-color: #bbf7d0 !important; color: #166534;">
        <div class="d-flex align-items-center gap-2">
            <div class="saas-stat-icon" style="background: #dcfce7; color: #15803d; width: 34px; height: 34px;">
                <i class="bi bi-shop"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <span class="saas-badge saas-badge-success py-0 px-2"><span class="saas-badge-dot"></span>Caja Operativa</span>
                    <small class="text-muted" style="font-size: 0.76rem;">Apertura: <strong><?= date('d/m/Y h:i A', strtotime($estadoCaja['caja']['fecha_apertura'])) ?></strong> por <strong><?= htmlspecialchars($estadoCaja['caja']['nombre_completo']) ?></strong></small>
                </div>
                <div style="color: #14532d; font-size: 0.8rem;">
                    Fondo Inicial: <strong>C$ <?= number_format($estadoCaja['monto_inicial'], 2) ?></strong> &bull; 
                    Efectivo Cobrado: <strong>C$ <?= number_format($estadoCaja['efectivo_cobrado'], 2) ?></strong> &bull; 
                    Efectivo Esperado: <span class="fw-bold text-dark">C$ <?= number_format($estadoCaja['monto_esperado_efectivo'], 2) ?></span>
                </div>
            </div>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-success px-3 py-1 font-semibold" style="font-size: 0.78rem;" data-bs-toggle="modal" data-bs-target="#modalCerrarCaja">
                <i class="bi bi-calculator me-1"></i> Arqueo de Turno
            </button>
        </div>
    </div>
<?php endif; ?>

<!-- Grid de KPIs / Estadísticas SaaS -->
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
<div class="saas-stats-grid">
    <div class="saas-stat-card">
        <div class="saas-stat-top">
            <span class="saas-stat-label">Recaudación de Hoy</span>
            <div class="saas-stat-icon">
                <i class="bi bi-cash-coin"></i>
            </div>
        </div>
        <div class="saas-stat-value">C$ <?= number_format($totalGeneralHoy, 2) ?></div>
        <div class="saas-stat-meta">
            <span class="text-muted">Total liquidado en el turno</span>
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-top">
            <span class="saas-stat-label">Efectivo en Caja</span>
            <div class="saas-stat-icon is-gold">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
        <div class="saas-stat-value">C$ <?= number_format($totalEfectivo, 2) ?></div>
        <div class="saas-stat-meta">
            <span class="text-muted"><?= $totalGeneralHoy > 0 ? round(($totalEfectivo / $totalGeneralHoy) * 100) : 0 ?>% de recaudación</span>
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-top">
            <span class="saas-stat-label">Cobros con Tarjeta</span>
            <div class="saas-stat-icon is-blue">
                <i class="bi bi-credit-card"></i>
            </div>
        </div>
        <div class="saas-stat-value">C$ <?= number_format($totalTarjeta, 2) ?></div>
        <div class="saas-stat-meta">
            <span class="text-muted">POS y terminales</span>
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-top">
            <span class="saas-stat-label">Transferencias</span>
            <div class="saas-stat-icon is-purple">
                <i class="bi bi-bank"></i>
            </div>
        </div>
        <div class="saas-stat-value">C$ <?= number_format($totalTransf, 2) ?></div>
        <div class="saas-stat-meta">
            <span class="text-muted">Banca móvil / ACH</span>
        </div>
    </div>
</div>

<?php if (!empty($estadoCaja['abierta'])): ?>
    <!-- Panel Operativo: Registrar Cobro Directo en Caja -->
    <div class="saas-card">
        <div class="saas-card-head">
            <h2 class="saas-card-title">
                <i class="bi bi-cash-stack text-primary"></i>
                <span>Registrar Cobro Inmediato</span>
            </h2>
            <span class="saas-badge saas-badge-success">
                <span class="saas-badge-dot"></span>Caja Operativa
            </span>
        </div>
        <div class="saas-card-body">
            <form id="formCobroDirecto" onsubmit="procesarCobroDirecto(event)">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-5 col-md-6">
                        <label class="form-label fw-bold small text-dark mb-1">
                            Cita por Cobrar <span class="text-danger">*</span>
                        </label>
                        <select id="directo_id_cita" name="id_cita" class="form-select" onchange="actualizarMontoPorCitaDirecto(this)" required>
                            <option value="">-- Seleccionar Cita Pendiente (<?= count($citasPendientes) ?>) --</option>
                            <?php foreach ($citasPendientes as $cp): ?>
                                <option value="<?= $cp['id'] ?>" data-monto="<?= $cp['saldo_pendiente'] ?>">
                                    Cita #<?= $cp['id'] ?> &bull; <?= htmlspecialchars($cp['cliente_nombre']) ?> &bull; Saldo: C$<?= number_format($cp['saldo_pendiente'], 2) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <label class="form-label fw-bold small text-dark mb-1">
                            Método de Pago <span class="text-danger">*</span>
                        </label>
                        <select id="directo_tipo_pago" name="tipo_pago" class="form-select" required>
                            <option value="Efectivo">Efectivo (Caja)</option>
                            <option value="Tarjeta">Tarjeta Débito / Crédito (POS)</option>
                            <option value="Transferencia">Transferencia Bancaria</option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <label class="form-label fw-bold small text-dark mb-1">
                            Monto (C$) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">C$</span>
                            <input type="number" step="0.01" id="directo_monto" name="monto" class="form-control fw-bold text-dark" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-12">
                        <button type="submit" class="btn-saas-primary w-100 justify-content-center py-2" id="btnSubmitCobroDirecto">
                            <i class="bi bi-check2-circle fs-6"></i>
                            <span>Cobrar Ahora</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<!-- Tabla de Pagos Realizados -->
<div class="saas-card">
    <div class="saas-card-head">
        <h2 class="saas-card-title">
            <i class="bi bi-receipt-cutoff text-gold"></i>
            <span>Historial de Transacciones &amp; Recibos</span>
        </h2>
        <div class="saas-search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="busquedaPago" class="saas-search-input" placeholder="Buscar por cliente, recibo..." onkeyup="filtrarTablaPagos()">
        </div>
    </div>

    <div class="saas-card-body p-0">
        <div class="table-responsive">
            <table class="saas-table" id="tablaPagos">
                <thead>
                    <tr>
                        <th>RECIBO ID</th>
                        <th>FECHA & HORA</th>
                        <th>PACIENTE / CLIENTE</th>
                        <th>CITA ASOCIADA</th>
                        <th>MÉTODO DE PAGO</th>
                        <th>MONTO COBRADO</th>
                        <th>ESTADO</th>
                        <th class="text-end">COMPROBANTE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pagos)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                No hay transacciones ni cobros liquidados aún.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pagos as $p): ?>
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark">#REC-<?= str_pad($p['id'], 5, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y H:i', strtotime($p['fecha_pago'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($p['cliente_nombre']) ?></div>
                                </td>
                                <td>
                                    <span class="saas-badge saas-badge-neutral">
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
                                    <span class="fw-medium text-dark small">
                                        <i class="bi <?= $iconMethod ?> me-1"></i><?= htmlspecialchars($p['tipo_pago']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-6">
                                        C$ <?= number_format($p['monto'], 2) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="saas-badge saas-badge-success">
                                        <span class="saas-badge-dot"></span>Liquidado
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary py-1 px-2" title="Imprimir Ticket de Recibo" onclick="imprimirTicket(<?= $p['id'] ?>)">
                                        <i class="bi bi-printer me-1"></i> Recibo
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
        <div class="modal-content modal-editorial">
            <div class="modal-header">
                <div>
                    <span class="modal-kicker">Caja</span>
                    <h5 class="modal-title">
                        <i class="bi bi-key-fill me-2"></i>Apertura de Turno de Caja
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAbrirCaja" onsubmit="procesarAbrirCaja(event)">
                <div class="modal-body">
                    <div class="modal-note">
                        <i class="bi bi-info-circle"></i>
                        <span>Ingresa el monto del fondo base de efectivo entregado para dar inicio a la jornada.</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Monto inicial en fondo (C$) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">C$</span>
                            <input type="number" step="0.01" name="monto_inicial" class="form-control" placeholder="0.00" value="1000.00" required>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Observaciones de apertura</label>
                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Fondo inicial entregado por administración..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-quiet btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
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
        <div class="modal-content modal-editorial">
            <div class="modal-header">
                <div>
                    <span class="modal-kicker">Caja</span>
                    <h5 class="modal-title">
                        <i class="bi bi-calculator me-2"></i>Arqueo &amp; Cierre de Turno
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCerrarCaja" onsubmit="procesarCerrarCaja(event)">
                <div class="modal-body">

                    <div class="summary-box mb-3">
                        <div class="summary-row">
                            <span class="label">Fondo inicial:</span>
                            <span class="value">C$<?= number_format($estadoCaja['monto_inicial'] ?? 0, 2) ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="label">Efectivo cobrado:</span>
                            <span class="value" style="color: var(--spa-leaf-ink);">+ C$<?= number_format($estadoCaja['efectivo_cobrado'] ?? 0, 2) ?></span>
                        </div>
                        <div class="summary-total">
                            <span class="label">Efectivo esperado en caja</span>
                            <span class="value" id="valEsperadoEfectivo">C$<?= number_format($estadoCaja['monto_esperado_efectivo'] ?? 0, 2) ?></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Efectivo físico contado (C$) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">C$</span>
                            <input type="number" step="0.01" id="monto_final_efectivo" name="monto_final_efectivo" class="form-control" placeholder="0.00" onkeyup="calcularCuadreCaja()" onchange="calcularCuadreCaja()" required>
                        </div>
                    </div>

                    <div class="summary-box text-center mb-3" id="boxCuadreCaja">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.72rem; letter-spacing: 1.4px;">Resultado del arqueo</small>
                        <div class="fs-5 fw-bold" id="lblResultadoDiferencia">Ingresa el efectivo contado</div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Observaciones del arqueo</label>
                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Justificación en caso de sobrante o faltante..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-quiet btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
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
        <div class="modal-content modal-editorial">
            <div class="modal-header">
                <div>
                    <span class="modal-kicker">Caja</span>
                    <h5 class="modal-title">
                        <i class="bi bi-cash-stack me-2"></i>Registrar Cobro en Caja
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCobro" onsubmit="procesarCobro(event)">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Seleccionar cita por cobrar <span class="text-danger">*</span></label>
                        <select id="id_cita" name="id_cita" class="form-select" onchange="actualizarMontoPorCita(this)" required>
                            <option value="">-- Seleccionar Cita Pendiente --</option>
                            <?php foreach ($citasPendientes as $cp): ?>
                                <option value="<?= $cp['id'] ?>" data-monto="<?= $cp['saldo_pendiente'] ?>">
                                    Cita #<?= $cp['id'] ?> - <?= htmlspecialchars($cp['cliente_nombre']) ?> (Saldo: C$<?= number_format($cp['saldo_pendiente'], 2) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Método de pago <span class="text-danger">*</span></label>
                        <select id="tipo_pago" name="tipo_pago" class="form-select" required>
                            <option value="Efectivo">Efectivo (Caja Chica)</option>
                            <option value="Tarjeta">Tarjeta Débito / Crédito (POS)</option>
                            <option value="Transferencia">Transferencia Bancaria</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Monto a cobrar (C$) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">C$</span>
                            <input type="number" step="0.01" id="monto" name="monto" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-quiet btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spa-primary btn-sm px-4">
                        <i class="bi bi-printer me-1"></i> Procesar &amp; Emitir Recibo
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

    // Reiniciar el formulario de arqueo cada vez que se cierra el modal de cierre de caja
    const modalCerrarCajaEl = document.getElementById('modalCerrarCaja');
    if (modalCerrarCajaEl) {
        modalCerrarCajaEl.addEventListener('hidden.bs.modal', () => {
            document.getElementById('formCerrarCaja').reset();
            document.getElementById('lblResultadoDiferencia').textContent = 'Ingresa el efectivo contado';
        });
    }
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

function actualizarMontoPorCitaDirecto(select) {
    const option = select.options[select.selectedIndex];
    const monto = option.getAttribute('data-monto');
    const inputMonto = document.getElementById('directo_monto');
    if (inputMonto) {
        inputMonto.value = monto ? parseFloat(monto).toFixed(2) : '';
    }
}

function procesarCobroDirecto(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitCobroDirecto');
    const fd = new FormData(document.getElementById('formCobroDirecto'));

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Procesando...';
    }

    fetch('index.php?c=pagos&a=cobrar', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            alert('¡Pago registrado correctamente! Se actualizará la caja y el historial.');
            location.reload();
        } else {
            alert('Error: ' + res.mensaje);
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check2-circle fs-6"></i> <span>Cobrar Ahora</span>';
            }
        }
    })
    .catch(() => {
        alert('Ocurrió un error de conexión al procesar el cobro.');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2-circle fs-6"></i> <span>Cobrar Ahora</span>';
        }
    });
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
    })
    .catch(() => alert('Ocurrió un error de conexión al procesar el cobro.'));
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
    })
    .catch(() => alert('Ocurrió un error de conexión al abrir la caja.'));
}

function calcularCuadreCaja() {
    const contado = parseFloat(document.getElementById('monto_final_efectivo').value || 0);
    const dif = contado - MONTO_ESPERADO_EFECTIVO;
    const box = document.getElementById('boxCuadreCaja');
    const lbl = document.getElementById('lblResultadoDiferencia');

    if (isNaN(contado)) return;

    // Tolerancia de medio centavo: evita falsos faltantes por redondeo de punto flotante
    if (Math.abs(dif) < 0.005) {
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
    })
    .catch(() => alert('Ocurrió un error de conexión al cerrar la caja.'));
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
