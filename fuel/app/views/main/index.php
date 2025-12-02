<?php
/**
 * Vista Principal del ERP Multi-Tenant
 * Utiliza configuraciones desde fuel/app/config/site.php
 * Los estilos CSS están en public/assets/css/custom.css
 */

// Cargar configuración del sitio
$site_config = Config::load('site', true);
?>

<!-- Hero Section -->
<div class="hero-section text-center">
	<div class="container">
		<h1><?php echo isset($site_config['hero']['title']) ? $site_config['hero']['title'] : 'ERP Multi-Tenant'; ?></h1>
		<p class="lead"><?php echo isset($site_config['hero']['description']) ? $site_config['hero']['description'] : 'Sistema de Gestión Empresarial'; ?></p>
		<p>
			<a class="btn btn-light btn-lg" href="<?php echo Uri::base(); ?>store" role="button" style="margin: 5px;">
				<span class="glyphicon glyphicon-shopping-cart"></span> Ir a la Tienda
			</a>
			<a class="btn btn-outline btn-lg" href="<?php echo Uri::base(); ?>landing" role="button" style="margin: 5px; background: rgba(255,255,255,0.2); color: white; border: 2px solid white;">
				<span class="glyphicon glyphicon-globe"></span> Ver Landing Page
			</a>
		</p>
	</div>
</div>

<!-- Alert de Instalación -->
<?php if (isset($site_config['installation']['required']) && $site_config['installation']['required']): ?>
<div class="install-alert">
	<h4>
		<span class="glyphicon glyphicon-warning-sign"></span> 
		<?php echo isset($site_config['installation']['alert_message']) ? $site_config['installation']['alert_message'] : '¡Atención! El sistema requiere instalación inicial'; ?>
	</h4>
	<p><?php echo isset($site_config['installation']['alert_details']) ? $site_config['installation']['alert_details'] : 'Necesitas configurar la base de datos y los módulos antes de comenzar.'; ?></p>
	<a href="<?php echo Uri::base(); ?>install" class="btn btn-warning btn-lg">
		<span class="glyphicon glyphicon-cog"></span> 
		<?php echo isset($site_config['installation']['button_text']) ? $site_config['installation']['button_text'] : 'Instalar Ahora'; ?>
	</a>
</div>
<?php endif; ?>

<!-- Módulos del ERP -->
<div class="row">
	<div class="col-md-12">
		<h3 class="section-title">
			<span class="glyphicon glyphicon-th-large"></span> Módulos del ERP
		</h3>
	</div>
</div>

<div class="row">
	<?php
	// Cargar módulos desde configuración
	$modules = isset($site_config['modules']) ? $site_config['modules'] : array();
	
	foreach ($modules as $module_key => $module):
		if (!isset($module['enabled']) || !$module['enabled']) continue;
		
		$name = isset($module['name']) ? $module['name'] : ucfirst($module_key);
		$description = isset($module['description']) ? $module['description'] : '';
		$icon = isset($module['icon']) ? $module['icon'] : 'glyphicon-th';
		$color = isset($module['color']) ? $module['color'] : 'default';
		
		// Color personalizado para Socios
		$custom_style = '';
		$custom_footer_style = '';
		if ($module_key === 'partners') {
			$custom_style = 'style="border-color: #9b59b6;"';
			$custom_footer_style = 'style="background: #9b59b6; color: white; border-color: #8e44ad;"';
		}
	?>
	<div class="col-lg-2 col-md-4 col-sm-6">
		<div class="panel panel-<?php echo $color; ?> module-card" <?php echo $custom_style; ?>>
			<div class="panel-heading" <?php echo ($module_key === 'partners') ? 'style="background: #9b59b6; color: white; border-color: #9b59b6;"' : ''; ?>>
				<h3 class="panel-title">
					<span class="glyphicon <?php echo $icon; ?>"></span> <?php echo $name; ?>
				</h3>
			</div>
			<div class="panel-body">
				<p><?php echo $description; ?></p>
			</div>
			<div class="panel-footer">
				<a href="<?php echo Uri::base() . $module_key; ?>" class="btn btn-<?php echo $color; ?> btn-block" <?php echo $custom_footer_style; ?>>
					Acceder
				</a>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
</div>

<!-- Frontend Público -->
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		<h3 class="section-title">
			<span class="glyphicon glyphicon-globe"></span> Frontend Público
		</h3>
	</div>
</div>

<div class="row">
	<?php
	$frontend = isset($site_config['frontend']) ? $site_config['frontend'] : array();
	foreach ($frontend as $key => $item):
		if (!isset($item['enabled']) || !$item['enabled']) continue;
	?>
	<div class="col-md-6">
		<div class="panel panel-default module-card">
			<div class="panel-heading">
				<h3 class="panel-title">
					<span class="glyphicon <?php echo isset($item['icon']) ? $item['icon'] : 'glyphicon-th'; ?>"></span> 
					<?php echo isset($item['name']) ? $item['name'] : ucfirst($key); ?>
				</h3>
			</div>
			<div class="panel-body">
				<p><?php echo isset($item['description']) ? $item['description'] : ''; ?></p>
			</div>
			<div class="panel-footer">
				<a href="<?php echo Uri::base() . $key; ?>" class="btn btn-primary btn-block">
					<span class="glyphicon <?php echo isset($item['icon']) ? $item['icon'] : 'glyphicon-th'; ?>"></span> 
					Ver <?php echo isset($item['name']) ? $item['name'] : ucfirst($key); ?>
				</a>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
</div>

<!-- Arquitectura Multi-tenant -->
<div class="row" style="margin-top: 40px;">
	<div class="col-md-12">
		<h3 class="section-title">
			<span class="glyphicon glyphicon-cloud"></span> Arquitectura Multi-tenant
		</h3>
	</div>
</div>

<div class="row">
	<?php
	$architecture = isset($site_config['architecture']) ? $site_config['architecture'] : array();
	foreach ($architecture as $key => $item):
	?>
	<div class="col-md-4">
		<div class="panel panel-default module-card">
			<div class="panel-body text-center">
				<span class="glyphicon <?php echo isset($item['icon']) ? $item['icon'] : 'glyphicon-th'; ?>"></span>
				<h4><?php echo isset($item['title']) ? $item['title'] : strtoupper($key); ?></h4>
				<p><?php echo isset($item['description']) ? $item['description'] : ''; ?></p>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
</div>
