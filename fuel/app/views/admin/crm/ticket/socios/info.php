<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Tickets Socios</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('socios', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/crm/socios/ticket', 'Tickets Socios'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								Ticket #<?php echo $ticket->id; ?>
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
	<div class="row">
		<div class="col">
			<div class="card-wrapper">

				<!-- DETALLES DEL TICKET -->
				<div class="card shadow-sm">
					<div class="card-header">
						<h3 class="mb-0">Ticket #<?php echo $ticket->id; ?> - <?php echo $ticket->subject; ?></h3>
						<span class="badge badge-<?php echo ($ticket->status == 0) ? 'warning' : (($ticket->status == 1) ? 'primary' : (($ticket->status == 2) ? 'success' : 'dark')); ?>">
							<?php
								switch ($ticket->status) {
									case 0: echo 'Abierto'; break;
									case 1: echo 'En Proceso'; break;
									case 2: echo 'Resuelto'; break;
									default: echo 'Cerrado'; break;
								}
							?>
						</span>
					</div>
					<div class="card-body">
						<p><?php echo nl2br($ticket->message); ?></p>
						<p class="text-muted">Fecha: <?php echo date('d/m/Y H:i', $ticket->created_at); ?></p>
					</div>
				</div>

				

				<!-- HISTORIAL DE RESPUESTAS -->
				<div class="card mt-4 shadow-sm border">
					<div class="card-header bg-gradient-primary text-white">
						<h3 class="mb-0"><i class="ni ni-chat-round mr-2"></i> Conversación del Ticket</h3>
					</div>
					<div class="card-body bg-white">
						<?php if (!empty($messages)): ?>
							<?php foreach ($messages as $msg): ?>
								<div class="d-flex <?php echo $msg['align_class']; ?> mb-3">
									<div class="shadow-sm rounded px-4 py-3 <?php echo $msg['bg_class']; ?>" style="max-width: 75%;">
										<p class="mb-1 font-weight-normal"><?php echo nl2br(e($msg['message'])); ?></p>
										<small class="d-block text-muted mt-2 font-italic">
											<?php echo $msg['sender_label']; ?> · <?php echo date('d/m/Y H:i', $msg['created_at']); ?>
										</small>
									</div>
								</div>
							<?php endforeach; ?>
						<?php else: ?>
							<p class="text-muted">No hay mensajes aún en este ticket.</p>
						<?php endif; ?>
					</div>
				</div>

				<!-- FORMULARIO DE RESPUESTA -->
				
				<div class="card mt-4 shadow-sm">
					<div class="card-header bg-light border-bottom">
						<h3 class="mb-0">
							<i class="ni ni-send mr-2"></i> Responder y Actualizar Estatus
						</h3>
					</div>
					<div class="card-body">
						<?php echo Form::open(array('method' => 'post')); ?>
							<div class="form-row">
								<!-- Campo de respuesta -->
								<div class="form-group col-md-8">
									<?php echo Form::label('Mensaje', 'message', array('class' => 'form-control-label font-weight-bold')); ?>
									<?php echo Form::textarea('message', '', array(
										'id'          => 'message',
										'name'        => 'message',
										'class'       => 'form-control',
										'rows'        => '4',
										'placeholder' => 'Escribe tu respuesta aquí...'
									)); ?>
								</div>

								<!-- Campo de estatus -->
								<div class="form-group col-md-4">
									<?php echo Form::label('Estatus del Ticket', 'estatus', ['class' => 'form-control-label font-weight-bold']); ?>
									<?php echo Form::select('estatus', $current_status, [
										0 => 'Abierto',
										1 => 'En Proceso',
										2 => 'Resuelto',
										3 => 'Cerrado'
									], ['class' => 'form-control']); ?>
								</div>
							</div>

							<div class="text-right">
								<?php echo Form::submit(array('value' => 'Enviar Respuesta y Cambiar Estatus', 'name' => 'enviar_respuesta_estatus', 'class' => 'btn btn-primary')); ?>
							</div>
						<?php echo Form::close(); ?>
					</div>
				</div>


			</div>
		</div>
	</div>
</div>

<!-- ESTILO EXTRA -->
<style>
	.bg-light.border {
		background-color: #f8f9fa !important;
		border: 1px solid #dee2e6;
	}
	.text-muted small {
		font-size: 0.8rem;
	}
</style>
