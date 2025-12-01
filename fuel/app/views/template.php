<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') : 'ERP Multi-tenant'; ?></title>
	<?php echo Asset::css('bootstrap.css'); ?>
	<style>
		body {
			padding-top: 20px;
			padding-bottom: 60px;
		}
		.navbar {
			margin-bottom: 20px;
		}
		.header-title {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
			background-color: #667eea;
			border-color: #667eea;
		}
		.btn-primary:hover {
			background-color: #764ba2;
			border-color: #764ba2;
		}
		.navbar-erp {
			background-color: #667eea;
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
			background-color: #764ba2;
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
			background-color: #667eea;
			color: #fff;
		}
	</style>
</head>
<body>
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
				<a class="navbar-brand" href="/">ERP Multi-tenant</a>
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
							<li><a href="<?php echo Uri::base(); ?>admin">Admin</a></li>
							<li><a href="<?php echo Uri::base(); ?>providers">Providers</a></li>
							<li><a href="<?php echo Uri::base(); ?>partners">Partners</a></li>
							<li><a href="<?php echo Uri::base(); ?>sellers">Sellers</a></li>
							<li><a href="<?php echo Uri::base(); ?>clients">Clients</a></li>
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
					<p>&copy; <?php echo date('Y'); ?> ERP Multi-tenant. Todos los derechos reservados.</p>
					<p><small>Versión: <?php echo Fuel::VERSION; ?></small></p>
				</div>
				<div class="col-md-6 text-right">
					<p class="text-muted">Página renderizada en {exec_time}s usando {mem_usage}mb de memoria.</p>
				</div>
			</div>
		</footer>
	</div>
	<?php echo Asset::js('jquery.min.js'); ?>
	<?php echo Asset::js('bootstrap.min.js'); ?>
</body>
</html>
