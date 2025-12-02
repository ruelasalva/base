<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-8 col-7">
                    <h4 class="text-white mb-0">Editar Regla Automática</h4>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block mt-1">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
                            <li class="breadcrumb-item"><?php echo Html::anchor('admin/notificaciones', 'Notificaciones'); ?></li>
                            <li class="breadcrumb-item active">Editar Regla</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-4 col-5 text-right">
                    <?php echo Html::anchor('admin/notificaciones', 'Regresar', array('class' => 'btn btn-sm btn-neutral')); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt--6">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h3 class="mb-0">Configuración de Regla Automática</h3>
                </div>
                <div class="card-body">
                    <?php echo Form::open(array('class' => 'needs-validation', 'autocomplete' => 'off')); ?>

                    <div class="form-group">
                        <label>Evento (event_key) <span class="text-danger">*</span></label>
                        <input type="text" name="event_key" class="form-control" value="<?= e($config->event_key) ?>" required>
                        <small class="text-muted">Clave interna para identificar el evento (ejemplo: <code>ticket_nuevo</code>).</small>
                    </div>
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" name="title" class="form-control" value="<?= e($config->title) ?>">
                    </div>
                    <div class="form-group">
                        <label>Mensaje</label>
                        <textarea name="message" class="form-control"><?= e($config->message) ?></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Icono (FontAwesome)</label>
                            <input type="text" name="icon" class="form-control" value="<?= e($config->icon) ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Prioridad</label>
                            <input type="number" name="priority" class="form-control" value="<?= e($config->priority) ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>URL Pattern</label>
                        <input type="text" name="url_pattern" class="form-control" value="<?= e($config->url_pattern) ?>">
                        <small class="text-muted">Puedes usar <code>{ID}</code> para reemplazar por el ID del objeto al lanzar la notificación.</small>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Grupos Destino</label>
                            <p>Deseclecionar con CTRL y clik los que no quieras</p>
                            <select name="group_ids[]" class="form-control" multiple>
                                <?php foreach ($groups as $gid): ?>
                                    <option value="<?= $gid ?>" <?= in_array($gid, $group_ids) ? 'selected' : '' ?>>
                                        <?= $group_definitions[$gid]['name'] ?? $gid ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Usuarios Destino</label>
                            <p>Deseclecionar con CTRL y clik los que no quieras</p>
                            <select name="user_ids[]" class="form-control" multiple>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?= $u->id ?>" <?= in_array($u->id, $user_ids) ? 'selected' : '' ?>>
                                        <?= e($u->username) ?> (<?= $group_definitions[$u->group]['name'] ?? $u->group ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>¿Activa?</label>
                        <select name="active" class="form-control">
                            <option value="1" <?= $config->active ? 'selected' : '' ?>>Sí</option>
                            <option value="0" <?= !$config->active ? 'selected' : '' ?>>No</option>
                        </select>
                    </div>
                    <div class="form-group mt-4 text-right">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar Cambios</button>
                        <?php echo Html::anchor('admin/notificaciones', 'Cancelar', array('class' => 'btn btn-link')); ?>
                    </div>
                    <?php echo Form::close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
