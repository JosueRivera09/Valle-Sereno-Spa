<!-- Encabezado con Botón Activo -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--spa-primary); font-family: var(--font-serif);">
            <i class="bi bi-calendar-heart text-warning me-2"></i>Agenda & Citas de Spa
        </h4>
        <p class="text-muted small mb-0">Control de sesiones terapéuticas, asignación de cabinas y horarios.</p>
    </div>
    <div>
        <button class="btn btn-spa-primary btn-sm px-3 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNuevaCita">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Agendar Cita</span>
        </button>
    </div>
</div>

<!-- Alertas de Sesión -->
<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['mensaje_tipo'] ?? 'info') ?> alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <?= htmlspecialchars($_SESSION['mensaje']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['mensaje'], $_SESSION['mensaje_tipo']); ?>
<?php endif; ?>

<!-- Tabla Dinámica de Citas -->
<div class="spa-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #556b61;">
                    <th>ID</th>
                    <th>CLIENTE</th>
                    <th>TERAPEUTA</th>
                    <th>FECHA</th>
                    <th>HORARIO</th>
                    <th>TOTAL</th>
                    <th>ESTADO</th>
                    <th>OBSERVACIONES</th>
                </tr>
            </thead>
            <tbody style="font-size: 0.88rem;">
                <?php if (empty($citas)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x fs-3 d-block mb-1 text-muted"></i>
                            No hay citas registradas en la base de datos.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($citas as $c): ?>
                        <tr>
                            <td class="fw-bold text-muted">#<?= $c['id'] ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($c['cliente_nombre']) ?></div>
                            </td>
                            <td>
                                <span class="text-secondary fw-semibold">
                                    <i class="bi bi-person me-1"></i><?= htmlspecialchars($c['terapeuta_nombre']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="text-dark">
                                    <i class="bi bi-calendar3 text-warning me-1"></i><?= date('d/m/Y', strtotime($c['fecha'])) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?= date('h:i A', strtotime($c['hora_inicio'])) ?> - <?= date('h:i A', strtotime($c['hora_fin'])) ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-dark fs-6" style="font-family: var(--font-serif); color: var(--spa-primary);">
                                    $<?= number_format($c['total'], 2) ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                    $estadoClass = match($c['estado']) {
                                        'Confirmada' => 'bg-success-subtle text-success border border-success-subtle',
                                        'Pendiente'  => 'bg-warning-subtle text-warning border border-warning-subtle',
                                        'Completada' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                        'Cancelada'  => 'bg-danger-subtle text-danger border border-danger-subtle',
                                        default      => 'bg-secondary-subtle text-secondary'
                                    };
                                ?>
                                <span class="badge <?= $estadoClass ?> rounded-pill px-2 py-1">
                                    <?= htmlspecialchars($c['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= htmlspecialchars($c['observaciones'] ?? 'Sin observaciones') ?>
                                </small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Nueva Cita -->
<div class="modal fade" id="modalNuevaCita" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--spa-primary);">
                <h5 class="modal-title fw-bold" style="font-family: var(--font-serif);">
                    <i class="bi bi-calendar-plus text-warning me-2"></i> Agendar Nueva Cita
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?c=citas&a=guardar" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">ID Cliente <span class="text-danger">*</span></label>
                        <input type="number" name="id_cliente" class="form-control" placeholder="Ej. 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">ID Terapeuta/Empleado <span class="text-danger">*</span></label>
                        <input type="number" name="id_empleado" class="form-control" placeholder="Ej. 2" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Fecha <span class="text-danger">*</span></label>
                        <input type="date" name="fecha" class="form-control" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Hora Inicio <span class="text-danger">*</span></label>
                            <input type="time" name="hora_inicio" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Hora Fin <span class="text-danger">*</span></label>
                            <input type="time" name="hora_fin" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Monto Total ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="total" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Detalles de la sesión..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spa-primary btn-sm px-4">
                        <i class="bi bi-check2-circle me-1"></i> Guardar Cita
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>