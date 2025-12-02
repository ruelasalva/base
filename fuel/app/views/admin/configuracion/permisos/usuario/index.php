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
                    <div class="col-lg-6 col-5 text-right">
                        <?php echo Html::anchor('admin/configuracion/permisos/usuario/general', 'Vista general de usuarios', array('class' => 'btn btn-sm btn-neutral')); ?>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- PAGE CONTENT -->
<!-- CONTROL DE PERMISOS POR USUARIO -->
<div class="card mt-4">
    <div class="card-header bg-gradient-primary text-white">
        <h3 class="mb-0">Permisos de Usuarios</h3>
    </div>
    <div class="card-body">
        <!-- Formulario de búsqueda -->
        <form method="post" action="<?php echo Uri::create('admin/configuracion/permisos/usuario/buscar'); ?>">
            <div class="input-group mb-3">
                <input type="text" name="search" class="form-control" placeholder="Buscar usuario, email..." value="<?php echo $search ?? ''; ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </div>
            </div>
        </form>

        <?php if(Session::get_flash('success')): ?>
            <div class="alert alert-success"><?php echo Session::get_flash('success'); ?></div>
        <?php endif; ?>
        <?php if(Session::get_flash('error')): ?>
            <div class="alert alert-danger"><?php echo Session::get_flash('error'); ?></div>
        <?php endif; ?>
           <?php if($pagination != ''): ?>
             <?php echo $pagination; ?>
             <?php endif; ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Grupo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(!empty($usuarios)): ?>
                    <?php foreach($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo $usuario['username']; ?></td>
                            <td><?php echo $usuario['email']; ?></td>
                            <td>
                                <?php
                                switch($usuario['group']) {
                                    case 20:  echo 'Empleado'; break;
                                    case 25:  echo 'Vendedor'; break;
                                    case 30:  echo 'Externo'; break;
                                    case 50:  echo 'Moderador'; break;
                                    case 100: echo 'Administrador'; break;
                                    default:  echo 'Desconocido'; break;
                                }
                                ?>
                            </td>
                            <td>
                                <?php echo Html::anchor(
                                    'admin/configuracion/permisos/usuario/editar/'.$usuario['id'],
                                    '<i class="fas fa-key"></i> Editar permisos',
                                    ['class' => 'btn btn-sm btn-primary']
                                ); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No hay usuarios encontrados.</td>
                    </tr>
                <?php endif; ?>
                </tbody>

            </table>
        </div>
        <?php if($pagination != ''): ?>
					<!-- CARD FOOTER -->
					<div class="card-footer py-4">
						<?php echo $pagination; ?>
					</div>
				<?php endif; ?>
    </div>
</div>



