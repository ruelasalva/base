<!-- Page Header -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">
                    <i class="fa-solid fa-warehouse me-2"></i>Gestión de Almacenes
                </h2>
                <p class="text-body-secondary mb-0">
                    Administra los almacenes y sus ubicaciones
                </p>
            </div>
            <?php if ($can_create): ?>
            <a href="<?php echo Uri::create('admin/almacenes/crear'); ?>" class="btn btn-primary">
                <i class="fa-solid fa-plus me-2"></i>Nuevo Almacén
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Estadísticas rápidas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-warehouse fa-2x text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">Total Almacenes</h6>
                        <h3 class="mb-0"><?php echo count($almacenes); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-check-circle fa-2x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">Activos</h6>
                        <h3 class="mb-0">
                            <?php 
                            $activos = array_filter($almacenes, function($a) { return $a['is_active'] == 1; });
                            echo count($activos);
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-building fa-2x text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">Principales</h6>
                        <h3 class="mb-0">
                            <?php 
                            $principales = array_filter($almacenes, function($a) { return $a['type'] == 'principal'; });
                            echo count($principales);
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-map-marked-alt fa-2x text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">Total Ubicaciones</h6>
                        <h3 class="mb-0">
                            <?php 
                            $total_ubicaciones = array_sum(array_column($almacenes, 'locations_count'));
                            echo $total_ubicaciones;
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Almacenes -->
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tabla-almacenes">
                <thead>
                    <tr>
                        <th width="8%">Código</th>
                        <th width="20%">Nombre</th>
                        <th width="10%">Tipo</th>
                        <th width="15%">Ciudad</th>
                        <th width="15%">Responsable</th>
                        <th width="10%" class="text-center">Ubicaciones</th>
                        <th width="8%" class="text-center">Estado</th>
                        <th width="14%" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($almacenes)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fa-solid fa-inbox fa-3x mb-3 d-block"></i>
                            No hay almacenes registrados
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($almacenes as $almacen): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($almacen['code']); ?></strong>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($almacen['name']); ?>
                                <?php if (!empty($almacen['description'])): ?>
                                <br><small class="text-muted"><?php echo htmlspecialchars($almacen['description']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $type_badges = [
                                    'principal' => '<span class="badge bg-primary">Principal</span>',
                                    'secundario' => '<span class="badge bg-info">Secundario</span>',
                                    'transito' => '<span class="badge bg-warning">Tránsito</span>',
                                    'virtual' => '<span class="badge bg-secondary">Virtual</span>'
                                ];
                                echo $type_badges[$almacen['type']] ?? $almacen['type'];
                                ?>
                            </td>
                            <td>
                                <?php if (!empty($almacen['city'])): ?>
                                    <?php echo htmlspecialchars($almacen['city']); ?>
                                    <?php if (!empty($almacen['state'])): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($almacen['state']); ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($almacen['manager_name'])): ?>
                                    <i class="fa-solid fa-user me-1"></i><?php echo htmlspecialchars($almacen['manager_name']); ?>
                                <?php else: ?>
                                    <span class="text-muted">Sin asignar</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?php echo Uri::create('admin/almacenes/ubicaciones/' . $almacen['id']); ?>" 
                                   class="btn btn-sm btn-outline-info"
                                   title="Ver ubicaciones">
                                    <i class="fa-solid fa-map-marker-alt me-1"></i>
                                    <?php echo $almacen['locations_count']; ?>
                                </a>
                            </td>
                            <td class="text-center">
                                <?php if ($almacen['is_active']): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <?php if ($can_edit): ?>
                                    <a href="<?php echo Uri::create('admin/almacenes/editar/' . $almacen['id']); ?>" 
                                       class="btn btn-sm btn-outline-primary"
                                       title="Editar">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($can_delete): ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger btn-delete"
                                            data-id="<?php echo $almacen['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($almacen['name']); ?>"
                                            title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
$(document).ready(function() {
    // DataTable
    $('#tabla-almacenes').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json'
        },
        order: [[0, 'asc']],
        pageLength: 25
    });

    // Eliminar
    $('.btn-delete').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            title: '¿Eliminar almacén?',
            html: `Se eliminará el almacén <strong>${name}</strong> y todas sus ubicaciones.<br><br>
                   <span class="text-danger"><i class="fa-solid fa-exclamation-triangle me-2"></i>
                   Esta acción no se puede deshacer</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?php echo Uri::create('admin/almacenes/eliminar/'); ?>' + id;
            }
        });
    });
});
</script>
