<!-- Page Header -->
<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="mb-0"><i class="fas fa-palette me-2"></i>Seleccionar Template</h2>
        <p class="text-body-secondary">Elige el diseño que más te guste para tu panel de administración</p>
    </div>
</div>

<!-- Templates Grid -->
<div class="row g-4">
    <?php if (isset($templates) && is_array($templates) && count($templates) > 0): ?>
    <?php foreach ($templates as $key => $template): ?>
    <div class="col-md-4">
        <div class="card h-100 <?php echo ($current_template == $key) ? 'border-primary border-3' : ''; ?>">
            <div class="card-body text-center">
                <?php if ($current_template == $key): ?>
                <div class="position-absolute top-0 end-0 m-3">
                    <span class="badge bg-success">Activo</span>
                </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <h4 class="mb-1"><?php echo htmlspecialchars($template['name']); ?></h4>
                    <p class="text-muted small"><?php echo htmlspecialchars($template['description']); ?></p>
                </div>

                <!-- Preview Image -->
                <div class="mb-3">
                    <div class="border rounded p-3 bg-light" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-desktop fa-5x text-secondary"></i>
                        <div class="position-absolute">
                            <small class="text-muted">Vista previa</small>
                        </div>
                    </div>
                </div>

                <!-- Características -->
                <div class="text-start mb-3">
                    <ul class="list-unstyled small">
                        <?php if ($key == 'coreui'): ?>
                            <li><i class="fas fa-check text-success me-2"></i>Moderno y minimalista</li>
                            <li><i class="fas fa-check text-success me-2"></i>Carga rápida</li>
                            <li><i class="fas fa-check text-success me-2"></i>Responsive</li>
                        <?php elseif ($key == 'adminlte'): ?>
                            <li><i class="fas fa-check text-success me-2"></i>Clásico y probado</li>
                            <li><i class="fas fa-check text-success me-2"></i>Rico en componentes</li>
                            <li><i class="fas fa-check text-success me-2"></i>Muy popular</li>
                        <?php else: ?>
                            <li><i class="fas fa-check text-success me-2"></i>Hermoso diseño</li>
                            <li><i class="fas fa-check text-success me-2"></i>Degradados modernos</li>
                            <li><i class="fas fa-check text-success me-2"></i>Elegante</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Action Button -->
                <?php if ($current_template == $key): ?>
                    <button class="btn btn-success w-100" disabled>
                        <i class="fas fa-check-circle me-1"></i>En Uso
                    </button>
                <?php else: ?>
                    <button class="btn btn-primary w-100 btn-select-template" 
                            data-template="<?php echo $key; ?>"
                            data-template-name="<?php echo htmlspecialchars($template['name']); ?>">
                        <i class="fas fa-paint-brush me-1"></i>Aplicar
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php else: ?>
    <div class="col-12">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No hay templates disponibles.
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Info Alert -->
<div class="alert alert-info mt-4">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Nota:</strong> El cambio de template es instantáneo y solo afecta a tu usuario. 
    Otros usuarios del sistema pueden elegir su propio template preferido.
</div>

<!-- JavaScript -->
<script>
$(document).ready(function() {
    $('.btn-select-template').on('click', function() {
        const btn = $(this);
        const template = btn.data('template');
        const templateName = btn.data('template-name');

        Swal.fire({
            title: '¿Cambiar template?',
            text: `Se aplicará el template ${templateName}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Aplicando...');

                $.ajax({
                    url: '<?php echo Uri::create('admin/configuracion/set_template'); ?>',
                    method: 'POST',
                    data: {
                        template: template,
                        <?php echo \Config::get('security.csrf_token_key'); ?>: '<?php echo \Security::fetch_token(); ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Template cambiado!',
                                text: 'Recargando página...',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'No se pudo cambiar el template'
                            });
                            btn.prop('disabled', false).html('<i class="fas fa-paint-brush me-1"></i>Aplicar');
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al cambiar el template'
                        });
                        btn.prop('disabled', false).html('<i class="fas fa-paint-brush me-1"></i>Aplicar');
                    }
                });
            }
        });
    });
});
</script>
