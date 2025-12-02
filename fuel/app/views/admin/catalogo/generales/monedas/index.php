<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Catálogo</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/catalogo/generales/monedas', 'Monedas'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/catalogo/generales/monedas/agregar', 'Agregar', array('class' => 'btn btn-sm btn-neutral')); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
	<!-- TABLE -->
	<div class="row">
		<div class="col">
			<div class="card">
				<!-- CARD HEADER -->
				<div class="card-header border-0">
					<?php echo Form::open(array('action' => 'admin/catalogo/generales/monedas/buscar', 'method' => 'post')); ?>
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Lista de monedas</h3>
						</div>
						<div class="col-md-3 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::input('search', (isset($search) ? $search : ''), array('id' => 'search', 'class' => 'form-control', 'placeholder' => 'Nombre', 'aria-describedby' => 'button-addon')); ?>
								<div class="input-group-append">
									<?php echo Form::submit(array('value'=> 'Buscar', 'name'=>'submit', 'id' => 'button-addon', 'class' => 'btn btn-outline-primary')); ?>
								</div>
							</div>
						</div>
					</div>
					<?php echo Form::close(); ?>
				</div>
				<!-- LIGHT TABLE -->
				<div class="table-responsive" data-toggle="lists" data-list-values='["name","code","symbol","type_exchange"]'>
					<table class="table align-items-center table-flush">
						<thead class="thead-light">
							<tr>
								<th scope="col" class="sort" data-sort="name">Nombre</th>
								<th scope="col" class="sort" data-sort="code">Código</th>
								<th scope="col" class="sort" data-sort="symbol">Símbolo</th>
								<th scope="col" class="sort" data-sort="type_exchange">Tipo de Cambio</th>
								<th scope="col" class="sort" data-sort="status">Mostrar</th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody class="list">
							<?php if(!empty($curriencies)): ?>
								<?php foreach($curriencies as $currienci): ?>
									<tr>
										<th class="name">
											<?php echo Html::anchor('admin/catalogo/generales/monedas/info/'.$currienci['id'], $currienci['name']); ?>
										</th>
										<td class="code"><?php echo $currienci['code']; ?></td>
										<td class="symbol"><?php echo $currienci['symbol']; ?></td>
										<td class="type_exchange"><?php echo number_format($currienci['type_exchange'], 4); ?></td>
										<td>
											<label class="custom-toggle">
												<input type="checkbox" class="toggle-currency" data-currency="<?php echo $currienci['id']; ?>" <?php echo ($currienci['deleted'] == 0) ? 'checked' : ''; ?>>
												<span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Sí"></span>
											</label>
										</td>
										<td class="text-right">
											<div class="dropdown">
												<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i class="fas fa-ellipsis-v"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
													<?php echo Html::anchor('admin/catalogo/generales/monedas/info/'.$currienci['id'], 'Ver', array('class' => 'dropdown-item')); ?>
													<?php echo Html::anchor('admin/catalogo/generales/monedas/editar/'.$currienci['id'], 'Editar', array('class' => 'dropdown-item')); ?>
													<div class="dropdown-divider"></div>
													<?php echo Html::anchor('admin/catalogo/generales/monedas/eliminar/'.$currienci['id'], 'Eliminar', array('class' => 'dropdown-item delete-item')); ?>
												</div>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<th scope="row" colspan="6">
										No existen registros
									</th>
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
	</div>
</div>
