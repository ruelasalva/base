<!-- AGREGAR.PHP - VISTA PARA CREAR UNA NUEVA PLANTILLA VISUAL (EDITOR DE DISEÑO) -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Agregar plantilla visual</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/editordiseno', 'Plantillas visuales'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">Agregar</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
	<div class="row">
		<div class="col-lg-6 col-12 mx-auto">
			<div class="card shadow">
				<div class="card-header pb-0">
					<h3 class="mb-0">Nueva plantilla visual</h3>
				</div>
				<div class="card-body">
					<?php echo Form::open(['action' => 'admin/editordiseno/agregar', 'method' => 'post']); ?>
					<div class="form-group">
						<?php echo Form::label('Nombre de la plantilla', 'name', ['class' => 'form-control-label']); ?>
						<?php echo Form::input('name', Input::post('name', isset($name) ? $name : ''), [
							'id' => 'name', 'class' => 'form-control', 'maxlength'=>80, 'required' => 'required',
							'placeholder'=>'Ejemplo: Home Navidad',
						]); ?>
					</div>
					<div class="text-center">
						<?php echo Form::button('submit', 'Diseñar plantilla', [
							'class' => 'btn btn-primary px-5',
							'type' => 'submit',
						]); ?>
						<?php echo Html::anchor('admin/editordiseno', 'Cancelar', ['class'=>'btn btn-link']); ?>
					</div>
					<?php echo Form::close(); ?>
				</div>
			</div>
		</div>
	</div>
</div>

