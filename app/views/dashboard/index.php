<!-- Tarjetas de Estadísticas / KPIs -->
<div class="row g-3 mb-4">

    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 h-100 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">Citas de Hoy</small>
                <h3 class="fw-bold mb-0 text-dark" style="font-family: var(--font-serif);">8</h3>
                <small class="text-success" style="font-size: 0.78rem;"><i class="bi bi-check2-circle"></i> 3 confirmadas</small>
            </div>
            <div class="stat-icon" style="background: rgba(30, 61, 52, 0.1); color: var(--spa-primary);">
                <i class="bi bi-calendar2-check"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 h-100 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">Terapeutas en Turno</small>
                <h3 class="fw-bold mb-0 text-dark" style="font-family: var(--font-serif);">4</h3>
                <small class="text-primary" style="font-size: 0.78rem;"><i class="bi bi-person-check"></i> Cabinas listas</small>
            </div>
            <div class="stat-icon" style="background: rgba(197, 160, 89, 0.15); color: #997838;">
                <i class="bi bi-person-badge"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 h-100 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">Clientes Registrados</small>
                <h3 class="fw-bold mb-0 text-dark" style="font-family: var(--font-serif);">124</h3>
                <small class="text-success" style="font-size: 0.78rem;"><i class="bi bi-arrow-up-short"></i> Base activa</small>
            </div>
            <div class="stat-icon" style="background: #eef6f3; color: #3b735c;">
                <i class="bi bi-people"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="spa-card p-3 h-100 d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">Ingresos Estimados</small>
                <h3 class="fw-bold mb-0 text-dark" style="font-family: var(--font-serif);">$ 850.00</h3>
                <small class="text-muted" style="font-size: 0.78rem;"><i class="bi bi-wallet2"></i> Jornada actual</small>
            </div>
            <div class="stat-icon" style="background: #fdf5ea; color: #c5a059;">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
    </div>
</div>

<!-- Secciones Operativas Rápidas -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="spa-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" style="color: var(--spa-primary); font-family: var(--font-serif);">
                    <i class="bi bi-clock-history text-warning me-2"></i> Próximas Citas & Tratamientos
                </h5>
                <a href="index.php?c=citas&a=index" class="btn btn-sm btn-link text-decoration-none fw-semibold" style="color: var(--spa-primary);">
                    Ver agenda completa <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr style="font-size: 0.8rem; color: #556b61;">
                            <th>HORA</th>
                            <th>CLIENTE</th>
                            <th>TERAPIA / SERVICIO</th>
                            <th>TERAPEUTA</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.88rem;">
                        <tr>
                            <td><span class="badge bg-light text-dark border">09:00 AM</span></td>
                            <td class="fw-semibold">Elena Rostova</td>
                            <td>Piedras Volcánicas Calientes</td>
                            <td>Mateo Delgado</td>
                            <td><span class="badge bg-success-subtle text-success">Confirmada</span></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-light text-dark border">10:30 AM</span></td>
                            <td class="fw-semibold">Carlos Mendoza</td>
                            <td>Aromaterapia & Lavanda</td>
                            <td>Mateo Delgado</td>
                            <td><span class="badge bg-warning-subtle text-warning">Pendiente</span></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-light text-dark border">11:45 AM</span></td>
                            <td class="fw-semibold">Sofía Benítez</td>
                            <td>Circuito de Hidroterapia</td>
                            <td>Valeria Sereno</td>
                            <td><span class="badge bg-info-subtle text-info">En Curso</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="spa-card p-4 h-100">
            <h5 class="fw-bold mb-3" style="color: var(--spa-primary); font-family: var(--font-serif);">
                <i class="bi bi-compass text-warning me-2"></i> Accesos Rápidos
            </h5>
            <div class="d-flex flex-column gap-2">
                <a href="index.php?c=citas&a=index" class="btn btn-outline-secondary text-start d-flex align-items-center justify-content-between p-3 rounded-3">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-calendar-plus text-success fs-5"></i>
                        <div>
                            <div class="fw-bold text-dark small">Agendar Nueva Cita</div>
                            <div class="text-muted" style="font-size:0.75rem;">Reserva de horario y terapeuta</div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>

                <a href="index.php?c=clientes&a=index" class="btn btn-outline-secondary text-start d-flex align-items-center justify-content-between p-3 rounded-3">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-person-plus text-primary fs-5"></i>
                        <div>
                            <div class="fw-bold text-dark small">Registrar Cliente</div>
                            <div class="text-muted" style="font-size:0.75rem;">Ficha y contacto de usuario</div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>

                <a href="index.php?c=pagos&a=index" class="btn btn-outline-secondary text-start d-flex align-items-center justify-content-between p-3 rounded-3">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-receipt text-warning fs-5"></i>
                        <div>
                            <div class="fw-bold text-dark small">Cobro en Caja</div>
                            <div class="text-muted" style="font-size:0.75rem;">Emitir recibo y registrar pago</div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                    
                </a>
            </div>
        </div>
    </div>
</div>
  

