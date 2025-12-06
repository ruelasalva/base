<div class="container-fluid">
	<div class="mb-4">
		<h2><i class="fas fa-file-invoice"></i> Nueva Póliza Contable</h2>
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Inicio</a></li>
				<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/polizas'); ?>">Pólizas</a></li>
				<li class="breadcrumb-item active">Nueva</li>
			</ol>
		</nav>
	</div>

	<form method="post" id="polizaForm">
		<div class="row">
			<div class="col-md-8">
				<div class="card mb-4">
					<div class="card-header">
						<h5 class="mb-0">Información General</h5>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-6 mb-3">
								<label class="form-label">Tipo de Póliza <span class="text-danger">*</span></label>
								<select name="entry_type" class="form-select" required>
									<option value="">Seleccionar...</option>
									<option value="ingreso">Ingreso (I)</option>
									<option value="egreso">Egreso (E)</option>
									<option value="diario">Diario (D)</option>
									<option value="apertura">Apertura (A)</option>
									<option value="ajuste">Ajuste (AJ)</option>
									<option value="cierre">Cierre (C)</option>
								</select>
								<small class="text-muted">El folio se generará automáticamente</small>
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label">Fecha <span class="text-danger">*</span></label>
								<input type="date" name="entry_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
							</div>
						</div>
						<div class="mb-3">
							<label class="form-label">Concepto <span class="text-danger">*</span></label>
							<input type="text" name="concept" class="form-control" maxlength="200" required>
						</div>
						<div class="mb-3">
							<label class="form-label">Referencia</label>
							<input type="text" name="reference" class="form-control" maxlength="50">
							<small class="text-muted">Opcional: factura, cheque, etc.</small>
						</div>
					</div>
				</div>

				<div class="card">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h5 class="mb-0">Partidas / Movimientos</h5>
						<button type="button" class="btn btn-sm btn-primary" id="addLine">
							<i class="fas fa-plus"></i> Agregar Línea
						</button>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-bordered" id="linesTable">
								<thead class="table-light">
									<tr>
										<th width="5%">#</th>
										<th width="25%">Cuenta</th>
										<th width="30%">Descripción</th>
										<th width="15%">Cargo</th>
										<th width="15%">Abono</th>
										<th width="5%">Ref</th>
										<th width="5%"></th>
									</tr>
								</thead>
								<tbody id="linesBody">
									<!-- Se agregarán filas dinámicamente -->
								</tbody>
								<tfoot class="table-light">
									<tr>
										<th colspan="3" class="text-end">Totales:</th>
										<th class="text-end" id="totalDebit">$0.00</th>
										<th class="text-end" id="totalCredit">$0.00</th>
										<th colspan="2"></th>
									</tr>
									<tr>
										<th colspan="3" class="text-end">Diferencia:</th>
										<th colspan="2" class="text-center">
											<span id="balanceStatus" class="badge bg-secondary">$0.00</span>
										</th>
										<th colspan="2"></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-4">
				<div class="card mb-4">
					<div class="card-header">
						<h5 class="mb-0">Acciones</h5>
					</div>
					<div class="card-body">
						<button type="submit" class="btn btn-success w-100 mb-2">
							<i class="fas fa-save"></i> Guardar Póliza
						</button>
						<a href="<?php echo Uri::create('admin/polizas'); ?>" class="btn btn-secondary w-100">
							<i class="fas fa-times"></i> Cancelar
						</a>
					</div>
				</div>

				<div class="card">
					<div class="card-header">
						<h5 class="mb-0"><i class="fas fa-info-circle"></i> Ayuda</h5>
					</div>
					<div class="card-body">
						<h6>Tipos de Póliza:</h6>
						<ul class="small">
							<li><strong>Ingreso:</strong> Entradas de dinero</li>
							<li><strong>Egreso:</strong> Salidas de dinero</li>
							<li><strong>Diario:</strong> Operaciones generales</li>
							<li><strong>Apertura:</strong> Inicio de ejercicio</li>
							<li><strong>Ajuste:</strong> Correcciones</li>
							<li><strong>Cierre:</strong> Fin de ejercicio</li>
						</ul>
						<hr>
						<h6>Partida Doble:</h6>
						<p class="small mb-0">El total de cargos debe ser igual al total de abonos para que la póliza esté balanceada.</p>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<!-- Template para líneas -->
<template id="lineTemplate">
	<tr>
		<td class="text-center line-number">1</td>
		<td>
			<select name="account_id[]" class="form-select form-select-sm account-select" required>
				<option value="">Seleccionar...</option>
				<?php foreach ($accounts as $account): ?>
					<option value="<?php echo $account->id; ?>">
						<?php echo $account->account_code . ' - ' . $account->name; ?>
					</option>
				<?php endforeach; ?>
			</select>
		</td>
		<td>
			<input type="text" name="description[]" class="form-control form-control-sm" maxlength="200">
		</td>
		<td>
			<input type="number" name="debit[]" class="form-control form-control-sm text-end debit-input" 
			       step="0.01" min="0" value="0.00">
		</td>
		<td>
			<input type="number" name="credit[]" class="form-control form-control-sm text-end credit-input" 
			       step="0.01" min="0" value="0.00">
		</td>
		<td>
			<input type="text" name="line_reference[]" class="form-control form-control-sm" maxlength="50">
		</td>
		<td class="text-center">
			<button type="button" class="btn btn-sm btn-danger remove-line" title="Eliminar">
				<i class="fas fa-trash"></i>
			</button>
		</td>
	</tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const linesBody = document.getElementById('linesBody');
	const addLineBtn = document.getElementById('addLine');
	const lineTemplate = document.getElementById('lineTemplate');
	
	// Agregar primera línea automáticamente
	addLine();
	addLine();
	
	// Evento para agregar línea
	addLineBtn.addEventListener('click', addLine);
	
	function addLine() {
		const clone = lineTemplate.content.cloneNode(true);
		linesBody.appendChild(clone);
		updateLineNumbers();
		attachLineEvents();
	}
	
	function attachLineEvents() {
		// Eliminar línea
		document.querySelectorAll('.remove-line').forEach(btn => {
			btn.onclick = function() {
				if (linesBody.children.length > 1) {
					this.closest('tr').remove();
					updateLineNumbers();
					calculateTotals();
				} else {
					alert('Debe haber al menos una línea');
				}
			};
		});
		
		// Calcular al cambiar montos
		document.querySelectorAll('.debit-input, .credit-input').forEach(input => {
			input.addEventListener('input', calculateTotals);
			input.addEventListener('blur', function() {
				// Si se llena un campo, limpiar el otro
				const row = this.closest('tr');
				if (this.classList.contains('debit-input') && parseFloat(this.value) > 0) {
					row.querySelector('.credit-input').value = '0.00';
				} else if (this.classList.contains('credit-input') && parseFloat(this.value) > 0) {
					row.querySelector('.debit-input').value = '0.00';
				}
				calculateTotals();
			});
		});
	}
	
	function updateLineNumbers() {
		document.querySelectorAll('.line-number').forEach((el, index) => {
			el.textContent = index + 1;
		});
	}
	
	function calculateTotals() {
		let totalDebit = 0;
		let totalCredit = 0;
		
		document.querySelectorAll('.debit-input').forEach(input => {
			totalDebit += parseFloat(input.value) || 0;
		});
		
		document.querySelectorAll('.credit-input').forEach(input => {
			totalCredit += parseFloat(input.value) || 0;
		});
		
		document.getElementById('totalDebit').textContent = '$' + totalDebit.toFixed(2);
		document.getElementById('totalCredit').textContent = '$' + totalCredit.toFixed(2);
		
		const difference = Math.abs(totalDebit - totalCredit);
		const balanceStatus = document.getElementById('balanceStatus');
		
		if (difference < 0.01) {
			balanceStatus.textContent = 'Balanceada ✓';
			balanceStatus.className = 'badge bg-success';
		} else {
			balanceStatus.textContent = '$' + difference.toFixed(2) + ' diferencia';
			balanceStatus.className = 'badge bg-danger';
		}
	}
	
	// Validar formulario antes de enviar
	document.getElementById('polizaForm').addEventListener('submit', function(e) {
		let totalDebit = 0;
		let totalCredit = 0;
		
		document.querySelectorAll('.debit-input').forEach(input => {
			totalDebit += parseFloat(input.value) || 0;
		});
		
		document.querySelectorAll('.credit-input').forEach(input => {
			totalCredit += parseFloat(input.value) || 0;
		});
		
		if (totalDebit === 0 && totalCredit === 0) {
			e.preventDefault();
			alert('Debe registrar al menos un movimiento');
			return false;
		}
		
		const difference = Math.abs(totalDebit - totalCredit);
		if (difference >= 0.01) {
			if (!confirm('La póliza NO está balanceada (diferencia: $' + difference.toFixed(2) + '). ¿Desea guardarla como borrador de todas formas?')) {
				e.preventDefault();
				return false;
			}
		}
	});
});
</script>
