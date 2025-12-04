<!-- Page Header -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">
                    <i class="fa-solid fa-edit me-2"></i>Editar Almacén
                </h2>
                <p class="text-body-secondary mb-0">
                    Modificar información de <strong><?php echo htmlspecialchars($almacen['name']); ?></strong>
                </p>
            </div>
            <a href="<?php echo Uri::create('admin/almacenes'); ?>" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>
</div>

<!-- Formulario -->
<form method="POST" action="<?php echo Uri::current(); ?>">
    <?php echo Form::csrf(); ?>
    
    <div class="row">
        <!-- Columna Principal -->
        <div class="col-lg-8">
            <!-- Información Básica -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fa-solid fa-info-circle me-2"></i>Información Básica</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="code" class="form-label fw-bold">Código</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($almacen['code']); ?>" 
                                   disabled>
                            <small class="text-muted">El código no se puede modificar</small>
                        </div>
                        
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label fw-bold">
                                Nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   value="<?php echo htmlspecialchars($almacen['name']); ?>"
                                   required
                                   maxlength="255">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Descripción</label>
                        <textarea class="form-control" 
                                  id="description" 
                                  name="description" 
                                  rows="3"><?php echo htmlspecialchars($almacen['description']); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label fw-bold">
                                Tipo <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="principal" <?php echo ($almacen['type'] == 'principal') ? 'selected' : ''; ?>>Principal</option>
                                <option value="secundario" <?php echo ($almacen['type'] == 'secundario') ? 'selected' : ''; ?>>Secundario</option>
                                <option value="transito" <?php echo ($almacen['type'] == 'transito') ? 'selected' : ''; ?>>Tránsito</option>
                                <option value="virtual" <?php echo ($almacen['type'] == 'virtual') ? 'selected' : ''; ?>>Virtual</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="manager_user_id" class="form-label fw-bold">Responsable</label>
                            <select class="form-select" id="manager_user_id" name="manager_user_id">
                                <option value="">Sin asignar</option>
                                <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>"
                                        <?php echo ($almacen['manager_user_id'] == $user['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['username']); ?> 
                                    (<?php echo htmlspecialchars($user['email']); ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ubicación Física -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fa-solid fa-map-marker-alt me-2"></i>Ubicación Física</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="address" class="form-label fw-bold">Dirección</label>
                        <input type="text" 
                               class="form-control" 
                               id="address" 
                               name="address"
                               value="<?php echo htmlspecialchars($almacen['address']); ?>" 
                               maxlength="500">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label fw-bold">Ciudad</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="city" 
                                   name="city"
                                   value="<?php echo htmlspecialchars($almacen['city']); ?>" 
                                   maxlength="100">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="state" class="form-label fw-bold">Estado</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="state" 
                                   name="state"
                                   value="<?php echo htmlspecialchars($almacen['state']); ?>" 
                                   maxlength="100">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label fw-bold">País</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="country" 
                                   name="country"
                                   value="<?php echo htmlspecialchars($almacen['country'] ?: 'México'); ?>" 
                                   maxlength="100">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="postal_code" class="form-label fw-bold">Código Postal</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="postal_code" 
                                   name="postal_code"
                                   value="<?php echo htmlspecialchars($almacen['postal_code']); ?>" 
                                   maxlength="20">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="phone" class="form-label fw-bold">Teléfono</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="phone" 
                                   name="phone"
                                   value="<?php echo htmlspecialchars($almacen['phone']); ?>" 
                                   maxlength="50">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Estado -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fa-solid fa-toggle-on me-2"></i>Estado</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1"
                               <?php echo ($almacen['is_active']) ? 'checked' : ''; ?>>
                        <label class="form-check-label fw-bold" for="is_active">
                            Almacén Activo
                        </label>
                    </div>
                </div>
            </div>

            <!-- Capacidad -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fa-solid fa-ruler-combined me-2"></i>Capacidad</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="capacity_m2" class="form-label fw-bold">
                            Metros Cuadrados (m²)
                        </label>
                        <input type="number" 
                               class="form-control" 
                               id="capacity_m2" 
                               name="capacity_m2"
                               value="<?php echo $almacen['capacity_m2']; ?>" 
                               step="0.01"
                               min="0">
                    </div>

                    <div class="mb-3">
                        <label for="capacity_units" class="form-label fw-bold">
                            Unidades Estimadas
                        </label>
                        <input type="number" 
                               class="form-control" 
                               id="capacity_units" 
                               name="capacity_units"
                               value="<?php echo $almacen['capacity_units']; ?>" 
                               min="0">
                    </div>
                </div>
            </div>

            <!-- Notas -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fa-solid fa-sticky-note me-2"></i>Notas</h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control" 
                              id="notes" 
                              name="notes" 
                              rows="4"><?php echo htmlspecialchars($almacen['notes']); ?></textarea>
                </div>
            </div>

            <!-- Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fa-solid fa-info-circle me-2"></i>Información</h5>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt class="text-muted small">ID</dt>
                        <dd class="mb-2"><code><?php echo $almacen['id']; ?></code></dd>

                        <dt class="text-muted small">Creado</dt>
                        <dd class="mb-0"><?php echo date('d/m/Y H:i', strtotime($almacen['created_at'])); ?></dd>
                    </dl>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fa-solid fa-save me-2"></i>Guardar Cambios
                </button>
                <a href="<?php echo Uri::create('admin/almacenes'); ?>" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-times me-2"></i>Cancelar
                </a>
            </div>
        </div>
    </div>
</form>
