<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">
                <i class="fa-solid fa-cloud-download-alt text-primary"></i>
                Descargar CFDIs desde SAT
            </h1>
            <p class="text-muted">Descarga masiva de facturas electrónicas emitidas y recibidas</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fa-solid fa-calendar-alt"></i> Selecciona el Rango de Fechas
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fecha Inicial *</label>
                                    <input type="date" name="date_from" class="form-control" 
                                           value="<?= date('Y-m-01') ?>" 
                                           max="<?= date('Y-m-d') ?>" 
                                           required>
                                    <small class="text-muted">Inicio del período a descargar</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fecha Final *</label>
                                    <input type="date" name="date_to" class="form-control" 
                                           value="<?= date('Y-m-d') ?>" 
                                           max="<?= date('Y-m-d') ?>" 
                                           required>
                                    <small class="text-muted">Fin del período a descargar</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tipo de Descarga *</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" value="recibidos" id="typeRecibidos" checked>
                                <label class="form-check-label" for="typeRecibidos">
                                    <strong>CFDIs Recibidos</strong> - Facturas que le han emitido a usted (gastos)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" value="emitidos" id="typeEmitidos">
                                <label class="form-check-label" for="typeEmitidos">
                                    <strong>CFDIs Emitidos</strong> - Facturas que usted ha emitido (ingresos)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" value="ambos" id="typeAmbos">
                                <label class="form-check-label" for="typeAmbos">
                                    <strong>Ambos</strong> - Emitidos y Recibidos
                                </label>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle"></i>
                            <strong>Importante:</strong>
                            <ul class="mb-0">
                                <li>El proceso puede tardar varios minutos dependiendo de la cantidad de facturas</li>
                                <li>Se recomienda descargar períodos no mayores a 1 mes</li>
                                <li>La descarga se ejecutará en segundo plano</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= Uri::create('admin/sat') ?>" class="btn btn-secondary">
                                <i class="fa-solid fa-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa-solid fa-download"></i> Iniciar Descarga
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <i class="fa-solid fa-lightbulb"></i> ¿Cómo funciona la descarga?
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="mb-2">
                                <i class="fa-solid fa-server fa-3x text-primary"></i>
                            </div>
                            <h6>1. Conexión al SAT</h6>
                            <p class="small text-muted">
                                Nos conectamos al portal SAT usando tus credenciales encriptadas
                            </p>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="mb-2">
                                <i class="fa-solid fa-cloud-download-alt fa-3x text-success"></i>
                            </div>
                            <h6>2. Descarga de XMLs</h6>
                            <p class="small text-muted">
                                Descargamos todos los CFDIs del rango de fechas seleccionado
                            </p>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="mb-2">
                                <i class="fa-solid fa-database fa-3x text-info"></i>
                            </div>
                            <h6>3. Almacenamiento</h6>
                            <p class="small text-muted">
                                Los CFDIs se guardan y organizan en tu base de datos
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
