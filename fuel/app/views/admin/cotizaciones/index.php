<script>
window.access_id = <?= (int)Auth::get('id'); ?>;
window.access_token = '<?= md5(Auth::get('login_hash')); ?>';
</script>
<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Cotizaciones</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/cotizaciones', 'Cotizaciones'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<span id="ultima-actualizacion-catalogos" class="text-muted small"></span>
					<button id="btn-sincronizar-cotizaciones" class="btn btn-warning mb-3" style="display:none;">
						<i class="fa fa-refresh"></i> Sincronizar Cotizaciones Pendientes
					</button>
					<?php echo Html::anchor('admin/cotizaciones/agregar_cotizacion', 'Nueva cotización', array('class' => 'btn btn-sm btn-neutral')); ?>
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
					<?php echo Form::open(array('action' => 'admin/cotizaciones/buscar', 'method' => 'post')); ?>
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Lista de cotizaciones</h3>
						</div>
						<div class="col-md-3 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::input('search', (isset($search) ? $search : ''), array('id' => 'search', 'class' => 'form-control', 'placeholder' => 'ID o nombre', 'aria-describedby' => 'button-addon')); ?>
								<div class="input-group-append">
									<?php echo Form::submit(array('value'=> 'Buscar', 'name'=>'submit', 'id' => 'button-addon', 'class' => 'btn btn-outline-primary')); ?>
								</div>
							</div>
						</div>
					</div>
					<?php echo Form::close(); ?>
				</div>
				<!-- LIGHT TABLE -->
				<div class="table-responsive" data-toggle="lists" data-list-values='["id", "code_sap", "partner", "email"," employee_id", "type", "total", "iva","subtotal","status", "valid_date", "docnum", "user"]'>
					<?php if (!empty($pending_quotes)): ?>
						<div class="card mb-4">
							<div class="card-header bg-warning">
								<strong>Cotizaciones pendientes de clientes (sin procesar)</strong>
							</div>
							<div class="card-body p-0">
								<div class="table-responsive">
									<table class="table align-items-center table-flush table-sm mb-0">
										<thead>
											<tr>
												<th>ID</th>
												<th>Socio</th>
												<th>Fecha</th>
												<th>Productos</th>
												<th>Estatus</th>
												<th>Acción</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($pending_quotes as $pq): ?>
												<tr>
													<td><?= $pq['id'] ?></td>
													<td><?= $pq['partner'] ?></td>
													<td><?= $pq['created_at'] ?></td>
													<td>
														<?php foreach ($pq['products'] as $prod): ?>
															<div><?= $prod['name'] ?> (<?= $prod['quantity'] ?>)</div>
														<?php endforeach; ?>
													</td>
													<td>
														<span class="badge badge-warning"><?= $pq['status'] ?></span>
													</td>
													<td>
														<?php echo Html::anchor('admin/cotizaciones/agregar_cotizacion?pending_id=' . $pq['id'], 'Procesar', ['class' => 'btn btn-primary btn-sm']); ?>


													</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					<?php endif; ?>


					<table class="table align-items-center table-flush">
						<thead class="thead-light">
							<tr>
								<th scope="col" class="sort" data-sort="id">ID</th>
								<th scope="col" class="sort" data-sort="valid_date">Fecha</th>
								<th scope="col" class="sort" data-sort="code_sapr">Codigo</th>
								<th scope="col" class="sort" data-sort="partner">Cliente</th>
								<th scope="col" class="sort" data-sort="total">Sub-Total</th>
								<th scope="col" class="sort" data-sort="total">IVA</th>
								<th scope="col" class="sort" data-sort="total">Total</th>
								<th scope="col" class="sort" data-sort="email">Email</th>
								<th scope="col" class="sort" data-sort="employee_id">Vendedor</th>
								<th scope="col" class="sort" data-sort="type">Tipo de pago</th>
								<th scope="col" class="sort" data-sort="status">Estatus <br> cotizacion</th>
								<th scope="col" class="sort" data-sort="user">Capturato <br>por</th>
								<th scope="col" class="sort" data-sort="docnum">Num. Doc. <br>Sistema</th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody class="list">
							<?php if(!empty($quotes)): ?>
								<?php foreach($quotes as $quote): ?>
									<tr class="<?php echo ($quote['status'] == 'Cancelada') ? 'text-danger' : ''; ?>">
										<th class="id">
											<?php echo Html::anchor('admin/cotizaciones/info/'.$quote['id'], $quote['id']); ?>
										</th>
										<td class="valid_date">
											<?php echo $quote['valid_date']; ?>
										</td>
										<td class="code_sap">
											<?php echo $quote['code_sap']; ?>
										</td>
										<td class="partner">
											<?php echo $quote['partner']; ?>
										</td>
										<td class="subtotal">
											<?php echo $quote['subtotal']; ?>
										</td>
										<td class="iva">
											<?php echo $quote['iva']; ?>
										</td>
										<td class="total">
											<?php echo $quote['total']; ?>
										</td>
										<td class="email">
											<?php echo $quote['email']; ?>
										</td>
										<td class="employee_id">
											<?php echo $quote['employee_id']; ?>
										</td>
										<td class="type">
											<?php echo $quote['type']; ?>
										</td>
										<td class="status">
											<?php echo $quote['status']; ?>
										</td>
										<td class="user">
											<?php echo $quote['user']; ?>
										</td>
										<td class="docnum">
											<?php echo $quote['docnum']; ?>
										</td>
										<td class="text-right">
											<div class="dropdown">
												<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i class="fas fa-ellipsis-v"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
													<?php echo Html::anchor('admin/cotizaciones/info/'.$quote['id'], 'Ver', array('class' => 'dropdown-item')); ?>
													<?php $num = (int) $quote['status_num']; ?>
													<?php if ($num === 0): ?>
														<?php echo Html::anchor('admin/cotizaciones/editar/'.$quote['id'], 'Editar', array('class' => 'dropdown-item')); ?>
													<?php endif; ?>
													<?php if (in_array($num, [0,1,2], true)): ?>
														<?php echo Html::anchor('admin/cotizaciones/imprimir/'.$quote['id'], 'Imprimir', array('class' => 'dropdown-item','target' => '_blank')); ?>
													<?php endif; ?>
													<?php if ($num === 0): ?>
														<?php echo Html::anchor('admin/cotizaciones/enfirme/'.$quote['id'], 'Poner en firme', array('class' => 'dropdown-item','target' => '_blank')); ?>
													<?php endif; ?>
													<div class="dropdown-divider"></div>
													<?php if ($num === 0): ?>
														<?php echo Html::anchor('admin/cotizaciones/cancelar/'.$quote['id'], 'Cancelar', array('class' => 'dropdown-item', 'data-id' => $quote['id'])); ?>
													<?php endif; ?>
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

<?= Asset::js('admin/cotizaciones-general-vue.js'); ?>


