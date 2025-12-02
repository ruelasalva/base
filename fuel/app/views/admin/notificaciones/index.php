<!-- ENCABEZADO VISUAL -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Notificaciones</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Notificaciones
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/notificaciones/agregar', 'Agregar', array('class' => 'btn btn-sm btn-neutral')); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PAGE CONTENT -->
<!-- ENCABEZADO VISUAL (mantén igual que tu diseño actual) -->

<div class="container-fluid mt--6">
    <!-- NOTIFICACIONES REALES -->
    <div class="row">
        <div class="col">
            <div class="card shadow mb-4">
                <div class="card-header border-0 bg-primary text-white">
                </div>
                <div class="card-body">
                    <?php if (empty($notifications)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fa fa-inbox" style="font-size:28px;opacity:.18;"></i>
                            <div>Sin notificaciones registradas.</div>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Título</th>
                                        <th>Tipo</th>
                                        <th>Activo</th>
                                        <th>Fecha</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($notifications as $notif): ?>
                                    <tr>
                                        <td><?php echo $notif->id; ?></td>
                                        <td>
                                            <?php if (!empty($notif->icon)): ?>
                                                <i class="<?= $notif->icon ?> mr-1"></i>
                                            <?php endif; ?>
                                            <?php echo e($notif->title); ?>
                                            <?php if (!empty($notif->message)): ?>
                                                <div style="font-size:12px;color:#666;"><?= $notif->message ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($notif->type == 'manual') echo '<span class="badge badge-info">Manual</span>';
                                            elseif ($notif->type == 'evento') echo '<span class="badge badge-primary">Por Evento</span>';
                                            else echo '<span class="badge badge-secondary">'.e($notif->type).'</span>';
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($notif->active): ?>
                                                <span class="badge badge-success">Sí</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', $notif->created_at); ?></td>
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <?php echo Html::anchor('admin/notificaciones/info/'.$notif->id, 'Ver', array('class' => 'dropdown-item')); ?>
                                                    <?php echo Html::anchor('admin/notificaciones/editar/'.$notif->id, 'Editar', array('class' => 'dropdown-item')); ?>
                                                    <?php echo Html::anchor('admin/notificaciones/eliminar/'.$notif->id, 'Eliminar', array(
                                                        'class' => 'dropdown-item text-danger',
                                                        'onclick' => "return confirm('¿Seguro que deseas eliminar esta notificación?');"
                                                    )); ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- PAGINACIÓN (si la necesitas) -->
            </div>
        </div>
    </div>
    <!-- FIN CARD NOTIFICACIONES -->

    <!-- REGLAS AUTOMÁTICAS (CARD) -->
    <div class="row">
        <div class="col">
            <div class="card shadow mb-4">
                <div class="card-header bg-gradient-secondary text-white">
                    <h5 class="mb-0"><i class="fa fa-cogs"></i> Reglas de Notificaciones Automáticas</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($plantillas)): ?>
                    <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Evento</th>
                                <th>Título</th>
                                <th>Grupos</th>
                                <th>Usuarios</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($plantillas as $p): ?>
                            <tr>
                                <td><code><?= $p['event_key'] ?></code></td>
                                <td><?= $p['title'] ?></td>
                                <td>
                                    <?php foreach ($p['grupos'] as $gid): ?>
                                        <span class="badge badge-info"><?= $group_definitions[$gid]['name'] ?? $gid ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <?php foreach ($p['usuarios'] as $uid):
                                        // Busca el username para mostrarlo como badge (opcional)
                                        $uname = '';
                                        if (!empty($users)) {
                                            foreach ($users as $usr) {
                                                if ($usr->id == $uid) {
                                                    $uname = $usr->username;
                                                    break;
                                                }
                                            }
                                        }
                                    ?>
                                        <span class="badge badge-primary"><?= $uname ?: $uid ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <?= Html::anchor('admin/notificaciones/editarauto/'.$p['id'], 'Editar', ['class'=>'btn btn-sm btn-link']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                    <?php else: ?>
                        <p class="text-muted">No tienes reglas de notificación configuradas.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- FIN CARD REGLAS AUTOMÁTICAS -->
</div>
