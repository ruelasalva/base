<!-- Page Header -->
<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="mb-0"><i class="fas fa-cubes me-2"></i>Gestión de Módulos</h2>
        <p class="text-body-secondary">Activa o desactiva módulos según las necesidades de tu negocio</p>
    </div>
</div>

<!-- Info Alert -->
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Información:</strong> Los módulos marcados como "Core" no pueden ser desactivados ya que son esenciales para el funcionamiento del sistema.
    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
</div>

<!-- Modules by Category -->
<?php if (isset($modules_by_category) && is_array($modules_by_category) && count($modules_by_category) > 0): ?>
<?php foreach ($modules_by_category as $category => $modules): ?>
<?php if (is_array($modules) && count($modules) > 0): ?>
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <?php 
            $icons = [
                'core' => 'fa-cube',
                'business' => 'fa-briefcase',
                'sales' => 'fa-shopping-cart',
                'marketing' => 'fa-bullhorn',
                'backend' => 'fa-server',
                'system' => 'fa-cogs'
            ];
            $icon = isset($icons[$category]) ? $icons[$category] : 'fa-folder';
            ?>
            <i class="fas <?php echo $icon; ?> me-2"></i>
            <?php echo isset($category_names[$category]) ? $category_names[$category] : ucfirst($category); ?>
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <?php foreach ($modules as $module): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 <?php echo $module['is_active_for_tenant'] ? 'border-success' : 'border-secondary'; ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="mb-1">
                                    <i class="fas <?php echo htmlspecialchars($module['icon']); ?> me-1"></i>
                                    <?php echo htmlspecialchars($module['display_name']); ?>
                                </h6>
                                <?php if ($module['is_core']): ?>
                                    <span class="badge bg-primary">Core</span>
                                <?php endif; ?>
                                <?php if ($module['is_active_for_tenant']): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                                <span class="badge bg-info">v<?php echo htmlspecialchars($module['version']); ?></span>
                            </div>
                        </div>
                        
                        <p class="text-body-secondary small mb-3">
                            <?php echo htmlspecialchars($module['description']); ?>
                        </p>

                        <?php if (!empty($module['requires_modules'])): ?>
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-link me-1"></i>
                                Requiere: <?php echo htmlspecialchars($module['requires_modules']); ?>
                            </small>
                        </div>
                        <?php endif; ?>

                        <!-- Actions -->
                        <?php if ($can_enable && !$module['is_core']): ?>
                        <div class="d-grid">
                            <?php if ($module['is_active_for_tenant']): ?>
                                <button class="btn btn-outline-danger btn-sm btn-disable-module" 
                                        data-module-id="<?php echo $module['id']; ?>"
                                        data-module-name="<?php echo htmlspecialchars($module['display_name']); ?>">
                                    <i class="fas fa-times-circle me-1"></i>Desactivar
                                </button>
                            <?php else: ?>
                                <button class="btn btn-success btn-sm btn-enable-module" 
                                        data-module-id="<?php echo $module['id']; ?>"
                                        data-module-name="<?php echo htmlspecialchars($module['display_name']); ?>">
                                    <i class="fas fa-check-circle me-1"></i>Activar
                                </button>
                            <?php endif; ?>
                        </div>
                        <?php elseif ($module['is_core']): ?>
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-lock me-1"></i>Módulo esencial
                            </small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endforeach; ?>
<?php else: ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>
    No se encontraron módulos disponibles.
</div>
<?php endif; ?>

<!-- JavaScript para gestionar módulos -->
<script>
$(document).ready(function() {
    // Activar módulo
    $('.btn-enable-module').on('click', function() {
        const btn = $(this);
        const moduleId = btn.data('module-id');
        const moduleName = btn.data('module-name');
        
        if (!confirm(`¿Estás seguro de activar el módulo "${moduleName}"?`)) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Activando...');

        $.ajax({
            url: '<?php echo Uri::create('admin/modules/enable'); ?>',
            method: 'POST',
            data: { 
                module_id: moduleId,
                <?php echo \Config::get('security.csrf_token_key'); ?>: '<?php echo \Security::fetch_token(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                    btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i>Activar');
                }
            },
            error: function(xhr) {
                let message = 'Error al activar el módulo';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
                btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i>Activar');
            }
        });
    });

    // Desactivar módulo
    $('.btn-disable-module').on('click', function() {
        const btn = $(this);
        const moduleId = btn.data('module-id');
        const moduleName = btn.data('module-name');
        
        Swal.fire({
            title: '¿Desactivar módulo?',
            text: `Se desactivará "${moduleName}". Los datos no se eliminarán.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, desactivar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Desactivando...');

                $.ajax({
                    url: '<?php echo Uri::create('admin/modules/disable'); ?>',
                    method: 'POST',
                    data: { 
                        module_id: moduleId,
                        <?php echo \Config::get('security.csrf_token_key'); ?>: '<?php echo \Security::fetch_token(); ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Desactivado!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                            btn.prop('disabled', false).html('<i class="fas fa-times-circle me-1"></i>Desactivar');
                        }
                    },
                    error: function(xhr) {
                        let message = 'Error al desactivar el módulo';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message
                        });
                        btn.prop('disabled', false).html('<i class="fas fa-times-circle me-1"></i>Desactivar');
                    }
                });
            }
        });
    });
});
</script>
