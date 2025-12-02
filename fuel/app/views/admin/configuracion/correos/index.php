<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Configuración de Correos</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">Correos</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<!-- podrías agregar un botón general si lo requieres -->
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
				<div class="card-header border-0">
					<h3 class="mb-0">Módulos de configuración de correos</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-4 mb-3">
							<div class="border p-3 rounded shadow-sm h-100">
								<h5><i class="fa-solid fa-users-gear text-primary"></i> Roles de Correo</h5>
								<p class="text-muted mb-2">
									Definir remitente, reply-to y destinatarios por cada rol (Ventas, Contacto, Soporte...).
								</p>
								<?php echo Html::anchor('admin/configuracion/correos/roles', 'Ir a Roles de Correo', ['class'=>'btn btn-primary btn-sm']); ?>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<div class="border p-3 rounded shadow-sm h-100">
								<h5><i class="fa-solid fa-file-lines text-success"></i> Plantillas de Correo</h5>
								<p class="text-muted mb-2">
									Definir estructura, asunto y vista utilizada en cada notificación de correo.
								</p>
								<?php echo Html::anchor('admin/configuracion/correos/templates', 'Ir a Plantillas de Correo', ['class'=>'btn btn-success btn-sm']); ?>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<div class="border p-3 rounded shadow-sm h-100">
								<h5><i class="fa-solid fa-wand-magic-sparkles text-warning"></i> Creador de Plantillas</h5>
								<p class="text-muted mb-2">
									Generar plantillas base predefinidas y personalizarlas fácilmente con el editor visual.
								</p>
								<?php echo Html::anchor('admin/configuracion/correos/creator', 'Ir al Creador de Plantillas', ['class'=>'btn btn-warning btn-sm']); ?>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
