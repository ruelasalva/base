<!-- ENCABEZADO VISUAL Y BREADCRUMB -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-7 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Editar Notificación</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/notificaciones', 'Notificaciones'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Editar
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-5 col-5 text-right">
                    <?php echo Html::anchor('admin/notificaciones/info/'.$notification->id, 'Ver', array('class' => 'btn btn-sm btn-info')); ?>
                    <?php echo Html::anchor('admin/notificaciones', 'Regresar', array('class' => 'btn btn-sm btn-secondary')); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <div class="card">
                <?php echo Form::open(['action' => 'admin/notificaciones/editar/'.$notification->id, 'method' => 'post']); ?>
                <div class="card-header border-0 pb-2">
                    <h3 class="mb-0">Editar Notificación</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info py-2 mb-3">
                        <i class="fa fa-info-circle"></i>
                        Los <b>proveedores</b>, <b>socios</b> y <b>clientes finales</b> (grupos <span class="font-monospace">1, 10, 15</span>) no aparecen en los selectores.
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-3">
                            <label>Tipo de notificación</label>
                            <select name="type" class="form-control" id="type-notif" onchange="toggleEventFields()" disabled>
                                <option value="manual" <?php echo ($notification->type == 'manual' ? 'selected' : ''); ?>>Manual</option>
                                <option value="evento" <?php echo ($notification->type == 'evento' ? 'selected' : ''); ?>>Por Evento</option>
                            </select>
                        </div>
                        <?php if ($notification->type == 'evento'): ?>
                        <div class="col-md-3" id="event-key-group">
                            <label>Clave de evento</label>
                            <input type="text" name="event_key" class="form-control" value="<?php echo isset($notification->event_key) ? e($notification->event_key) : ''; ?>" readonly>
                        </div>
                        <div class="col-md-3" id="url-pattern-group">
                            <label>URL patrón</label>
                            <input type="text" name="url_pattern" class="form-control" value="<?php echo isset($notification->url_pattern) ? e($notification->url_pattern) : ''; ?>">
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-6">
                            <label>Título <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="<?php echo e($notification->title); ?>" required maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label>URL (opcional)</label>
                            <input type="text" name="url" class="form-control" value="<?php echo e($notification->url); ?>" maxlength="255">
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-12">
                            <label>Mensaje <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" required rows="3"><?php echo e($notification->message); ?></textarea>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-3">
                            <label>Ícono</label>
                            <input type="text" name="icon" class="form-control" maxlength="64" value="<?php echo e($notification->icon); ?>" placeholder="fa fa-bell">
                        </div>
                        <div class="col-md-3">
                            <label>Prioridad</label>
                            <input type="number" name="priority" class="form-control" value="<?php echo (int)$notification->priority; ?>" min="1" max="10">
                        </div>
                        <div class="col-md-3">
                            <label>Expira (opcional)</label>
                            <input type="date" name="expires_at" class="form-control" value="<?php echo ($notification->expires_at ? date('Y-m-d', $notification->expires_at) : ''); ?>">
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-6">
                            <label>Usuarios destino</label>
                            <p>Deseclecionar con CTRL y clik los que no quieras</p>
                            <select name="user_ids[]" class="form-control" multiple>
                                <?php foreach ($users as $u): ?>
                                    <?php
                                        $grupo_num = $u->group;
                                        $grupo_name = isset($group_definitions[$grupo_num]['name']) ? $group_definitions[$grupo_num]['name'] : $grupo_num;
                                        $selected = in_array($u->id, $selected_users) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo $u->id; ?>" <?php echo $selected; ?>>
                                        <?php echo e($u->username . ' (' . $u->email . ') [Grupo: ' . $grupo_name . ' (' . $grupo_num . ')]'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Grupos destino</label>
                            <p>Deseclecionar con CTRL y clik los que no quieras</p>
                            <select name="group_ids[]" class="form-control" multiple>
                                <?php foreach ($groups as $g): ?>
                                    <?php
                                        $grupo_name = isset($group_definitions[$g]['name']) ? $group_definitions[$g]['name'] : $g;
                                        $selected = in_array($g, $selected_groups) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo $g; ?>" <?php echo $selected; ?>>
                                        <?php echo $grupo_name . ' (' . $g . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
                <?php echo Form::close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
function toggleEventFields() {
    var type = document.getElementById('type-notif').value;
    document.getElementById('event-key-group').style.display = (type === 'evento') ? 'block' : 'none';
    document.getElementById('url-pattern-group').style.display = (type === 'evento') ? 'block' : 'none';
}
document.addEventListener('DOMContentLoaded', function() {
    toggleEventFields();
});
</script>
