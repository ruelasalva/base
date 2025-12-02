<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Socios de Negocios</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/socios', 'Socios de Negocios'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/socios/agregar', 'Agregar', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/socios/importar_csv', 'Importar Socios por CSV', array('class' => 'btn btn-sm btn-neutral')); ?><br><br>
					<?php echo Html::anchor('admin/socios/csv/exportar_generales', 'Exportar Generales en CSV', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/socios/csv/exportar_entregas', 'Exportar Domicilios Entrega CSV', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/socios/csv/exportar_contactos', 'Exportar Contactos CSV', array('class' => 'btn btn-sm btn-neutral')); ?><br><br>
					<?php echo Html::anchor('admin/socios/csv/exportar_actualizados', 'Exportar Actualizados', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/socios/csv/exportar_todo', 'Exportar Todo', array('class' => 'btn btn-sm btn-neutral')); ?>
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
					<?php echo Form::open(array('action' => 'admin/socios/buscar', 'method' => 'post')); ?>
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Lista de Socios de Negocios</h3>
						</div>
						<div class="col-md-3">
							<select id="filtro_socios" class="form-control">
								<option value="none">Filtrar socios por...</option>
								<option value="updated">Actualizados (últimos 7 días)</option>
								<option value="csf">Con constancia fiscal</option>
								<option value="deliveries">Con entregas registradas</option>
								<option value="contacts">Con contactos registrados</option>
							</select>
						</div>
						<div class="col-md-3 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::input('search', (isset($search) ? $search : ''), array('id' => 'search', 'class' => 'form-control', 'placeholder' => 'Codigo ó Nombre', 'aria-describedby' => 'button-addon')); ?>
								<div class="input-group-append">
									<?php echo Form::submit(array('value'=> 'Buscar', 'name'=>'submit', 'id' => 'button-addon', 'class' => 'btn btn-outline-primary')); ?>
								</div>
							</div>
							
						</div>
					</div>
					<?php echo Form::close(); ?>
				</div>
				<!-- LIGHT TABLE -->
				<div class="table-responsive" data-toggle="lists" data-list-values='["id", "code_sap", "username", "name", "rfc","email", "type_id", "banned","connected" ,"updated_at"]'>
					<table class="table align-items-center table-flush">
						<thead class="thead-light">
							<tr>
								<th scope="col" class="sort" data-sort="id">ID</th>
								<th scope="col" class="sort" data-sort="code_sap">Codigo<br> Cliente</th>
								<th scope="col" class="sort" data-sort="name">Razon Social</th>
								<th scope="col" class="sort" data-sort="rfc">RFC</th>
								<th scope="col" class="sort" data-sort="email">Correo</th>
								<th scope="col" class="sort" data-sort="csf">CSF</th>
								<th scope="col" class="sort" data-sort="deliveries">Dom.<br> Entrega</th>
								<th scope="col" class="sort" data-sort="contacts">Contactos</th>
								<th scope="col" class="sort" data-sort="type_id">Lista de<br> Precios</th>
								<th scope="col" class="sort" data-sort="employee_id">Vendedor</th>
								<th scope="col" class="sort" data-sort="banned">Bloqueado</th>
								<th scope="col" class="sort" data-sort="updated_at">Ultima<br> Actualizacion</th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody class="list">
							<?php if(!empty($partners)): ?>
								<?php foreach($partners as $partner): ?>
									<tr>
										<th class="id">
											<?php echo Html::anchor('admin/socios/info/'.$partner['id'],$partner['id']); ?>
										</th>
										<th class="code_sap">
											<?php echo Html::anchor('admin/socios/info/'.$partner['id'], $partner['code_sap']); ?>
										</th>
										<td class="name">
											<?php echo $partner['name']; ?>
										</td>
										<td class="rfc">
											<?php echo $partner['rfc']; ?>
										</td>
										<td class="email">
											<?php echo $partner['email']; ?>
										</td>

										<td class="csf">
											<?php echo $partner['csf']; ?>
										</td>

										<td class="deliveries">
											<?php echo $partner['deliveries']; ?>
										</td>

										<td class="contacts">
											<?php echo $partner['contacts']; ?>
										</td>

										<td class="type_id">
											<?php echo $partner['type_id']; ?>
										</td>
										
										<td class="employee_id">
											<?php echo $partner['employee_id']; ?>
										</td>
										<td class="banned">
											<?php echo $partner['banned']; ?>
										</td>
										<td class="updated_at">
											<?php echo $partner['updated_at']; ?>
										</td>
										<td class="text-right">
											<div class="dropdown">
												<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i class="fas fa-ellipsis-v"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
													<?php echo Html::anchor('admin/socios/info/'.$partner['id'], 'Ver', array('class' => 'dropdown-item')); ?>
													<?php echo Html::anchor('admin/socios/editar/'.$partner['id'], 'Editar', array('class' => 'dropdown-item')); ?>
													<?php echo Html::anchor('admin/socios/recuperar_contrasena_socios/'.$partner['user_id'], 'Recuperar Contraseña', array('class' => 'dropdown-item')); ?>
												</div>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<th scope="row">
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
