<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        <?php echo $account ? 'Editar Cuenta Contable' : 'Nueva Cuenta Contable'; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <!-- Código de Cuenta -->
                            <div class="col-md-4 mb-3">
                                <label for="account_code" class="form-label">
                                    Código de Cuenta <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="account_code" 
                                       name="account_code" 
                                       value="<?php echo $account ? $account->account_code : ''; ?>" 
                                       required 
                                       maxlength="20"
                                       placeholder="ej: 1000, 1100, 1101...">
                                <small class="text-muted">Código único de identificación</small>
                            </div>

                            <!-- Código SAT -->
                            <div class="col-md-4 mb-3">
                                <label for="sat_code" class="form-label">Código SAT</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="sat_code" 
                                       name="sat_code" 
                                       value="<?php echo $account ? $account->sat_code : ''; ?>" 
                                       maxlength="20"
                                       placeholder="Código SAT (opcional)">
                                <small class="text-muted">Para cumplimiento fiscal</small>
                            </div>

                            <!-- Nivel (auto-calculado, solo informativo) -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Nivel en Jerarquía</label>
                                <input type="text" 
                                       class="form-control" 
                                       value="<?php echo $account ? $account->level : 'Se calculará automáticamente'; ?>" 
                                       disabled>
                                <small class="text-muted">Se calcula según cuenta padre</small>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Nombre -->
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label">
                                    Nombre de la Cuenta <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       value="<?php echo $account ? $account->name : ''; ?>" 
                                       required 
                                       maxlength="150"
                                       placeholder="Nombre descriptivo de la cuenta">
                            </div>
                        </div>

                        <div class="row">
                            <!-- Cuenta Padre -->
                            <div class="col-md-6 mb-3">
                                <label for="parent_id" class="form-label">Cuenta Padre (Opcional)</label>
                                <select class="form-select" id="parent_id" name="parent_id">
                                    <option value="">-- Sin cuenta padre (Nivel 0) --</option>
                                    <?php foreach ($parent_accounts as $parent): ?>
                                        <?php if (!$account || $parent->id != $account->id): ?>
                                            <option value="<?php echo $parent->id; ?>" 
                                                    <?php echo ($account && $account->parent_id == $parent->id) ? 'selected' : ''; ?>>
                                                <?php echo str_repeat('&nbsp;&nbsp;&nbsp;', $parent->level); ?>
                                                <?php echo $parent->account_code . ' - ' . $parent->name; ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Selecciona si esta cuenta es subcuenta de otra</small>
                            </div>

                            <!-- Tipo de Cuenta -->
                            <div class="col-md-6 mb-3">
                                <label for="account_type" class="form-label">
                                    Tipo de Cuenta <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="account_type" name="account_type" required>
                                    <option value="">Selecciona...</option>
                                    <option value="activo" <?php echo ($account && $account->account_type == 'activo') ? 'selected' : ''; ?>>
                                        Activo
                                    </option>
                                    <option value="pasivo" <?php echo ($account && $account->account_type == 'pasivo') ? 'selected' : ''; ?>>
                                        Pasivo
                                    </option>
                                    <option value="capital" <?php echo ($account && $account->account_type == 'capital') ? 'selected' : ''; ?>>
                                        Capital
                                    </option>
                                    <option value="ingresos" <?php echo ($account && $account->account_type == 'ingresos') ? 'selected' : ''; ?>>
                                        Ingresos
                                    </option>
                                    <option value="egresos" <?php echo ($account && $account->account_type == 'egresos') ? 'selected' : ''; ?>>
                                        Egresos
                                    </option>
                                    <option value="resultado" <?php echo ($account && $account->account_type == 'resultado') ? 'selected' : ''; ?>>
                                        Resultado
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Subtipo -->
                            <div class="col-md-6 mb-3">
                                <label for="account_subtype" class="form-label">Subtipo (Opcional)</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="account_subtype" 
                                       name="account_subtype" 
                                       value="<?php echo $account ? $account->account_subtype : ''; ?>" 
                                       maxlength="50"
                                       placeholder="ej: Circulante, Fijo, Corriente...">
                                <small class="text-muted">Clasificación adicional</small>
                            </div>

                            <!-- Naturaleza -->
                            <div class="col-md-6 mb-3">
                                <label for="nature" class="form-label">
                                    Naturaleza <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="nature" name="nature" required>
                                    <option value="">Selecciona...</option>
                                    <option value="deudora" <?php echo ($account && $account->nature == 'deudora') ? 'selected' : ''; ?>>
                                        Deudora (Activos, Egresos)
                                    </option>
                                    <option value="acreedora" <?php echo ($account && $account->nature == 'acreedora') ? 'selected' : ''; ?>>
                                        Acreedora (Pasivos, Capital, Ingresos)
                                    </option>
                                </select>
                                <small class="text-muted">Define el comportamiento de cargos y abonos</small>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Descripción detallada de la cuenta..."><?php echo $account ? $account->description : ''; ?></textarea>
                        </div>

                        <div class="row">
                            <!-- Permite Movimientos -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Configuración</label>
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="allows_movement" 
                                           name="allows_movement" 
                                           value="1" 
                                           <?php echo (!$account || $account->allows_movement) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="allows_movement">
                                        Permite Movimientos (Pólizas)
                                    </label>
                                </div>
                                <small class="text-muted">
                                    Desactiva si es cuenta de agrupación (solo tiene subcuentas)
                                </small>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado</label>
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1" 
                                           <?php echo (!$account || $account->is_active) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_active">
                                        Cuenta Activa
                                    </label>
                                </div>
                                <small class="text-muted">Solo cuentas activas pueden usarse en pólizas</small>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Guardar Cuenta
                            </button>
                            <a href="<?php echo Uri::create('admin/cuentascontables'); ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar de Ayuda -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-1"></i> Guía Rápida
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">Tipos de Cuenta</h6>
                    <ul class="small">
                        <li><strong>Activo:</strong> Bienes y derechos</li>
                        <li><strong>Pasivo:</strong> Obligaciones</li>
                        <li><strong>Capital:</strong> Patrimonio</li>
                        <li><strong>Ingresos:</strong> Entradas</li>
                        <li><strong>Egresos:</strong> Salidas</li>
                    </ul>

                    <hr>

                    <h6 class="text-primary">Naturaleza</h6>
                    <ul class="small">
                        <li><strong>Deudora:</strong> Aumenta con cargo, disminuye con abono</li>
                        <li><strong>Acreedora:</strong> Aumenta con abono, disminuye con cargo</li>
                    </ul>

                    <hr>

                    <h6 class="text-primary">Jerarquía</h6>
                    <p class="small mb-0">
                        Puedes crear cuentas de varios niveles. Las cuentas padre agrupan subcuentas.
                        Ejemplo: 1000 → 1100 → 1101
                    </p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-warning">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-1"></i> Consejos
                    </h6>
                </div>
                <div class="card-body small">
                    <ul class="mb-0">
                        <li>Usa códigos numéricos consecutivos</li>
                        <li>Define bien la naturaleza desde el inicio</li>
                        <li>Las cuentas de agrupación no permiten movimientos</li>
                        <li>El código SAT es opcional pero recomendado</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
