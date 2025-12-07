<!-- Page Header -->
<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="mb-0"><i class="fa-solid fa-puzzle-piece me-2"></i>Gestión de Módulos</h2>
        <p class="text-body-secondary">Activa o desactiva módulos según las necesidades de tu empresa</p>
    </div>
</div>

<!-- Info Alert -->
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="fa-solid fa-circle-info me-2"></i>
    <strong>Información:</strong> Los módulos del núcleo del sistema no pueden ser desactivados. Los módulos con registros existentes tampoco pueden desactivarse para proteger tus datos.
    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
</div>

<!-- Modules by Category -->
<?php if (isset($grouped_modules) && isset($ordered_categories)): ?>
    <?php foreach ($ordered_categories as $category): ?>
        <?php if (isset($grouped_modules[$category]) && count($grouped_modules[$category]) > 0): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-gradient">
                <h5 class="mb-0">
                    <i class="fa-solid <?php echo $category_icons[$category]; ?> me-2 text-primary"></i>
                    <?php echo $category_names[$category]; ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ($grouped_modules[$category] as $module): ?>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="card h-100 module-card <?php echo $module['is_tenant_active'] ? 'border-success' : 'border-secondary'; ?> hover-shadow">
                            <div class="card-body d-flex flex-column">
                                <!-- Header con icono y estado -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="module-icon-wrapper">
                                        <i class="fa-solid <?php echo htmlspecialchars($module['icon']); ?> fa-2x text-<?php echo $module['is_tenant_active'] ? 'success' : 'muted'; ?>"></i>
                                    </div>
                                    <div>
                                        <?php if ($module['is_core'] == 1): ?>
                                            <span class="badge bg-primary" title="Módulo del núcleo del sistema">
                                                <i class="fa-solid fa-lock me-1"></i>Core
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($module['is_tenant_active']): ?>
                                            <span class="badge bg-success">
                                                <i class="fa-solid fa-circle-check me-1"></i>Activo
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">
                                                <i class="fa-solid fa-circle-xmark me-1"></i>Inactivo
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Nombre y descripción -->
                                <h6 class="mb-2 fw-bold"><?php echo htmlspecialchars($module['display_name']); ?></h6>
                                <p class="text-body-secondary small mb-3 flex-grow-1">
                                    <?php echo htmlspecialchars($module['description']); ?>
                                </p>

                                <!-- Información adicional si está activo -->
                                <?php if ($module['is_tenant_active'] && !empty($module['activated_at'])): ?>
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fa-solid fa-calendar me-1"></i>
                                        Activado: <?php echo date('d/m/Y', strtotime($module['activated_at'])); ?>
                                    </small>
                                </div>
                                <?php endif; ?>

                                <!-- Botones de acción -->
                                <div class="mt-auto">
                                    <div class="d-flex gap-2 mb-2">
                                        <?php if ($is_super_admin): ?>
                                        <!-- Botón editar (solo super admin) -->
                                        <a href="<?php echo Uri::create('admin/modules/editar/' . $module['id']); ?>" 
                                           class="btn btn-outline-primary btn-sm flex-grow-1"
                                           title="Editar módulo">
                                            <i class="fa-solid fa-edit me-1"></i>Editar
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($can_activate): ?>
                                        <?php if ($module['is_core'] == 1): ?>
                                            <!-- Módulo core: no se puede desactivar -->
                                            <button class="btn btn-outline-secondary btn-sm w-100" disabled>
                                                <i class="fa-solid fa-lock me-1"></i>
                                                Módulo Esencial
                                            </button>
                                        <?php elseif ($module['is_tenant_active']): ?>
                                            <!-- Módulo activo: botón para desactivar -->
                                            <button class="btn btn-outline-danger btn-sm w-100 btn-toggle-module" 
                                                    data-module-id="<?php echo $module['id']; ?>"
                                                    data-module-name="<?php echo htmlspecialchars($module['display_name']); ?>"
                                                    data-action="deactivate">
                                                <i class="fa-solid fa-toggle-off me-1"></i>
                                                Desactivar
                                            </button>
                                        <?php else: ?>
                                            <!-- Módulo inactivo: botón para activar -->
                                            <button class="btn btn-success btn-sm w-100 btn-toggle-module" 
                                                    data-module-id="<?php echo $module['id']; ?>"
                                                    data-module-name="<?php echo htmlspecialchars($module['display_name']); ?>"
                                                    data-action="activate">
                                                <i class="fa-solid fa-toggle-on me-1"></i>
                                                Activar
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <small class="text-muted text-center d-block">
                                            <i class="fa-solid fa-ban me-1"></i>
                                            Sin permisos para modificar
                                        </small>
                                    <?php endif; ?>
                                </div>
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
    <i class="fa-solid fa-triangle-exclamation me-2"></i>
    No se encontraron módulos disponibles.
</div>
<?php endif; ?>

<!-- CSS personalizado -->
<style>
.module-card {
    transition: all 0.3s ease;
    border-width: 2px;
}

.module-card:hover {
    transform: translateY(-5px);
}

.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}

.module-icon-wrapper {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.03);
    border-radius: 10px;
}

.bg-gradient {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}
</style>

<!-- JavaScript para gestionar módulos -->
<script>
// Usar jQuery cuando esté disponible
$(document).ready(function() {
    console.log('Módulos JS cargado - Botones encontrados:', $('.btn-toggle-module').length);
    
    // Verificar si SweetAlert2 está disponible
    if (typeof Swal === 'undefined') {
        console.error('ERROR: SweetAlert2 no está cargado');
        alert('Error: La librería SweetAlert2 no está disponible. Por favor recarga la página.');
        return;
    }
    
    // Manejar activación/desactivación de módulos
    $('.btn-toggle-module').on('click', function(e) {
        e.preventDefault();
        console.log('Botón clickeado');
        
        const btn = $(this);
        const moduleId = btn.data('module-id');
        const moduleName = btn.data('module-name');
        const action = btn.data('action');
        
        console.log('Datos del módulo:', {moduleId, moduleName, action});
        
        // Texto y colores según la acción
        const actionTexts = {
            activate: {
                title: '¿Activar módulo?',
                text: `Se activará el módulo "${moduleName}"`,
                confirmText: 'Sí, activar',
                confirmColor: '#198754',
                processingText: '<i class="fa-solid fa-spinner fa-spin me-1"></i>Activando...'
            },
            deactivate: {
                title: '¿Desactivar módulo?',
                text: `Se desactivará el módulo "${moduleName}". Si existen registros relacionados, no podrá desactivarse.`,
                confirmText: 'Sí, desactivar',
                confirmColor: '#dc3545',
                processingText: '<i class="fa-solid fa-spinner fa-spin me-1"></i>Desactivando...'
            }
        };

        const texts = actionTexts[action];

        Swal.fire({
            title: texts.title,
            text: texts.text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: texts.confirmColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: texts.confirmText,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            console.log('Modal result:', result);
            
            if (result.isConfirmed) {
                // Deshabilitar botón y mostrar spinner
                const originalHtml = btn.html();
                btn.prop('disabled', true).html(texts.processingText);
                
                const ajaxUrl = '<?php echo Uri::create('admin/modules/toggle'); ?>';
                const csrfKey = '<?php echo \Config::get('security.csrf_token_key'); ?>';
                const csrfToken = '<?php echo \Security::fetch_token(); ?>';
                
                console.log('Enviando AJAX:', {url: ajaxUrl, moduleId, action, csrfKey, csrfToken});

                $.ajax({
                    url: ajaxUrl,
                    method: 'POST',
                    data: { 
                        module_id: moduleId,
                        action: action,
                        [csrfKey]: csrfToken
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Respuesta exitosa:', response);
                        
                        if (response.success) {
                            // Mostrar mensaje de éxito y recargar
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            // Mostrar mensaje de error
                            let errorHtml = response.message;
                            
                            // Si hay detalles de uso, mostrarlos
                            if (response.details && response.details.length > 0) {
                                errorHtml += '<br><br><strong>Registros existentes:</strong><ul class="text-start">';
                                response.details.forEach(function(detail) {
                                    errorHtml += `<li>${detail.table}: ${detail.count} registro(s)</li>`;
                                });
                                errorHtml += '</ul>';
                            }

                            Swal.fire({
                                icon: 'warning',
                                title: 'No se puede desactivar',
                                html: errorHtml
                            });
                            
                            // Restaurar botón
                            btn.prop('disabled', false).html(originalHtml);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error AJAX:', {xhr, status, error});
                        console.error('Response:', xhr.responseText);
                        
                        let message = 'Error al procesar la solicitud';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.status === 404) {
                            message = 'Ruta no encontrada (404). Verifica que el controlador existe.';
                        } else if (xhr.status === 403) {
                            message = 'Acceso denegado. Verifica el CSRF token.';
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message
                        });
                        
                        // Restaurar botón
                        btn.prop('disabled', false).html(originalHtml);
                    }
                });
            }
        });
    }); // Fin evento click
}); // Fin $(document).ready
</script>
