<!-- Page Header -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">
                    <i class="fa-solid fa-edit me-2"></i>Editar Módulo
                </h2>
                <p class="text-body-secondary mb-0">
                    Modificar información básica del módulo <strong><?php echo htmlspecialchars($module['display_name']); ?></strong>
                </p>
            </div>
            <a href="<?php echo Uri::create('admin/modules'); ?>" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>
</div>

<!-- Alert de advertencia -->
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fa-solid fa-triangle-exclamation me-2"></i>
    <strong>Advertencia:</strong> Modificar la información de módulos del sistema puede afectar su funcionamiento. Solo modifica si sabes lo que estás haciendo.
    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
</div>

<!-- Formulario de Edición -->
<form method="POST" action="<?php echo Uri::current(); ?>">
    <?php echo Form::csrf(); ?>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-info-circle me-2"></i>Información Básica
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Nombre Interno (solo lectura) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Nombre Interno (slug)
                            <i class="fa-solid fa-circle-info text-muted" 
                               title="Este campo no se puede modificar"></i>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($module['name']); ?>" 
                               disabled>
                        <small class="text-muted">
                            Este es el identificador único del módulo en el código
                        </small>
                    </div>

                    <!-- Nombre Visible -->
                    <div class="mb-3">
                        <label for="display_name" class="form-label fw-bold">
                            Nombre Visible <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="display_name" 
                               name="display_name" 
                               value="<?php echo htmlspecialchars($module['display_name']); ?>" 
                               required
                               maxlength="255">
                        <small class="text-muted">
                            Nombre que se mostrará en el menú y en la interfaz
                        </small>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">
                            Descripción
                        </label>
                        <textarea class="form-control" 
                                  id="description" 
                                  name="description" 
                                  rows="3"><?php echo htmlspecialchars($module['description']); ?></textarea>
                        <small class="text-muted">
                            Breve descripción de la funcionalidad del módulo
                        </small>
                    </div>

                    <!-- Ícono -->
                    <div class="mb-3">
                        <label for="icon" class="form-label fw-bold">
                            Ícono (FontAwesome)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa-solid <?php echo htmlspecialchars($module['icon']); ?>" id="icon-preview"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   id="icon" 
                                   name="icon" 
                                   value="<?php echo htmlspecialchars($module['icon']); ?>" 
                                   placeholder="fa-puzzle-piece">
                        </div>
                        <small class="text-muted">
                            Clase de ícono de FontAwesome (ej: fa-users, fa-chart-bar)
                            <a href="https://fontawesome.com/icons" target="_blank" class="ms-2">
                                <i class="fa-solid fa-external-link"></i>Ver iconos disponibles
                            </a>
                        </small>
                    </div>

                    <!-- Categoría -->
                    <div class="mb-3">
                        <label for="category" class="form-label fw-bold">
                            Categoría <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="core" <?php echo ($module['category'] == 'core') ? 'selected' : ''; ?>>
                                Núcleo del Sistema
                            </option>
                            <option value="contabilidad" <?php echo ($module['category'] == 'contabilidad') ? 'selected' : ''; ?>>
                                Contabilidad
                            </option>
                            <option value="finanzas" <?php echo ($module['category'] == 'finanzas') ? 'selected' : ''; ?>>
                                Finanzas
                            </option>
                            <option value="compras" <?php echo ($module['category'] == 'compras') ? 'selected' : ''; ?>>
                                Compras
                            </option>
                            <option value="inventario" <?php echo ($module['category'] == 'inventario') ? 'selected' : ''; ?>>
                                Inventario
                            </option>
                            <option value="sales" <?php echo ($module['category'] == 'sales') ? 'selected' : ''; ?>>
                                Ventas
                            </option>
                            <option value="rrhh" <?php echo ($module['category'] == 'rrhh') ? 'selected' : ''; ?>>
                                Recursos Humanos
                            </option>
                            <option value="marketing" <?php echo ($module['category'] == 'marketing') ? 'selected' : ''; ?>>
                                Marketing
                            </option>
                            <option value="backend" <?php echo ($module['category'] == 'backend') ? 'selected' : ''; ?>>
                                Backend/Integraciones
                            </option>
                            <option value="system" <?php echo ($module['category'] == 'system') ? 'selected' : ''; ?>>
                                Sistema
                            </option>
                        </select>
                    </div>

                    <!-- Orden -->
                    <div class="mb-3">
                        <label for="menu_order" class="form-label fw-bold">
                            Orden de Visualización
                        </label>
                        <input type="number" 
                               class="form-control" 
                               id="menu_order" 
                               name="menu_order" 
                               value="<?php echo htmlspecialchars($module['menu_order']); ?>" 
                               min="1" 
                               max="9999">
                        <small class="text-muted">
                            Número que determina el orden de aparición (menor = primero)
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar con información adicional -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-toggle-on me-2"></i>Estado
                    </h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="is_enabled" 
                               name="is_enabled" 
                               value="1"
                               <?php echo ($module['is_enabled']) ? 'checked' : ''; ?>>
                        <label class="form-check-label fw-bold" for="is_enabled">
                            Módulo Activo
                        </label>
                    </div>
                    <small class="text-muted d-block">
                        Si está inactivo, el módulo no aparecerá en ningún menú ni estará disponible para los tenants
                    </small>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-info-circle me-2"></i>Información
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt class="text-muted small">ID del Módulo</dt>
                        <dd class="mb-2"><code><?php echo $module['id']; ?></code></dd>

                        <dt class="text-muted small">Puede Desactivarse</dt>
                        <dd class="mb-2">
                            <?php if ($module['is_core'] == 0): ?>
                                <span class="badge bg-success">Sí</span>
                            <?php else: ?>
                                <span class="badge bg-danger">No (Core)</span>
                            <?php endif; ?>
                        </dd>

                        <?php if (!empty($module['requires_modules'])): ?>
                        <dt class="text-muted small">Módulos Requeridos</dt>
                        <dd class="mb-2">
                            <?php 
                            $requires = json_decode($module['requires_modules'], true);
                            if (is_array($requires) && count($requires) > 0) {
                                echo '<ul class="mb-0 ps-3">';
                                foreach ($requires as $req) {
                                    echo '<li><code>' . htmlspecialchars($req) . '</code></li>';
                                }
                                echo '</ul>';
                            } else {
                                echo '<span class="text-muted">Ninguno</span>';
                            }
                            ?>
                        </dd>
                        <?php endif; ?>

                        <dt class="text-muted small">Creado</dt>
                        <dd class="mb-2">
                            <?php echo date('d/m/Y H:i', strtotime($module['created_at'])); ?>
                        </dd>

                        <?php if (!empty($module['updated_at'])): ?>
                        <dt class="text-muted small">Última Actualización</dt>
                        <dd class="mb-0">
                            <?php echo date('d/m/Y H:i', strtotime($module['updated_at'])); ?>
                        </dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fa-solid fa-save me-2"></i>Guardar Cambios
                </button>
                <a href="<?php echo Uri::create('admin/modules'); ?>" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-times me-2"></i>Cancelar
                </a>
            </div>
        </div>
    </div>
</form>

<!-- JavaScript para preview del ícono -->
<script>
$(document).ready(function() {
    // Preview de ícono en tiempo real
    $('#icon').on('input', function() {
        const iconClass = $(this).val();
        $('#icon-preview').attr('class', 'fa-solid ' + iconClass);
    });
});
</script>

<style>
.hover-info:hover {
    background-color: #f8f9fa;
    cursor: help;
}
</style>
