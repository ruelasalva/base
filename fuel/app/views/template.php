<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	// Cargar configuración del sitio desde base de datos
	$site_config_db = Model_SiteConfig::get_config(1); // TODO: tenant_id dinámico
	$site_config = Config::load('site', true);
	$site_name = $site_config_db ? $site_config_db->site_name : (isset($site_config['site_name']) ? $site_config['site_name'] : 'ERP Multi-tenant');
	?>
	<title><?php echo isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') : $site_name; ?></title>
	
	<!-- SEO Meta Tags -->
	<?php if ($site_config_db): ?>
		<?php if ($site_config_db->meta_description): ?>
			<meta name="description" content="<?php echo htmlspecialchars($site_config_db->meta_description, ENT_QUOTES, 'UTF-8'); ?>">
		<?php endif; ?>
		<?php if ($site_config_db->meta_keywords): ?>
			<meta name="keywords" content="<?php echo htmlspecialchars($site_config_db->meta_keywords, ENT_QUOTES, 'UTF-8'); ?>">
		<?php endif; ?>
		<?php if ($site_config_db->meta_author): ?>
			<meta name="author" content="<?php echo htmlspecialchars($site_config_db->meta_author, ENT_QUOTES, 'UTF-8'); ?>">
		<?php endif; ?>
		<?php if ($site_config_db->theme_color): ?>
			<meta name="theme-color" content="<?php echo $site_config_db->theme_color; ?>">
		<?php endif; ?>
		
		<!-- Open Graph -->
		<?php if ($site_config_db->og_image): ?>
			<meta property="og:image" content="<?php echo $site_config_db->og_image; ?>">
		<?php endif; ?>
		<meta property="og:title" content="<?php echo isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') : $site_name; ?>">
		<?php if ($site_config_db->meta_description): ?>
			<meta property="og:description" content="<?php echo htmlspecialchars($site_config_db->meta_description, ENT_QUOTES, 'UTF-8'); ?>">
		<?php endif; ?>
		
		<!-- Favicons -->
		<?php if ($site_config_db->favicon_16): ?>
			<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $site_config_db->favicon_16; ?>">
		<?php endif; ?>
		<?php if ($site_config_db->favicon_32): ?>
			<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $site_config_db->favicon_32; ?>">
			<link rel="shortcut icon" href="<?php echo $site_config_db->favicon_32; ?>">
		<?php endif; ?>
		<?php if ($site_config_db->favicon_57): ?>
			<link rel="apple-touch-icon-precomposed" href="<?php echo $site_config_db->favicon_57; ?>">
		<?php endif; ?>
		<?php if ($site_config_db->favicon_72): ?>
			<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $site_config_db->favicon_72; ?>">
		<?php endif; ?>
		<?php if ($site_config_db->favicon_114): ?>
			<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $site_config_db->favicon_114; ?>">
		<?php endif; ?>
		<?php if ($site_config_db->favicon_144): ?>
			<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $site_config_db->favicon_144; ?>">
		<?php endif; ?>
	<?php endif; ?>
	<?php echo Asset::css('bootstrap.css'); ?>
	<?php echo Asset::css('bootstrap.css'); ?>
	<?php echo Asset::css('custom.css'); ?>
	<style>
		body {
			padding-top: 20px;
			padding-bottom: 60px;
		}
		.navbar {
			margin-bottom: 20px;
		}
		<?php
		// Aplicar colores del tema desde configuración
		$gradient_start = isset($site_config['theme']['gradient_start']) ? $site_config['theme']['gradient_start'] : '#667eea';
		$gradient_end = isset($site_config['theme']['gradient_end']) ? $site_config['theme']['gradient_end'] : '#764ba2';
		$gradient_angle = isset($site_config['theme']['gradient_angle']) ? $site_config['theme']['gradient_angle'] : '135deg';
		?>
		.header-title {
			background: linear-gradient(<?php echo $gradient_angle; ?>, <?php echo $gradient_start; ?> 0%, <?php echo $gradient_end; ?> 100%);
			color: white;
			padding: 30px 0;
			margin-bottom: 30px;
		}
		.header-title h1 {
			margin: 0;
		}
		.content-area {
			min-height: 400px;
		}
		.footer {
			margin-top: 40px;
			padding: 20px 0;
			border-top: 1px solid #e5e5e5;
		}
		.btn-primary {
			background-color: <?php echo $gradient_start; ?>;
			border-color: <?php echo $gradient_start; ?>;
		}
		.btn-primary:hover {
			background-color: <?php echo $gradient_end; ?>;
			border-color: <?php echo $gradient_end; ?>;
		}
		.navbar-erp {
			background-color: <?php echo $gradient_start; ?>;
			border-color: #5a6fd6;
		}
		.navbar-erp .navbar-brand,
		.navbar-erp .navbar-nav > li > a {
			color: #fff;
		}
		.navbar-erp .navbar-nav > li > a:hover,
		.navbar-erp .navbar-nav > li > a:focus {
			color: #e0e0e0;
			background-color: #5a6fd6;
		}
		.navbar-erp .navbar-nav > .active > a,
		.navbar-erp .navbar-nav > .active > a:hover,
		.navbar-erp .navbar-nav > .active > a:focus {
			background-color: <?php echo $gradient_end; ?>;
			color: #fff;
		}
		.navbar-erp .navbar-toggle {
			border-color: #fff;
		}
		.navbar-erp .navbar-toggle .icon-bar {
			background-color: #fff;
		}
		.dropdown-menu > li > a {
			color: #333;
		}
		.dropdown-menu > li > a:hover {
			background-color: <?php echo $gradient_start; ?>;
			color: #fff;
		}
	</style>
	
	<!-- Scripts de Tracking y Analytics -->
	<?php if ($site_config_db): ?>
		<?php echo $site_config_db->get_all_head_scripts(); ?>
	<?php endif; ?>
</head>
<body>
	<!-- Google Tag Manager (body) -->
	<?php if ($site_config_db): ?>
		<?php echo $site_config_db->get_all_body_scripts(); ?>
	<?php endif; ?>
	
	<!-- Navegación Principal -->
	<nav class="navbar navbar-erp">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/"><?php echo $site_name; ?></a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li><a href="<?php echo Uri::base(); ?>">Inicio</a></li>
					<li><a href="<?php echo Uri::base(); ?>landing">Landing</a></li>
					<li><a href="<?php echo Uri::base(); ?>tienda">Tienda</a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
							Backends <span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo Uri::base(); ?>admin">Administración</a></li>
							<li><a href="<?php echo Uri::base(); ?>providers">Proveedores</a></li>
							<li><a href="<?php echo Uri::base(); ?>partners">Socios</a></li>
							<li><a href="<?php echo Uri::base(); ?>sellers">Vendedores</a></li>
							<li><a href="<?php echo Uri::base(); ?>clients">Clientes</a></li>
						</ul>
					</li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="<?php echo Uri::base(); ?>contacto">Contacto</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<!-- Header -->
	<div class="header-title">
		<div class="container">
			<h1><?php echo isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') : 'Bienvenido'; ?></h1>
		</div>
	</div>

	<!-- Contenido Principal -->
	<div class="container">
		<?php if (Session::get_flash('success')): ?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<?php echo Session::get_flash('success'); ?>
		</div>
		<?php endif; ?>

		<?php if (Session::get_flash('error')): ?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<?php echo Session::get_flash('error'); ?>
		</div>
		<?php endif; ?>

		<div class="content-area">
			<?php echo isset($content) ? $content : ''; ?>
		</div>

		<!-- Footer -->
		<footer class="footer">
			<div class="row">
				<div class="col-md-6">
					<?php
					$footer_text = isset($site_config['footer']['text']) ? $site_config['footer']['text'] : '&copy; ' . date('Y') . ' ERP Multi-tenant. Todos los derechos reservados.';
					$powered_by = isset($site_config['footer']['powered_by']) ? $site_config['footer']['powered_by'] : 'Powered by FuelPHP';
					?>
					<p><?php echo $footer_text; ?></p>
					<p><small><?php echo $powered_by; ?> <?php echo Fuel::VERSION; ?></small></p>
				</div>
				<div class="col-md-6 text-right">
					<p class="text-muted">Página renderizada en {exec_time}s usando {mem_usage}mb de memoria.</p>
				</div>
			</div>
		</footer>
	</div>
	
	<!-- Banner de Cookies -->
	<?php if ($site_config_db && $site_config_db->cookie_consent_enabled): ?>
	<div id="cookie-consent-banner" class="cookie-banner" style="display: none;">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-md-8">
					<p class="mb-0">
						<i class="fas fa-cookie-bite me-2"></i>
						<?php echo htmlspecialchars($site_config_db->cookie_message ?: 'Este sitio utiliza cookies para mejorar tu experiencia.', ENT_QUOTES, 'UTF-8'); ?>
						<?php if ($site_config_db->privacy_policy_url): ?>
							<a href="<?php echo $site_config_db->privacy_policy_url; ?>" class="cookie-link">Política de Privacidad</a>
						<?php endif; ?>
					</p>
				</div>
				<div class="col-md-4 text-end">
					<button onclick="acceptCookies()" class="btn btn-sm btn-primary">Aceptar</button>
					<button onclick="declineCookies()" class="btn btn-sm btn-secondary">Rechazar</button>
				</div>
			</div>
		</div>
	</div>
	<style>
		.cookie-banner {
			position: fixed;
			bottom: 0;
			left: 0;
			right: 0;
			background: rgba(0, 0, 0, 0.95);
			color: white;
			padding: 15px 0;
			z-index: 9999;
			box-shadow: 0 -2px 10px rgba(0,0,0,0.3);
		}
		.cookie-banner p {
			color: white;
		}
		.cookie-link {
			color: #4db8ff;
			text-decoration: underline;
			margin-left: 10px;
		}
	</style>
	<script>
		// Verificar si ya aceptó cookies
		if (!localStorage.getItem('cookie_consent')) {
			document.getElementById('cookie-consent-banner').style.display = 'block';
		}
		
		function acceptCookies() {
			localStorage.setItem('cookie_consent', 'accepted');
			document.getElementById('cookie-consent-banner').style.display = 'none';
		}
		
		function declineCookies() {
			localStorage.setItem('cookie_consent', 'declined');
			document.getElementById('cookie-consent-banner').style.display = 'none';
		}
	</script>
	<?php endif; ?>
	
	<?php echo Asset::js('jquery.min.js'); ?>
	<?php echo Asset::js('bootstrap.min.js'); ?>
</body>
</html>
