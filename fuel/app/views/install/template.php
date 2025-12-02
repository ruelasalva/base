<?php
/**
 * Función helper para obtener la clase CSS del paso
 */
function get_step_class($step_number)
{
	$current_action = Request::active()->action;
	$step_map = array(
		'index' => 1,
		'configurar' => 2,
		'ejecutar' => 3,
		'auto_install' => 3,
		'crear_admin' => 4,
		'completado' => 5,
	);

	$current_step = isset($step_map[$current_action]) ? $step_map[$current_action] : 1;

	if ($step_number < $current_step)
	{
		return 'completed';
	}
	elseif ($step_number === $current_step)
	{
		return 'active';
	}

	return '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') : 'Instalador'; ?> - ERP Multi-tenant</title>
	<?php echo Asset::css('bootstrap.css'); ?>
	<style>
		:root {
			--primary-color: #667eea;
			--primary-hover: #5a6fd6;
			--success-color: #28a745;
			--danger-color: #dc3545;
			--warning-color: #ffc107;
			--info-color: #17a2b8;
			--dark-color: #343a40;
		}

		body {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			min-height: 100vh;
			padding: 40px 0;
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
		}

		.installer-container {
			max-width: 900px;
			margin: 0 auto;
		}

		.installer-header {
			text-align: center;
			color: #fff;
			margin-bottom: 30px;
		}

		.installer-header h1 {
			font-size: 2.5rem;
			font-weight: 300;
			margin-bottom: 10px;
		}

		.installer-header p {
			font-size: 1.1rem;
			opacity: 0.9;
		}

		.installer-card {
			background: #fff;
			border-radius: 10px;
			box-shadow: 0 10px 40px rgba(0,0,0,0.2);
			overflow: hidden;
		}

		.installer-progress {
			background: #f8f9fa;
			padding: 20px 30px;
			border-bottom: 1px solid #e9ecef;
		}

		.progress-steps {
			display: flex;
			justify-content: space-between;
			margin: 0;
			padding: 0;
			list-style: none;
			position: relative;
		}

		.progress-steps::before {
			content: '';
			position: absolute;
			top: 20px;
			left: 40px;
			right: 40px;
			height: 3px;
			background: #dee2e6;
			z-index: 0;
		}

		.progress-step {
			display: flex;
			flex-direction: column;
			align-items: center;
			position: relative;
			z-index: 1;
			flex: 1;
		}

		.step-number {
			width: 40px;
			height: 40px;
			border-radius: 50%;
			background: #dee2e6;
			color: #6c757d;
			display: flex;
			align-items: center;
			justify-content: center;
			font-weight: bold;
			margin-bottom: 8px;
			border: 3px solid #fff;
			box-shadow: 0 2px 5px rgba(0,0,0,0.1);
		}

		.progress-step.active .step-number {
			background: var(--primary-color);
			color: #fff;
		}

		.progress-step.completed .step-number {
			background: var(--success-color);
			color: #fff;
		}

		.step-label {
			font-size: 0.85rem;
			color: #6c757d;
			text-align: center;
		}

		.progress-step.active .step-label,
		.progress-step.completed .step-label {
			color: var(--dark-color);
			font-weight: 500;
		}

		.installer-content {
			padding: 30px;
		}

		.installer-footer {
			background: #f8f9fa;
			padding: 15px 30px;
			border-top: 1px solid #e9ecef;
			text-align: center;
		}

		.installer-footer p {
			margin: 0;
			color: #6c757d;
			font-size: 0.9rem;
		}

		.btn-installer {
			background: var(--primary-color);
			border: none;
			color: #fff;
			padding: 12px 30px;
			border-radius: 5px;
			font-size: 1rem;
			font-weight: 500;
			transition: all 0.3s ease;
		}

		.btn-installer:hover {
			background: var(--primary-hover);
			color: #fff;
			transform: translateY(-2px);
			box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
		}

		.btn-installer-outline {
			background: transparent;
			border: 2px solid var(--primary-color);
			color: var(--primary-color);
			padding: 10px 28px;
		}

		.btn-installer-outline:hover {
			background: var(--primary-color);
			color: #fff;
		}

		.status-badge {
			display: inline-block;
			padding: 5px 12px;
			border-radius: 20px;
			font-size: 0.85rem;
			font-weight: 500;
		}

		.status-success {
			background: #d4edda;
			color: #155724;
		}

		.status-warning {
			background: #fff3cd;
			color: #856404;
		}

		.status-danger {
			background: #f8d7da;
			color: #721c24;
		}

		.status-info {
			background: #d1ecf1;
			color: #0c5460;
		}

		.migration-list {
			list-style: none;
			padding: 0;
			margin: 0;
		}

		.migration-item {
			display: flex;
			align-items: center;
			padding: 12px 15px;
			border-bottom: 1px solid #e9ecef;
		}

		.migration-item:last-child {
			border-bottom: none;
		}

		.migration-checkbox {
			margin-right: 15px;
		}

		.migration-info {
			flex: 1;
		}

		.migration-name {
			font-weight: 500;
			color: var(--dark-color);
		}

		.migration-description {
			font-size: 0.85rem;
			color: #6c757d;
		}

		.migration-status {
			margin-left: auto;
		}

		.form-control {
			border-radius: 5px;
			border: 2px solid #e9ecef;
			padding: 12px 15px;
			font-size: 1rem;
			transition: border-color 0.3s ease;
		}

		.form-control:focus {
			border-color: var(--primary-color);
			box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
		}

		.form-label {
			font-weight: 500;
			color: var(--dark-color);
			margin-bottom: 8px;
		}

		.icon-check {
			color: var(--success-color);
		}

		.icon-times {
			color: var(--danger-color);
		}

		.icon-warning {
			color: var(--warning-color);
		}

		.alert {
			border-radius: 5px;
			border: none;
			padding: 15px 20px;
		}

		.alert-success {
			background: #d4edda;
			color: #155724;
		}

		.alert-danger {
			background: #f8d7da;
			color: #721c24;
		}

		.alert-info {
			background: #d1ecf1;
			color: #0c5460;
		}

		.alert-warning {
			background: #fff3cd;
			color: #856404;
		}

		@media (max-width: 768px) {
			.progress-steps {
				flex-wrap: wrap;
			}

			.progress-step {
				flex: 0 0 50%;
				margin-bottom: 15px;
			}

			.progress-steps::before {
				display: none;
			}

			.step-label {
				font-size: 0.75rem;
			}

			.installer-header h1 {
				font-size: 1.8rem;
			}

			.installer-content {
				padding: 20px;
			}
		}
	</style>
</head>
<body>
	<div class="container installer-container">
		<!-- Header -->
		<div class="installer-header">
			<h1>
				<span class="glyphicon glyphicon-cog"></span>
				<?php echo isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') : 'Instalador del Sistema'; ?>
			</h1>
			<p>Configure y prepare su sistema ERP Multi-tenant</p>
		</div>

		<!-- Card Principal -->
		<div class="installer-card">
			<!-- Barra de Progreso -->
			<div class="installer-progress">
				<ul class="progress-steps">
					<li class="progress-step <?php echo get_step_class(1); ?>">
						<span class="step-number">1</span>
						<span class="step-label">Inicio</span>
					</li>
					<li class="progress-step <?php echo get_step_class(2); ?>">
						<span class="step-number">2</span>
						<span class="step-label">Base de Datos</span>
					</li>
					<li class="progress-step <?php echo get_step_class(3); ?>">
						<span class="step-number">3</span>
						<span class="step-label">Migraciones</span>
					</li>
					<li class="progress-step <?php echo get_step_class(4); ?>">
						<span class="step-number">4</span>
						<span class="step-label">Administrador</span>
					</li>
					<li class="progress-step <?php echo get_step_class(5); ?>">
						<span class="step-number">5</span>
						<span class="step-label">Completado</span>
					</li>
				</ul>
			</div>

			<!-- Contenido -->
			<div class="installer-content">
				<?php if (Session::get_flash('success')): ?>
				<div class="alert alert-success">
					<span class="glyphicon glyphicon-ok-circle"></span>
					<?php echo Session::get_flash('success'); ?>
				</div>
				<?php endif; ?>

				<?php if (Session::get_flash('error')): ?>
				<div class="alert alert-danger">
					<span class="glyphicon glyphicon-exclamation-sign"></span>
					<?php echo Session::get_flash('error'); ?>
				</div>
				<?php endif; ?>

				<?php echo isset($content) ? $content : ''; ?>
			</div>

			<!-- Footer -->
			<div class="installer-footer">
				<p>ERP Multi-tenant &copy; <?php echo date('Y'); ?> - Instalador v1.0</p>
			</div>
		</div>
	</div>

	<?php echo Asset::js('jquery.min.js'); ?>
	<?php echo Asset::js('bootstrap.min.js'); ?>
</body>
</html>
