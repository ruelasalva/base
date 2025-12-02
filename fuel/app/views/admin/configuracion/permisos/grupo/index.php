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
								<?php echo Html::anchor('admin/configuracion/permisos/grupo', 'Permisos por Grupo'); ?>
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
        <h3 class="mb-0">Permisos por Grupo</h3>
    </div>
    <div class="card-body">
        <form method="post" action="">
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th rowspan="3" class="align-middle text-center" style="position: sticky; left: 0; z-index: 3; background: #fff;">Grupo</th>
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
                        <?php foreach ($grupos as $group_id => $group_name): ?>
                            <tr>
                                <td style="position: sticky; left: 0; background: #fff; z-index: 2;">
                                    <strong><?php echo $group_name; ?></strong>
                                    <br><small>ID: <?php echo $group_id; ?></small>
                                </td>
                                <?php foreach ($modules as $mod_key => $mod): ?>
                                    <?php if (!empty($mod['children'])): ?>
                                        <?php foreach ($mod['children'] as $child_key => $child_name): ?>
                                            <?php foreach (['view', 'edit', 'delete', 'create'] as $action): ?>
                                                <td class="text-center">
                                                    <input type="checkbox"
                                                        name="perm[<?php echo $group_id; ?>][<?php echo $child_key; ?>][<?php echo $action; ?>]"
                                                        value="1"
                                                        <?php if (isset($perms[$group_id][$child_key][$action]) && $perms[$group_id][$child_key][$action]): ?>
                                                            checked
                                                        <?php endif; ?>
                                                    >
                                                </td>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <?php foreach (['view', 'edit', 'delete', 'create'] as $action): ?>
                                            <td class="text-center">
                                                <input type="checkbox"
                                                    name="perm[<?php echo $group_id; ?>][<?php echo $mod_key; ?>][<?php echo $action; ?>]"
                                                    value="1"
                                                    <?php if (isset($perms[$group_id][$mod_key][$action]) && $perms[$group_id][$mod_key][$action]): ?>
                                                        checked
                                                    <?php endif; ?>
                                                >
                                            </td>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-right mt-3">
                <button type="submit" class="btn btn-success">Guardar Permisos de Grupo</button>
            </div>
        </form>
    </div>
</div>



