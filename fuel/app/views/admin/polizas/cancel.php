<div class="container-fluid">
	<div class="mb-4">
		<h2><i class="fas fa-ban"></i> Cancelar Póliza</h2>
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Inicio</a></li>
				<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/polizas'); ?>">Pólizas</a></li>
				<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/polizas/view/' . $entry->id); ?>"><?php echo $entry->entry_number; ?></a></li>
				<li class="breadcrumb-item active">Cancelar</li>
			</ol>
		</nav>
	</div>

	<div class="row">
		<div class="col-md-8">
			<div class="card">
				<div class="card-body">
					<div class="alert alert-warning">
						<h5><i class="fas fa-exclamation-triangle"></i> Advertencia</h5>
						<p>Está a punto de cancelar la siguiente póliza:</p>
						<ul>
							<li><strong>Folio:</strong> <?php echo $entry->entry_number; ?></li>
							<li><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($entry->entry_date)); ?></li>
							<li><strong>Concepto:</strong> <?php echo $entry->concept; ?></li>
							<li><strong>Total Cargos:</strong> $<?php echo number_format($entry->total_debit, 2); ?></li>
							<li><strong>Total Abonos:</strong> $<?php echo number_format($entry->total_credit, 2); ?></li>
						</ul>
						<p class="mb-0"><strong>La póliza cancelada no afectará los saldos de las cuentas.</strong></p>
					</div>

					<form method="post">
						<div class="mb-3">
							<label class="form-label">Motivo de Cancelación <span class="text-danger">*</span></label>
							<textarea name="cancellation_reason" class="form-control" rows="5" required 
							          placeholder="Indique el motivo por el cual se cancela esta póliza..."></textarea>
							<small class="text-muted">Este motivo quedará registrado en el historial de auditoría</small>
						</div>

						<div class="d-flex gap-2">
							<button type="submit" class="btn btn-warning">
								<i class="fas fa-ban"></i> Confirmar Cancelación
							</button>
							<a href="<?php echo Uri::create('admin/polizas/view/' . $entry->id); ?>" class="btn btn-secondary">
								<i class="fas fa-times"></i> Volver
							</a>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<div class="card">
				<div class="card-header">
					<h5 class="mb-0"><i class="fas fa-info-circle"></i> Información</h5>
				</div>
				<div class="card-body">
					<h6>Efectos de la cancelación:</h6>
					<ul class="small">
						<li>La póliza cambiará a estado "Cancelada"</li>
						<li>No se puede revertir esta acción</li>
						<li>Los movimientos no afectarán los libros</li>
						<li>Quedará registrada para auditoría</li>
					</ul>
					<hr>
					<h6>Alternativas:</h6>
					<p class="small mb-0">Si necesita corregir información, considere crear una póliza de ajuste en lugar de cancelar.</p>
				</div>
			</div>
		</div>
	</div>
</div>
