<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">
                <i class="fa-solid fa-list text-info"></i>
                CFDIs Descargados
            </h1>
            <p class="text-muted">Listado de facturas electrónicas almacenadas</p>
        </div>
        <div class="col-auto">
            <a href="<?= Uri::create('admin/sat/upload') ?>" class="btn btn-secondary">
                <i class="fa-solid fa-upload"></i> Subir XMLs
            </a>
            <a href="<?= Uri::create('admin/sat/download') ?>" class="btn btn-primary">
                <i class="fa-solid fa-cloud-download-alt"></i> Descargar del SAT
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa-solid fa-filter"></i> Filtros de Búsqueda
        </div>
        <div class="card-body">
            <form method="GET" action="<?= Uri::create('admin/sat/cfdis') ?>">
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="uuid" class="form-control" placeholder="UUID / Folio Fiscal" 
                               value="<?= Html::chars($filters['uuid'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="rfc_emisor" class="form-control" placeholder="RFC Emisor" 
                               value="<?= Html::chars($filters['rfc_emisor'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="rfc_receptor" class="form-control" placeholder="RFC Receptor" 
                               value="<?= Html::chars($filters['rfc_receptor'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="tipo_comprobante" class="form-select">
                            <option value="">Todos los tipos</option>
                            <option value="I" <?= ($filters['tipo_comprobante'] ?? '') === 'I' ? 'selected' : '' ?>>Ingreso</option>
                            <option value="E" <?= ($filters['tipo_comprobante'] ?? '') === 'E' ? 'selected' : '' ?>>Egreso</option>
                            <option value="T" <?= ($filters['tipo_comprobante'] ?? '') === 'T' ? 'selected' : '' ?>>Traslado</option>
                            <option value="N" <?= ($filters['tipo_comprobante'] ?? '') === 'N' ? 'selected' : '' ?>>Nómina</option>
                            <option value="P" <?= ($filters['tipo_comprobante'] ?? '') === 'P' ? 'selected' : '' ?>>Pago</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <select name="estado_sat" class="form-select">
                            <option value="">Estado</option>
                            <option value="vigente" <?= ($filters['estado_sat'] ?? '') === 'vigente' ? 'selected' : '' ?>>Vigente</option>
                            <option value="cancelado" <?= ($filters['estado_sat'] ?? '') === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-md-3">
                        <input type="date" name="fecha_desde" class="form-control" placeholder="Fecha Desde" 
                               value="<?= Html::chars($filters['fecha_desde'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="fecha_hasta" class="form-control" placeholder="Fecha Hasta" 
                               value="<?= Html::chars($filters['fecha_hasta'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <?php if (array_filter($filters)): ?>
                            <a href="<?= Uri::create('admin/sat/cfdis') ?>" class="btn btn-secondary w-100">
                                <i class="fa-solid fa-times"></i> Limpiar Filtros
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resultados -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>
                <i class="fa-solid fa-database"></i> 
                <?= number_format($total_count) ?> CFDIs encontrados
            </span>
            <div>
                <a href="<?= Uri::create('admin/sat/export?format=csv&' . http_build_query($filters)) ?>" class="btn btn-sm btn-success">
                    <i class="fa-solid fa-file-csv"></i> Exportar CSV
                </a>
                <a href="<?= Uri::create('admin/sat/export?format=excel&' . http_build_query($filters)) ?>" class="btn btn-sm btn-success">
                    <i class="fa-solid fa-file-excel"></i> Exportar Excel
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($cfdis)): ?>
                <div class="text-center py-5">
                    <i class="fa-solid fa-inbox fa-4x text-muted mb-3"></i>
                    <p class="text-muted">No se encontraron CFDIs con los filtros especificados</p>
                    <a href="<?= Uri::create('admin/sat/download') ?>" class="btn btn-primary">
                        <i class="fa-solid fa-download"></i> Descargar CFDIs
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 100px;">Fecha</th>
                                <th style="width: 120px;">UUID</th>
                                <th>Emisor</th>
                                <th>Receptor</th>
                                <th style="width: 80px;">Serie-Folio</th>
                                <th style="width: 50px;">Tipo</th>
                                <th style="width: 120px;" class="text-end">Subtotal</th>
                                <th style="width: 100px;" class="text-end">IVA</th>
                                <th style="width: 120px;" class="text-end">Total</th>
                                <th style="width: 80px;">Estado</th>
                                <th style="width: 100px;" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cfdis as $cfdi): ?>
                            <tr>
                                <td>
                                    <small><?= date('d/m/Y', strtotime($cfdi['fecha_emision'])) ?></small><br>
                                    <small class="text-muted"><?= date('H:i', strtotime($cfdi['fecha_emision'])) ?></small>
                                </td>
                                <td>
                                    <small class="font-monospace" title="<?= $cfdi['uuid'] ?>">
                                        <?= substr($cfdi['uuid'], 0, 8) ?>...
                                    </small>
                                </td>
                                <td>
                                    <strong><?= $cfdi['rfc_emisor'] ?></strong><br>
                                    <small class="text-muted"><?= Str::truncate($cfdi['nombre_emisor'], 30) ?></small>
                                </td>
                                <td>
                                    <strong><?= $cfdi['rfc_receptor'] ?></strong><br>
                                    <small class="text-muted"><?= Str::truncate($cfdi['nombre_receptor'], 30) ?></small>
                                </td>
                                <td>
                                    <?php if ($cfdi['serie'] || $cfdi['folio']): ?>
                                        <?= $cfdi['serie'] ?>-<?= $cfdi['folio'] ?>
                                    <?php else: ?>
                                        <small class="text-muted">N/A</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $cfdi['tipo_comprobante'] === 'I' ? 'success' : 
                                        ($cfdi['tipo_comprobante'] === 'E' ? 'danger' : 'secondary') 
                                    ?>">
                                        <?= [
                                            'I' => 'ING',
                                            'E' => 'EGR',
                                            'T' => 'TRA',
                                            'N' => 'NOM',
                                            'P' => 'PAG'
                                        ][$cfdi['tipo_comprobante']] ?? $cfdi['tipo_comprobante'] ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <small><?= $cfdi['moneda'] ?></small>
                                    $<?= number_format($cfdi['subtotal'], 2) ?>
                                </td>
                                <td class="text-end">
                                    $<?= number_format($cfdi['impuestos_trasladados'] - $cfdi['impuestos_retenidos'], 2) ?>
                                </td>
                                <td class="text-end">
                                    <strong>$<?= number_format($cfdi['total'], 2) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $cfdi['estado_sat'] === 'vigente' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($cfdi['estado_sat']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= Uri::create('admin/sat/view/' . $cfdi['id']) ?>" 
                                           class="btn btn-info" title="Ver detalle">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="<?= Uri::create('admin/sat/validate/' . $cfdi['id']) ?>" 
                                           class="btn btn-warning" title="Validar SAT">
                                            <i class="fa-solid fa-check"></i>
                                        </a>
                                        <button type="button" class="btn btn-success" title="Descargar XML" 
                                                onclick="downloadXML(<?= $cfdi['id'] ?>)">
                                            <i class="fa-solid fa-download"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <?php if ($total_pages > 1): ?>
                <div class="card-footer">
                    <nav>
                        <ul class="pagination pagination-sm mb-0 justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="<?= Uri::create('admin/sat/cfdis?page=' . $i . '&' . http_build_query($filters)) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    <div class="text-center text-muted small mt-2">
                        Página <?= $page ?> de <?= $total_pages ?> 
                        (mostrando <?= count($cfdis) ?> de <?= number_format($total_count) ?> registros)
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function downloadXML(cfdiId) {
    // TODO: Implementar descarga de XML
    window.location.href = '<?= Uri::create("admin/sat/download_xml/") ?>' + cfdiId;
}
</script>
