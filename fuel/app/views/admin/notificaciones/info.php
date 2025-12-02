<!-- ENCABEZADO VISUAL Y BREADCRUMB -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-7 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">
                        Notificación #<?php echo $notification->id; ?>
                    </h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/notificaciones', 'Notificaciones'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Detalle
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-5 col-5 text-right">
                    <?php echo Html::anchor('admin/notificaciones/editar/'.$notification->id, 'Editar', array('class' => 'btn btn-sm btn-neutral')); ?>
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
                <!-- DATOS DE LA NOTIFICACIÓN -->
                <div class="card-header border-0 pb-2">
                    <h3 class="mb-0">
                        <?php echo e($notification->title); ?>
                        <?php if ($notification->type == 'manual'): ?>
                            <span class="badge badge-info">Manual</span>
                        <?php elseif ($notification->type == 'evento'): ?>
                            <span class="badge badge-primary">Por Evento</span>
                        <?php else: ?>
                            <span class="badge badge-secondary"><?php echo e($notification->type); ?></span>
                        <?php endif; ?>
                    </h3>
                    <div class="small text-muted mt-1">
                        Creada el <?php echo date('d/m/Y H:i', $notification->created_at); ?> |
                        Estado:
                        <?php if ($notification->active): ?>
                            <span class="badge badge-success">Activa</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inactiva</span>
                        <?php endif; ?>
                        <?php if ($notification->expires_at && $notification->expires_at < time()): ?>
                            <span class="badge badge-danger">Expirada</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body pb-1">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-2">Mensaje:</h5>
                            <div class="mb-3">
                                <?php echo $notification->message; ?>
                            </div>
                            <?php if (!empty($notification->url)): ?>
                                <div>
                                    <strong>Enlace:</strong>
                                    <a href="<?php echo e($notification->url); ?>" target="_blank"><?php echo e($notification->url); ?></a>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($notification->icon)): ?>
                                <div class="mt-2">
                                    <strong>Icono:</strong> <i class="<?php echo e($notification->icon); ?>"></i>
                                    <span class="ml-2"><?php echo e($notification->icon); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <h5 class="mb-2">Información adicional</h5>
                            <div>
                                <strong>ID:</strong> <?php echo $notification->id; ?><br>
                                <strong>Prioridad:</strong> <?php echo (isset($notification->priority) ? $notification->priority : ''); ?><br>
                                <strong>Expira:</strong>
                                <?php
                                    if ($notification->expires_at) {
                                        echo date('d/m/Y H:i', $notification->expires_at);
                                    } else {
                                        echo 'No definido';
                                    }
                                ?><br>
                                <strong>Creado por:</strong>
                                <?php
                                    if ($notification->created_by) {
                                        // Si tienes la relación, muestra nombre, si no solo el ID
                                        echo 'Usuario #'.$notification->created_by;
                                    } else {
                                        echo 'Sistema';
                                    }
                                ?>
                            </div>
                            <?php if ($evento): ?>
                                <div class="mt-2">
                                    <strong>Evento:</strong>
                                    <?php echo $evento->event_key; ?> <br>
                                    <span class="text-muted"><?php echo e($evento->title); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- TABLA DE DESTINATARIOS -->
                <div class="table-responsive px-4 pb-4">
                    <h5 class="mt-4">Destinatarios</h5>
                    <table class="table table-flush table-sm">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Grupo</th>
                                <th>Leída</th>
                                <th>Fecha de lectura</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recipients)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        Sin destinatarios registrados.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recipients as $rec): ?>
                                    <tr>
                                        <td>
                                            <?php
                                            if ($rec->user) {
                                                echo e($rec->user->username);
                                            } else {
                                                echo '<span class="text-muted">No asignado</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($rec->user) {
                                                echo e($rec->user->email);
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            // Si tienes grupo del usuario, muestralo aquí
                                            if ($rec->user && isset($rec->user->group)) {
                                                echo e($rec->user->group);
                                            } elseif (!empty($rec->user_group_id)) {
                                                echo 'Grupo #'.e($rec->user_group_id);
                                            } else {
                                                echo '<span class="text-muted">-</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($rec->status == 1): ?>
                                                <span class="badge badge-success">Sí</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($rec->read_at) {
                                                echo date('d/m/Y H:i', is_numeric($rec->read_at) ? $rec->read_at : strtotime($rec->read_at));
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
