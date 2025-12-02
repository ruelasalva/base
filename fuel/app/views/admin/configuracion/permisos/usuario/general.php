<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Configuración General</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/configuracion/permisos/usuario', 'Permisos por Usuario'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								Ver Información
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<!--BOTONES -->
                    
				</div>
			</div>
		</div>
	</div>
</div>

<!-- PAGE CONTENT -->
<!-- CONTROL DE PERMISOS POR USUARIO -->
<div class="card mt-4">
    <div class="card-header bg-gradient-primary text-white">
        <h3 class="mb-0">Permisos por Usuario (General)</h3>
    </div>
    <div class="card-body">

        <div class="alert alert-info p-2 mb-2 small">
            <strong>Nota:</strong>
            <i class="fas fa-check text-primary"></i> Permiso directo de usuario &nbsp; | &nbsp;
            <i class="fas fa-check text-danger"></i> Permiso heredado de grupo
        </div>

        <!-- ENVUELVE la tabla en un div para scroll -->
        <div style="max-width:100vw; max-height:65vh; overflow:auto; border-radius:10px;">
            <table class="table table-bordered table-sm align-middle permisos-table">
                <thead class="thead-light">
                    <tr>
                        <th rowspan="3" class="align-middle text-center" style="position: sticky; left: 0; z-index: 3; background: #fff; min-width: 210px;">
                            Usuario<br>Email<br>ID<br>Grupo
                        </th>
                        <?php foreach ($modules as $mod): ?>
                            <th class="text-center" colspan="<?php echo !empty($mod['children']) ? count($mod['children']) * 4 : 4; ?>">
                                <?php echo $mod['name']; ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <?php foreach ($modules as $mod): ?>
                            <?php if (!empty($mod['children'])): ?>
                                <?php foreach ($mod['children'] as $submod): ?>
                                    <th class="text-center" colspan="4"><?php echo $submod; ?></th>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <th class="text-center" colspan="4"></th>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <!-- ¡AQUÍ NO PONGAS <th></th> VACÍO! -->
                        <?php foreach ($modules as $mod): ?>
                            <?php if (!empty($mod['children'])): ?>
                                <?php foreach ($mod['children'] as $submod): ?>
                                    <th class="text-center"><small>Ver</small></th>
                                    <th class="text-center"><small>Editar</small></th>
                                    <th class="text-center"><small>Eliminar</small></th>
                                    <th class="text-center"><small>Crear</small></th>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <th class="text-center"><small>Ver</small></th>
                                <th class="text-center"><small>Editar</small></th>
                                <th class="text-center"><small>Eliminar</small></th>
                                <th class="text-center"><small>Crear</small></th>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td style="position: sticky; left: 0; background: #fff; z-index: 2; min-width:210px;">
                            <strong><?php echo $usuario['username']; ?></strong><br>
                            <small><?php echo $usuario['email']; ?></small><br>
                            <small>ID: <?php echo $usuario['id']; ?></small><br>
                            <?php
                                $gid = (int)($usuario['group'] ?? 0);
                                $has_group = isset($grupos[$gid]);
                                $gname = $has_group ? $grupos[$gid] : 'SIN GRUPO';
                                $gcolor = $colores_grupo[$gid] ?? 'badge-secondary';
                            ?>
                            <span class="badge badge-pill <?php echo $gcolor; ?>">
                                <?php echo $gname; ?>
                            </span>
                        </td>
                        <?php foreach ($modules as $mod_key => $mod): ?>
                            <?php if (!empty($mod['children'])): ?>
                                <?php foreach ($mod['children'] as $child_key => $child_name): ?>
                                    <?php foreach (['view', 'edit', 'delete', 'create'] as $action): ?>
                                        <td class="text-center">
                                            <?php
                                                $user_perm  = $perms_usuario[$usuario['id']][$child_key][$action] ?? null;
                                                $group_perm = $perms_grupo[$gid][$child_key][$action] ?? null;
                                                // Ambos si existen los dos (¡Sí se pueden ver juntos!)
                                                if ($user_perm) {
                                                    echo '<i class="fas fa-check text-primary" title="Permiso usuario"></i>';
                                                }
                                                if ($group_perm) {
                                                    echo '<i class="fas fa-check text-danger ml-1" title="Permiso grupo"></i>';
                                                }
                                                if (!$user_perm && !$group_perm) {
                                                    echo '<span class="text-muted">-</span>';
                                                }
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php foreach (['view', 'edit', 'delete', 'create'] as $action): ?>
                                    <td class="text-center">
                                        <?php
                                            $user_perm  = $perms_usuario[$usuario['id']][$mod_key][$action] ?? null;
                                            $group_perm = $perms_grupo[$gid][$mod_key][$action] ?? null;
                                            if ($user_perm) {
                                                echo '<i class="fas fa-check text-primary" title="Permiso usuario"></i>';
                                            }
                                            if ($group_perm) {
                                                echo '<i class="fas fa-check text-danger ml-1" title="Permiso grupo"></i>';
                                            }
                                            if (!$user_perm && !$group_perm) {
                                                echo '<span class="text-muted">-</span>';
                                            }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div> <!-- CIERRE DEL DIV DE SCROLL -->
    </div>
</div>





