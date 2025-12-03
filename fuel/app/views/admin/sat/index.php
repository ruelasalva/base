<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">
                <i class="fa-solid fa-file-invoice-dollar text-primary"></i>
                SAT - Dashboard Fiscal
            </h1>
            <p class="text-muted">Gestión de facturas electrónicas y cumplimiento fiscal</p>
        </div>
    </div>

    <?php if (empty($credentials)): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-exclamation-triangle"></i>
        <strong>Configura tus credenciales SAT</strong> 
        <p class="mb-0">Para comenzar a descargar facturas, primero debes configurar tu RFC y contraseña del portal SAT.</p>
        <a href="<?= Uri::create('admin/sat/credentials') ?>" class="btn btn-warning btn-sm mt-2">
            <i class="fa-solid fa-key"></i> Configurar Credenciales
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php else: ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-check-circle"></i>
        <strong>Credenciales configuradas:</strong> RFC <code><?= $credentials['rfc'] ?></code>
        <small class="d-block mt-1">
            Última conexión: <?= $credentials['last_connection'] ? date('d/m/Y H:i', strtotime($credentials['last_connection'])) : 'Nunca' ?>
        </small>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Estadísticas Principales -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Total CFDIs</div>
                            <h2 class="mb-0"><?= number_format($stats['total_cfdis'] ?? 0) ?></h2>
                        </div>
                        <div class="fs-1 text-primary">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Vigentes</div>
                            <h2 class="mb-0 text-success"><?= number_format($stats['by_status']['vigente'] ?? 0) ?></h2>
                        </div>
                        <div class="fs-1 text-success">
                            <i class="fa-solid fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Cancelados</div>
                            <h2 class="mb-0 text-danger"><?= number_format($stats['by_status']['cancelado'] ?? 0) ?></h2>
                        </div>
                        <div class="fs-1 text-danger">
                            <i class="fa-solid fa-times-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Sin Procesar</div>
                            <h2 class="mb-0 text-info"><?= number_format($stats['by_status']['no_encontrado'] ?? 0) ?></h2>
                        </div>
                        <div class="fs-1 text-info">
                            <i class="fa-solid fa-question-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fa-solid fa-bolt"></i> Acciones Rápidas
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="<?= Uri::create('admin/sat/download') ?>" class="btn btn-primary w-100 py-3">
                                <i class="fa-solid fa-cloud-download-alt fa-2x d-block mb-2"></i>
                                <span>Descargar CFDIs</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= Uri::create('admin/sat/upload') ?>" class="btn btn-secondary w-100 py-3">
                                <i class="fa-solid fa-upload fa-2x d-block mb-2"></i>
                                <span>Subir XMLs</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= Uri::create('admin/sat/cfdis') ?>" class="btn btn-info w-100 py-3">
                                <i class="fa-solid fa-list fa-2x d-block mb-2"></i>
                                <span>Ver CFDIs</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= Uri::create('admin/sat/reports') ?>" class="btn btn-success w-100 py-3">
                                <i class="fa-solid fa-chart-bar fa-2x d-block mb-2"></i>
                                <span>Reportes</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CFDIs por Tipo -->
    <?php if (!empty($stats['by_type'])): ?>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-chart-pie"></i> CFDIs por Tipo
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th class="text-end">Cantidad</th>
                                    <th class="text-end">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['by_type'] as $type => $count): ?>
                                <tr>
                                    <td><strong><?= $type ?></strong></td>
                                    <td class="text-end"><?= number_format($count) ?></td>
                                    <td class="text-end">
                                        <?= number_format(($count / $stats['total_cfdis']) * 100, 1) ?>%
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimas Descargas -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-history"></i> Últimas Descargas
                </div>
                <div class="card-body">
                    <?php if (empty($recent_downloads)): ?>
                        <p class="text-muted text-center py-3">
                            <i class="fa-solid fa-info-circle"></i> No hay descargas registradas
                        </p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_downloads as $download): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-<?= $download['status'] === 'completed' ? 'success' : ($download['status'] === 'failed' ? 'danger' : 'warning') ?>">
                                            <?= ucfirst($download['status']) ?>
                                        </span>
                                        <span class="ms-2">
                                            <?= date('d/m/Y', strtotime($download['date_from'])) ?> - 
                                            <?= date('d/m/Y', strtotime($download['date_to'])) ?>
                                        </span>
                                    </div>
                                    <div class="text-muted small">
                                        <?= $download['total_downloaded'] ?> CFDIs
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Últimos CFDIs -->
    <?php if (!empty($recent_cfdis)): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-file-invoice"></i> Últimos CFDIs Descargados
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>UUID</th>
                                    <th>Fecha</th>
                                    <th>Emisor</th>
                                    <th>Receptor</th>
                                    <th>Tipo</th>
                                    <th class="text-end">Total</th>
                                    <th>Estado</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_cfdis as $cfdi): ?>
                                <tr>
                                    <td>
                                        <small class="font-monospace"><?= substr($cfdi['uuid'], 0, 8) ?>...</small>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($cfdi['fecha_emision'])) ?></td>
                                    <td>
                                        <small><?= $cfdi['rfc_emisor'] ?></small><br>
                                        <small class="text-muted"><?= Str::truncate($cfdi['nombre_emisor'], 25) ?></small>
                                    </td>
                                    <td>
                                        <small><?= $cfdi['rfc_receptor'] ?></small><br>
                                        <small class="text-muted"><?= Str::truncate($cfdi['nombre_receptor'], 25) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= $cfdi['tipo_comprobante'] ?></span>
                                    </td>
                                    <td class="text-end">
                                        $<?= number_format($cfdi['total'], 2) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $cfdi['estado_sat'] === 'vigente' ? 'success' : 'danger' ?>">
                                            <?= ucfirst($cfdi['estado_sat']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= Uri::create('admin/sat/view/' . $cfdi['id']) ?>" class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <a href="<?= Uri::create('admin/sat/cfdis') ?>" class="btn btn-primary">
                            Ver Todos los CFDIs <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
