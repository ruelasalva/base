<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Reporte Diario CRM</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/crm/activity', 'Reportes CRM'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/crm/task/activity', 'Agregar'); ?>
							</li>
						</ol>
					</nav>
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
			<div class="card-wrapper">
				<!-- CUSTOM FORM VALIDATION -->
				<div class="card">
					<!-- CARD HEADER -->
					<?php echo Form::open(array('method' => 'post', 'id' => 'activity-form')); ?>
					<div class="card-header d-flex justify-content-between align-items-center">
						<h3 class="mb-0">Agregar</h3>
						<div class="form-group mb-0 d-flex align-items-center">
							<label for="global-date" class="form-control-label mr-2">Fecha:</label>
							<?php echo Form::input('global_date', date('d/m/Y', time()), array(
								'id' => 'global_date',
								'class' => 'form-control datepicker'
							)); ?>
						</div>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<fieldset>
							<div class="form-row">
								<div class="col-md-12 mt-0 mb-3">
									<legend class="mb-0 heading">Captura de actividades</legend>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-5">
									<div class="form-group">
										<?php echo Form::label('Cliente', 'customer', array('class' => 'form-control-label', 'for' => 'customer')); ?>
										<?php echo Form::input('customer', '', array('id' => 'customer', 'class' => 'form-control', 'placeholder' => 'Cliente')); ?>
									</div>
								</div>
								<div class="form-group col-md-5">
									<div class="form-group">
										<?php echo Form::label('Razon Social', 'company', array('class' => 'form-control-label', 'for' => 'company')); ?>
										<?php echo Form::input('company', '', array('id' => 'company', 'class' => 'form-control', 'placeholder' => 'Razón Social')); ?>
									</div>
								</div>
								<div class="form-group col-md-2">
									<div class="form-group">
										<?php echo Form::label('Monto Total Sin IVA', 'total', array('class' => 'form-control-label', 'for' => 'total')); ?>
										<?php echo Form::input('total', '', array('id' => 'total', 'class' => 'form-control', 'placeholder' => 'Ingresa el monto total sin IVA', 'type' => 'number', 'step' => '0.01', 'min' => '0')); ?>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-6">
									<div class="form-group">
										<?php echo Form::label('Medio de contacto', 'contact_id', array('class' => 'form-control-label', 'for' => 'contact_id')); ?>
										<?php echo Form::select('contact_id', 'none', $contact_opts, array('id' => 'contact_id', 'class' => 'form-control', 'data-toggle' => 'select')); ?>
									</div>
								</div>
								<div class="form-group col-md-2">
									<?php echo Form::label('Hora', 'hour', array('class' => 'form-control-label')); ?>
									<?php echo Form::input('hour', '', array('id' => 'hour', 'class' => 'form-control', 'type' => 'time')); ?>
								</div>
								<div class="form-group col-md-2">
									<?php echo Form::label('Factura', 'invoice', array('class' => 'form-control-label')); ?>
									<?php
										echo Form::select(
											'invoice',
											'',
											array(
												'' => 'S/N',
												'1' => 'Sí',
												'0' => 'No',
											),
											array('id' => 'invoice', 'class' => 'form-control', 'data-toggle' => 'select')
										);
									?>
								</div>
								<div class="form-group col-md-2">
									<?php echo Form::label('Foráneo', 'foreing', array('class' => 'form-control-label')); ?>
									<?php
										echo Form::select(
											'foreing',
											'',
											array(
												'' => 'S/N',
												'1' => 'Sí',
												'0' => 'No',
											),
											array('id' => 'foreing', 'class' => 'form-control', 'data-toggle' => 'select')
										);
									?>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-6">
									<div class="form-group">
										<?php echo Form::label('Duración de Llamada', 'time_id', array('class' => 'form-control-label', 'for' => 'time_id')); ?>
										<?php echo Form::select('time_id', 'none', $time_opts, array('id' => 'time_id', 'class' => 'form-control', 'data-toggle' => 'select')); ?>
									</div>
								</div>
								<div class="form-group col-md-6">
									<div class="form-group">
										<?php echo Form::label('Flujo', 'type_id', array('class' => 'form-control-label', 'for' => 'type_id')); ?>
										<?php echo Form::select('type_id', 'none', $type_opts, array('id' => 'type_id', 'class' => 'form-control', 'data-toggle' => 'select')); ?>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-6">
									<div class="form-group">
										<?php echo Form::label('Tipo de interacción', 'status_id', array('class' => 'form-control-label', 'for' => 'status_id')); ?>
										<?php echo Form::select('status_id', 'none', $status_opts, array('id' => 'status_id', 'class' => 'form-control', 'data-toggle' => 'select')); ?>
									</div>
								</div>
								<div class="form-group col-md-6">
									<div class="form-group">
										<?php echo Form::label('Producto de Interés', 'category_id', array('class' => 'form-control-label', 'for' => 'category_id')); ?>
										<?php echo Form::select('category_id', 'none', $category_opts, array('id' => 'category_id', 'class' => 'form-control', 'data-toggle' => 'select')); ?>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-12">
									<div class="form-group">
										<?php echo Form::label('Minuta (Explicación Breve)', 'comments', array('class' => 'form-control-label', 'for' => 'comments')); ?>
										<?php echo Form::textarea('comments', '', array('id' => 'comments', 'class' => 'form-control', 'placeholder' => 'Minuta (Explicación Breve)', 'rows' => 7)); ?>
									</div>
								</div>
							</div>
						</fieldset>
						<div class="form-group text-right">
							<button id="add-activity" type="button" class="btn btn-primary" data-actnum="<?php echo $act_num; ?>" data-edit="0">Agregar</button>
							<button id="finalize-activities" type="button" class="btn btn-success" data-actnum="<?php echo $act_num; ?>">Cerrar día</button>
						</div>
					</div>
					<?php echo Form::close(); ?>
					<!-- CARD FOOTER -->
					<!-- REGISTROS -->
					<div class="card-footer">
						<h4 class="mb-3 text-primary font-weight-bold">Actividades Registradas</h4>
						<div class="table-responsive">
							<table class="table table-bordered table-hover align-middle text-center">
								<thead class="thead-light">
									<tr>
										<th>Cliente</th>
										<th>Factura</th>
										<th>Razon Social</th>
										<th>Foraneo</th>
										<th>Medio<br>de Contacto</th>
										<th>Hora</th>
										<th>Duracion <br> de llamada</th>
										<th>Flujo</th>
										<th>Estatus</th>
										<th>Productos <br> de interes</th>
										<th>Minuta</th>
										<th>Total <br>sin IVA</th>
									</tr>
								</thead>
								<tbody id="activities-table-body">
									<!-- Registros dinámicos -->
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
