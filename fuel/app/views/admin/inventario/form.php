<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title mb-0">
					<i class="fas fa-dolly"></i> 
					<?php echo $movement ? 'Editar Movimiento: ' . htmlspecialchars($movement->code, ENT_QUOTES, 'UTF-8') : 'Nuevo Movimiento de Inventario'; ?>
				</h3>
			</div>

			<form action="" method="post" id="movementForm">
				<?php echo Form::csrf(); ?>

				<div class="card-body">
					<div class="row">
						<!-- Tipo de Movimiento -->
						<div class="col-md-4 mb-3">
							<label class="form-label">Tipo de Movimiento <span class="text-danger">*</span></label>
							<select name="type" id="type" class="form-select" required <?php echo $movement ? 'disabled' : ''; ?>>
								<option value="">Seleccionar...</option>
								<option value="entry" <?php echo $type === 'entry' ? 'selected' : ''; ?>>Entrada</option>
								<option value="exit" <?php echo $type === 'exit' ? 'selected' : ''; ?>>Salida</option>
								<option value="transfer" <?php echo $type === 'transfer' ? 'selected' : ''; ?>>Traspaso</option>
								<option value="adjustment" <?php echo $type === 'adjustment' ? 'selected' : ''; ?>>Ajuste</option>
								<option value="relocation" <?php echo $type === 'relocation' ? 'selected' : ''; ?>>Reubicación</option>
							</select>
							<?php if ($movement): ?>
								<input type="hidden" name="type" value="<?php echo $movement->type; ?>">
							<?php endif; ?>
						</div>

						<!-- Subtipo -->
						<div class="col-md-4 mb-3">
							<label class="form-label">Subtipo</label>
							<select name="subtype" id="subtype" class="form-select">
								<option value="">Ninguno</option>
								<!-- Opciones dinámicas según tipo -->
								<option value="purchase" data-type="entry" <?php echo ($movement && $movement->subtype === 'purchase') ? 'selected' : ''; ?>>Compra</option>
								<option value="return" data-type="entry" <?php echo ($movement && $movement->subtype === 'return') ? 'selected' : ''; ?>>Devolución</option>
								<option value="production" data-type="entry" <?php echo ($movement && $movement->subtype === 'production') ? 'selected' : ''; ?>>Producción</option>
								
								<option value="sale" data-type="exit" <?php echo ($movement && $movement->subtype === 'sale') ? 'selected' : ''; ?>>Venta</option>
								<option value="damage" data-type="exit" <?php echo ($movement && $movement->subtype === 'damage') ? 'selected' : ''; ?>>Merma/Daño</option>
								<option value="return" data-type="exit" <?php echo ($movement && $movement->subtype === 'return') ? 'selected' : ''; ?>>Devolución</option>
								
								<option value="inventory_count" data-type="adjustment" <?php echo ($movement && $movement->subtype === 'inventory_count') ? 'selected' : ''; ?>>Conteo Físico</option>
								<option value="correction" data-type="adjustment" <?php echo ($movement && $movement->subtype === 'correction') ? 'selected' : ''; ?>>Corrección</option>
							</select>
						</div>

						<!-- Fecha -->
						<div class="col-md-4 mb-3">
							<label class="form-label">Fecha de Movimiento <span class="text-danger">*</span></label>
							<input type="date" name="movement_date" class="form-control" 
								   value="<?php echo $movement ? date('Y-m-d', strtotime($movement->movement_date)) : date('Y-m-d'); ?>" 
								   required>
						</div>
					</div>

					<div class="row">
						<!-- Almacén Origen -->
						<div class="col-md-6 mb-3">
							<label class="form-label">Almacén <span id="warehouseLabel">Origen</span> <span class="text-danger">*</span></label>
							<select name="warehouse_id" id="warehouse_id" class="form-select" required>
								<option value="">Seleccionar...</option>
								<?php foreach ($warehouses as $wh): ?>
									<option value="<?php echo $wh['id']; ?>" 
										<?php echo ($movement && $movement->warehouse_id == $wh['id']) ? 'selected' : ''; ?>>
										<?php echo htmlspecialchars($wh['name'], ENT_QUOTES, 'UTF-8'); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<!-- Almacén Destino (solo para traspasos) -->
						<div class="col-md-6 mb-3" id="warehouse_to_container" style="display: none;">
							<label class="form-label">Almacén Destino <span class="text-danger">*</span></label>
							<select name="warehouse_to_id" id="warehouse_to_id" class="form-select">
								<option value="">Seleccionar...</option>
								<?php foreach ($warehouses as $wh): ?>
									<option value="<?php echo $wh['id']; ?>" 
										<?php echo ($movement && $movement->warehouse_to_id == $wh['id']) ? 'selected' : ''; ?>>
										<?php echo htmlspecialchars($wh['name'], ENT_QUOTES, 'UTF-8'); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<div class="row">
						<!-- Razón -->
						<div class="col-md-6 mb-3">
							<label class="form-label">Razón</label>
							<input type="text" name="reason" class="form-control" 
								   value="<?php echo $movement ? htmlspecialchars($movement->reason, ENT_QUOTES, 'UTF-8') : ''; ?>" 
								   placeholder="Motivo del movimiento">
						</div>

						<!-- Notas -->
						<div class="col-md-6 mb-3">
							<label class="form-label">Notas</label>
							<textarea name="notes" class="form-control" rows="1" 
									  placeholder="Observaciones adicionales"><?php echo $movement ? htmlspecialchars($movement->notes, ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
						</div>
					</div>

					<hr class="my-4">

					<!-- Items del Movimiento -->
					<div class="d-flex justify-content-between align-items-center mb-3">
						<h5 class="mb-0"><i class="fas fa-box"></i> Productos</h5>
						<button type="button" class="btn btn-sm btn-success" id="addItemBtn">
							<i class="fas fa-plus"></i> Agregar Producto
						</button>
					</div>

					<div class="table-responsive">
						<table class="table table-bordered" id="itemsTable">
							<thead class="table-light">
								<tr>
									<th width="300">Producto</th>
									<th width="150" id="locationFromHeader" style="display: none;">Ubicación Origen</th>
									<th width="150" id="locationToHeader" style="display: none;">Ubicación Destino</th>
									<th width="100">Cantidad</th>
									<th width="120">Costo Unitario</th>
									<th width="120">Subtotal</th>
									<th width="200">Notas</th>
									<th width="80">Acciones</th>
								</tr>
							</thead>
							<tbody id="itemsTableBody">
								<?php if ($movement && count($movement->items)): ?>
									<?php foreach ($movement->items as $index => $item): ?>
										<tr>
											<td>
										<select name="items[<?php echo $index; ?>][product_id]" class="form-select product-select" required>
											<option value="">Seleccionar...</option>
											<?php foreach ($products as $prod): ?>
												<option value="<?php echo $prod->id; ?>" 
													data-cost="<?php echo $prod->cost_price; ?>" 
													<?php echo $item->product_id == $prod->id ? 'selected' : ''; ?>>
													<?php echo htmlspecialchars($prod->name, ENT_QUOTES, 'UTF-8'); ?>
												</option>
											<?php endforeach; ?>
												</select>
											</td>
									<td class="location-from-col" style="display: none;">
										<select name="items[<?php echo $index; ?>][location_from_id]" class="form-select">
											<option value="">Sin ubicación</option>
											<?php foreach ($locations as $loc): ?>
												<option value="<?php echo $loc->id; ?>" <?php echo $item->location_from_id == $loc->id ? 'selected' : ''; ?>>
													<?php echo htmlspecialchars($loc->code, ENT_QUOTES, 'UTF-8'); ?>
												</option>
											<?php endforeach; ?>
												</select>
											</td>
									<td class="location-to-col" style="display: none;">
										<select name="items[<?php echo $index; ?>][location_to_id]" class="form-select">
											<option value="">Sin ubicación</option>
											<?php foreach ($locations as $loc): ?>
												<option value="<?php echo $loc->id; ?>" <?php echo $item->location_to_id == $loc->id ? 'selected' : ''; ?>>
													<?php echo htmlspecialchars($loc->code, ENT_QUOTES, 'UTF-8'); ?>
												</option>
											<?php endforeach; ?>
												</select>
											</td>
											<td>
												<input type="number" name="items[<?php echo $index; ?>][quantity]" class="form-control item-quantity" 
													   value="<?php echo $item->quantity; ?>" min="0.01" step="0.01" required>
											</td>
											<td>
												<input type="number" name="items[<?php echo $index; ?>][unit_cost]" class="form-control item-cost" 
													   value="<?php echo $item->unit_cost; ?>" min="0" step="0.01" required>
											</td>
											<td>
												<input type="text" class="form-control item-subtotal" value="$<?php echo number_format($item->subtotal, 2); ?>" readonly>
											</td>
											<td>
												<input type="text" name="items[<?php echo $index; ?>][notes]" class="form-control" 
													   value="<?php echo htmlspecialchars($item->notes, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Notas">
											</td>
											<td class="text-center">
												<button type="button" class="btn btn-sm btn-danger remove-item-btn">
													<i class="fas fa-trash"></i>
												</button>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
							<tfoot>
								<tr class="table-light">
									<td colspan="5" class="text-end"><strong>Total:</strong></td>
									<td><strong id="totalAmount">$0.00</strong></td>
									<td colspan="2"></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>

				<div class="card-footer">
					<div class="d-flex justify-content-between">
						<a href="<?php echo Uri::create('admin/inventario'); ?>" class="btn btn-secondary">
							<i class="fas fa-arrow-left"></i> Cancelar
						</a>
						<button type="submit" class="btn btn-primary">
							<i class="fas fa-save"></i> Guardar Movimiento
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Template para nueva fila de item -->
<template id="itemRowTemplate">
	<tr>
		<td>
			<select name="items[INDEX][product_id]" class="form-select product-select" required>
				<option value="">Seleccionar...</option>
				<?php foreach ($products as $prod): ?>
					<option value="<?php echo $prod->id; ?>" data-cost="<?php echo $prod->cost_price; ?>">
						<?php echo htmlspecialchars($prod->name, ENT_QUOTES, 'UTF-8'); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</td>
		<td class="location-from-col" style="display: none;">
			<select name="items[INDEX][location_from_id]" class="form-select">
				<option value="">Sin ubicación</option>
				<?php foreach ($locations as $loc): ?>
					<option value="<?php echo $loc->id; ?>">
						<?php echo htmlspecialchars($loc->code, ENT_QUOTES, 'UTF-8'); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</td>
		<td class="location-to-col" style="display: none;">
			<select name="items[INDEX][location_to_id]" class="form-select">
				<option value="">Sin ubicación</option>
				<?php foreach ($locations as $loc): ?>
					<option value="<?php echo $loc->id; ?>">
						<?php echo htmlspecialchars($loc->code, ENT_QUOTES, 'UTF-8'); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</td>
		<td>
			<input type="number" name="items[INDEX][quantity]" class="form-control item-quantity" value="1" min="0.01" step="0.01" required>
		</td>
		<td>
			<input type="number" name="items[INDEX][unit_cost]" class="form-control item-cost" value="0.00" min="0" step="0.01" required>
		</td>
		<td>
			<input type="text" class="form-control item-subtotal" value="$0.00" readonly>
		</td>
		<td>
			<input type="text" name="items[INDEX][notes]" class="form-control" placeholder="Notas">
		</td>
		<td class="text-center">
			<button type="button" class="btn btn-sm btn-danger remove-item-btn">
				<i class="fas fa-trash"></i>
			</button>
		</td>
	</tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
	let itemIndex = <?php echo $movement ? count($movement->items) : 0; ?>;

	// Configurar formulario según tipo
	function updateFormByType() {
		const type = document.getElementById('type').value;
		const warehouseLabel = document.getElementById('warehouseLabel');
		const warehouseToContainer = document.getElementById('warehouse_to_container');
		const warehouseToSelect = document.getElementById('warehouse_to_id');
		const locationFromHeader = document.getElementById('locationFromHeader');
		const locationToHeader = document.getElementById('locationToHeader');
		const locationFromCols = document.querySelectorAll('.location-from-col');
		const locationToCols = document.querySelectorAll('.location-to-col');
		const subtypeSelect = document.getElementById('subtype');

		// Filtrar subtipos
		Array.from(subtypeSelect.options).forEach(opt => {
			if (opt.value === '') {
				opt.style.display = 'block';
			} else {
				const optType = opt.getAttribute('data-type');
				opt.style.display = (optType === type) ? 'block' : 'none';
				if (optType !== type && opt.selected) {
					subtypeSelect.value = '';
				}
			}
		});

		// Configurar según tipo
		switch(type) {
			case 'entry':
				warehouseLabel.textContent = '';
				warehouseToContainer.style.display = 'none';
				warehouseToSelect.removeAttribute('required');
				locationFromHeader.style.display = 'none';
				locationToHeader.style.display = 'table-cell';
				locationFromCols.forEach(col => col.style.display = 'none');
				locationToCols.forEach(col => col.style.display = 'table-cell');
				break;
			case 'exit':
				warehouseLabel.textContent = '';
				warehouseToContainer.style.display = 'none';
				warehouseToSelect.removeAttribute('required');
				locationFromHeader.style.display = 'table-cell';
				locationToHeader.style.display = 'none';
				locationFromCols.forEach(col => col.style.display = 'table-cell');
				locationToCols.forEach(col => col.style.display = 'none');
				break;
			case 'transfer':
				warehouseLabel.textContent = 'Origen';
				warehouseToContainer.style.display = 'block';
				warehouseToSelect.setAttribute('required', 'required');
				locationFromHeader.style.display = 'table-cell';
				locationToHeader.style.display = 'table-cell';
				locationFromCols.forEach(col => col.style.display = 'table-cell');
				locationToCols.forEach(col => col.style.display = 'table-cell');
				break;
			case 'relocation':
				warehouseLabel.textContent = '';
				warehouseToContainer.style.display = 'none';
				warehouseToSelect.removeAttribute('required');
				locationFromHeader.style.display = 'table-cell';
				locationToHeader.style.display = 'table-cell';
				locationFromCols.forEach(col => col.style.display = 'table-cell');
				locationToCols.forEach(col => col.style.display = 'table-cell');
				break;
			case 'adjustment':
				warehouseLabel.textContent = '';
				warehouseToContainer.style.display = 'none';
				warehouseToSelect.removeAttribute('required');
				locationFromHeader.style.display = 'none';
				locationToHeader.style.display = 'table-cell';
				locationFromCols.forEach(col => col.style.display = 'none');
				locationToCols.forEach(col => col.style.display = 'table-cell');
				break;
		}
	}

	// Agregar item
	document.getElementById('addItemBtn').addEventListener('click', function() {
		const template = document.getElementById('itemRowTemplate');
		const newRow = template.content.cloneNode(true);
		const tr = newRow.querySelector('tr');
		
		// Reemplazar INDEX con el índice actual
		tr.innerHTML = tr.innerHTML.replace(/INDEX/g, itemIndex);
		
		document.getElementById('itemsTableBody').appendChild(tr);
		itemIndex++;
		
		updateFormByType();
		calculateTotal();
	});

	// Eliminar item
	document.addEventListener('click', function(e) {
		if (e.target.closest('.remove-item-btn')) {
			e.target.closest('tr').remove();
			calculateTotal();
		}
	});

	// Calcular subtotal cuando cambia cantidad o costo
	document.addEventListener('input', function(e) {
		if (e.target.classList.contains('item-quantity') || e.target.classList.contains('item-cost')) {
			const row = e.target.closest('tr');
			const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
			const cost = parseFloat(row.querySelector('.item-cost').value) || 0;
			const subtotal = quantity * cost;
			row.querySelector('.item-subtotal').value = '$' + subtotal.toFixed(2);
			calculateTotal();
		}
	});

	// Auto-llenar costo al seleccionar producto
	document.addEventListener('change', function(e) {
		if (e.target.classList.contains('product-select')) {
			const selectedOption = e.target.options[e.target.selectedIndex];
			const cost = selectedOption.getAttribute('data-cost') || 0;
			const row = e.target.closest('tr');
			row.querySelector('.item-cost').value = parseFloat(cost).toFixed(2);
			
			const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
			const subtotal = quantity * parseFloat(cost);
			row.querySelector('.item-subtotal').value = '$' + subtotal.toFixed(2);
			calculateTotal();
		}
	});

	// Calcular total
	function calculateTotal() {
		let total = 0;
		document.querySelectorAll('.item-quantity').forEach(function(input) {
			const row = input.closest('tr');
			const quantity = parseFloat(input.value) || 0;
			const cost = parseFloat(row.querySelector('.item-cost').value) || 0;
			total += quantity * cost;
		});
		document.getElementById('totalAmount').textContent = '$' + total.toFixed(2);
	}

	// Evento cambio de tipo
	document.getElementById('type').addEventListener('change', updateFormByType);

	// Inicializar
	updateFormByType();
	calculateTotal();
});
</script>
