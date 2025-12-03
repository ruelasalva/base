<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Carga Masiva de Facturas</title>
	<style>
		.upload-container {
			max-width: 1200px;
			margin: 20px auto;
			padding: 20px;
			background: #f9f9f9;
			border-radius: 8px;
		}
		.upload-header {
			text-align: center;
			margin-bottom: 30px;
		}
		.upload-form {
			background: white;
			padding: 30px;
			border-radius: 8px;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
		}
		.file-input-container {
			border: 2px dashed #ccc;
			padding: 40px;
			text-align: center;
			margin-bottom: 20px;
			cursor: pointer;
			transition: all 0.3s;
		}
		.file-input-container:hover {
			border-color: #007bff;
			background: #f0f8ff;
		}
		.file-input-container.drag-over {
			border-color: #28a745;
			background: #e8f5e9;
		}
		.file-list {
			margin: 20px 0;
			max-height: 300px;
			overflow-y: auto;
		}
		.file-item {
			padding: 10px;
			background: #f5f5f5;
			margin-bottom: 5px;
			border-radius: 4px;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}
		.file-item .remove-btn {
			color: #dc3545;
			cursor: pointer;
			font-weight: bold;
		}
		.submit-btn {
			width: 100%;
			padding: 15px;
			background: #007bff;
			color: white;
			border: none;
			border-radius: 4px;
			font-size: 16px;
			cursor: pointer;
			transition: background 0.3s;
		}
		.submit-btn:hover {
			background: #0056b3;
		}
		.submit-btn:disabled {
			background: #ccc;
			cursor: not-allowed;
		}
		.results-container {
			margin-top: 30px;
		}
		.results-section {
			background: white;
			padding: 20px;
			border-radius: 8px;
			margin-bottom: 20px;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
		}
		.results-section h3 {
			margin-top: 0;
			padding-bottom: 10px;
			border-bottom: 2px solid #eee;
		}
		.success-section h3 {
			color: #28a745;
		}
		.failed-section h3 {
			color: #dc3545;
		}
		table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 15px;
		}
		table th, table td {
			padding: 12px;
			text-align: left;
			border-bottom: 1px solid #ddd;
		}
		table th {
			background: #f8f9fa;
			font-weight: bold;
		}
		table tr:hover {
			background: #f5f5f5;
		}
		.download-csv-btn {
			display: inline-block;
			padding: 10px 20px;
			background: #28a745;
			color: white;
			text-decoration: none;
			border-radius: 4px;
			margin-top: 10px;
		}
		.download-csv-btn:hover {
			background: #218838;
		}
		.alert {
			padding: 15px;
			margin-bottom: 20px;
			border-radius: 4px;
		}
		.alert-success {
			background: #d4edda;
			border: 1px solid #c3e6cb;
			color: #155724;
		}
		.alert-danger {
			background: #f8d7da;
			border: 1px solid #f5c6cb;
			color: #721c24;
		}
		.alert-info {
			background: #d1ecf1;
			border: 1px solid #bee5eb;
			color: #0c5460;
		}
		.loading {
			text-align: center;
			padding: 40px;
			display: none;
		}
		.loading.active {
			display: block;
		}
		.spinner {
			border: 4px solid #f3f3f3;
			border-top: 4px solid #007bff;
			border-radius: 50%;
			width: 50px;
			height: 50px;
			animation: spin 1s linear infinite;
			margin: 0 auto 20px;
		}
		@keyframes spin {
			0% { transform: rotate(0deg); }
			100% { transform: rotate(360deg); }
		}
		.badge {
			display: inline-block;
			padding: 3px 8px;
			border-radius: 3px;
			font-size: 12px;
			font-weight: bold;
		}
		.badge-success {
			background: #28a745;
			color: white;
		}
		.badge-danger {
			background: #dc3545;
			color: white;
		}
		.badge-warning {
			background: #ffc107;
			color: #333;
		}
		.warnings {
			color: #856404;
			font-size: 14px;
			margin-top: 5px;
		}
	</style>
</head>
<body>

<div class="upload-container">
	<div class="upload-header">
		<h1>Carga Masiva de Facturas XML</h1>
		<p>Seleccione múltiples archivos XML de CFDI para validarlos y cargarlos</p>
	</div>

	<?php if (!empty($success_message)): ?>
		<div class="alert alert-success"><?php echo $success_message; ?></div>
	<?php endif; ?>

	<?php if (!empty($error_message)): ?>
		<div class="alert alert-danger"><?php echo $error_message; ?></div>
	<?php endif; ?>

	<div class="upload-form">
		<form method="POST" enctype="multipart/form-data" id="uploadForm">
			<?php echo Form::csrf(); ?>
			
			<div class="file-input-container" id="dropZone">
				<input type="file" name="xml_files[]" id="fileInput" multiple accept=".xml" style="display:none;">
				<p style="margin: 0; font-size: 18px;">
					<strong>Arrastre archivos aquí</strong><br>
					o<br>
					<span style="color: #007bff; text-decoration: underline;">haga clic para seleccionar</span>
				</p>
				<p style="margin: 10px 0 0; color: #666; font-size: 14px;">
					Solo archivos XML • Máximo 5MB por archivo
				</p>
			</div>

			<div class="file-list" id="fileList"></div>

			<button type="submit" class="submit-btn" id="submitBtn" disabled>
				Procesar Facturas
			</button>
		</form>

		<div class="loading" id="loading">
			<div class="spinner"></div>
			<p>Validando facturas con el SAT, por favor espere...</p>
		</div>
	</div>

	<?php if (!empty($success_bills) || !empty($failed_bills)): ?>
	<div class="results-container">
		
		<?php if (!empty($success_bills)): ?>
		<div class="results-section success-section">
			<h3>✓ Facturas Cargadas Exitosamente (<?php echo count($success_bills); ?>)</h3>
			<table>
				<thead>
					<tr>
						<th>UUID</th>
						<th>RFC Emisor</th>
						<th>Fecha</th>
						<th>Total</th>
						<th>Estado SAT</th>
						<th>Advertencias</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($success_bills as $bill): ?>
					<tr>
						<td><?php echo substr($bill['uuid'], 0, 20) . '...'; ?></td>
						<td><?php echo $bill['rfc_emisor']; ?></td>
						<td><?php echo date('d/m/Y', strtotime($bill['fecha'])); ?></td>
						<td>$<?php echo number_format($bill['total'], 2); ?></td>
						<td>
							<?php if ($bill['sat_status'] === 'vigente'): ?>
								<span class="badge badge-success">Vigente</span>
							<?php elseif ($bill['sat_status'] === 'cancelado'): ?>
								<span class="badge badge-danger">Cancelado</span>
							<?php else: ?>
								<span class="badge badge-warning">No verificado</span>
							<?php endif; ?>
						</td>
						<td>
							<?php if (!empty($bill['warnings'])): ?>
								<div class="warnings">
									<?php foreach ($bill['warnings'] as $warning): ?>
										• <?php echo $warning; ?><br>
									<?php endforeach; ?>
								</div>
							<?php else: ?>
								—
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php endif; ?>

		<?php if (!empty($failed_bills)): ?>
		<div class="results-section failed-section">
			<h3>✗ Facturas Rechazadas (<?php echo count($failed_bills); ?>)</h3>
			
			<?php if (!empty($csv_path)): ?>
			<div class="alert alert-info">
				Se ha generado un reporte detallado de los errores:
				<a href="<?php echo $csv_path; ?>" class="download-csv-btn" download>
					Descargar Reporte CSV
				</a>
			</div>
			<?php endif; ?>

			<table>
				<thead>
					<tr>
						<th>Archivo</th>
						<th>UUID</th>
						<th>Total</th>
						<th>Errores</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($failed_bills as $bill): ?>
					<tr>
						<td><?php echo basename($bill['file']); ?></td>
						<td><?php echo !empty($bill['uuid']) ? substr($bill['uuid'], 0, 20) . '...' : 'N/A'; ?></td>
						<td><?php echo !empty($bill['total']) ? '$' . number_format($bill['total'], 2) : 'N/A'; ?></td>
						<td>
							<?php foreach ($bill['errors'] as $error): ?>
								<span style="color: #dc3545;">• <?php echo $error; ?></span><br>
							<?php endforeach; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php endif; ?>

	</div>
	<?php endif; ?>
</div>

<script>
// Variables globales
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const fileList = document.getElementById('fileList');
const submitBtn = document.getElementById('submitBtn');
const uploadForm = document.getElementById('uploadForm');
const loading = document.getElementById('loading');
let selectedFiles = [];

// Click en zona de drop abre selector
dropZone.addEventListener('click', () => fileInput.click());

// Prevenir comportamiento por defecto del drag
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
	dropZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
	e.preventDefault();
	e.stopPropagation();
}

// Resaltar zona cuando se arrastra sobre ella
['dragenter', 'dragover'].forEach(eventName => {
	dropZone.addEventListener(eventName, () => dropZone.classList.add('drag-over'), false);
});

['dragleave', 'drop'].forEach(eventName => {
	dropZone.addEventListener(eventName, () => dropZone.classList.remove('drag-over'), false);
});

// Manejar archivos dropeados
dropZone.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
	const dt = e.dataTransfer;
	const files = dt.files;
	handleFiles(files);
}

// Manejar archivos seleccionados
fileInput.addEventListener('change', function(e) {
	handleFiles(this.files);
});

function handleFiles(files) {
	const xmlFiles = Array.from(files).filter(file => {
		if (!file.name.toLowerCase().endsWith('.xml')) {
			alert(`${file.name} no es un archivo XML`);
			return false;
		}
		if (file.size > 5 * 1024 * 1024) {
			alert(`${file.name} es demasiado grande (máximo 5MB)`);
			return false;
		}
		return true;
	});

	// Agregar archivos nuevos
	xmlFiles.forEach(file => {
		if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
			selectedFiles.push(file);
		}
	});

	updateFileList();
	updateSubmitButton();
}

function updateFileList() {
	if (selectedFiles.length === 0) {
		fileList.innerHTML = '';
		return;
	}

	fileList.innerHTML = selectedFiles.map((file, index) => `
		<div class="file-item">
			<span>${file.name} (${formatFileSize(file.size)})</span>
			<span class="remove-btn" onclick="removeFile(${index})">✕</span>
		</div>
	`).join('');
}

function removeFile(index) {
	selectedFiles.splice(index, 1);
	updateFileList();
	updateSubmitButton();
}

function updateSubmitButton() {
	submitBtn.disabled = selectedFiles.length === 0;
	submitBtn.textContent = selectedFiles.length === 0 
		? 'Procesar Facturas' 
		: `Procesar ${selectedFiles.length} Factura${selectedFiles.length > 1 ? 's' : ''}`;
}

function formatFileSize(bytes) {
	if (bytes < 1024) return bytes + ' B';
	if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
	return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

// Mostrar loading al enviar
uploadForm.addEventListener('submit', function() {
	if (selectedFiles.length > 0) {
		loading.classList.add('active');
		submitBtn.disabled = true;
	}
});
</script>

</body>
</html>
