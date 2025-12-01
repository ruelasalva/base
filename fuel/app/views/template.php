<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo isset($title) ? $title : 'Aplicación Base'; ?></title>
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
	</style>
</head>
<body>
	<!-- Navegación -->
	<nav class="navbar navbar-default">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/">Aplicación Base</a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li class="active"><a href="/">Inicio</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<!-- Header -->
	<div class="header-title">
		<div class="container">
			<h1><?php echo isset($title) ? $title : 'Bienvenido'; ?></h1>
		</div>
	</div>

	<!-- Contenido Principal -->
	<div class="container">
		<div class="content-area">
			<?php echo isset($content) ? $content : ''; ?>
		</div>

		<!-- Footer -->
		<footer class="footer">
			<div class="row">
				<div class="col-md-6">
					<p>&copy; <?php echo date('Y'); ?> Aplicación Base. Todos los derechos reservados.</p>
					<p><small>Versión: <?php echo Fuel::VERSION; ?></small></p>
				</div>
				<div class="col-md-6 text-right">
					<p class="text-muted">Página renderizada en {exec_time}s usando {mem_usage}mb de memoria.</p>
				</div>
			</div>
		</footer>
	</div>
</body>
</html>
