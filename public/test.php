<!DOCTYPE html>
<html>
<head>
	<title>Test - Sistema Base</title>
	<style>
		body { font-family: Arial, sans-serif; margin: 20px; }
		.ok { color: green; }
		.error { color: red; }
		.info { color: blue; }
		pre { background: #f4f4f4; padding: 10px; border: 1px solid #ddd; }
	</style>
</head>
<body>
	<h1>Diagnóstico del Sistema</h1>
	
	<h2>1. PHP Funcionando</h2>
	<p class="ok">✓ PHP está funcionando correctamente</p>
	<p>Versión de PHP: <strong><?php echo PHP_VERSION; ?></strong></p>
	
	<h2>2. Rutas</h2>
	<ul>
		<li><a href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/">Ir a la página principal</a></li>
		<li><a href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/diagnostico">Ir al diagnóstico del sistema</a></li>
		<li><a href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/install">Ir al instalador</a></li>
	</ul>
	
	<h2>3. Variables del Servidor</h2>
	<pre><?php
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "SCRIPT_FILENAME: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "\n";
	?></pre>
	
	<h2>4. Archivos Críticos</h2>
	<?php
	$files = [
		'../index.php' => 'Index principal',
		'../fuel/app/bootstrap.php' => 'Bootstrap',
		'../fuel/app/config/config.php' => 'Configuración',
		'../fuel/app/config/routes.php' => 'Rutas',
		'../fuel/app/classes/controller/main.php' => 'Controlador Main',
		'../fuel/app/views/main/index.php' => 'Vista Main',
		'../fuel/app/views/template.php' => 'Template'
	];
	
	foreach ($files as $file => $name) {
		$path = __DIR__ . '/' . $file;
		if (file_exists($path)) {
			echo "<p class='ok'>✓ $name existe</p>";
		} else {
			echo "<p class='error'>✗ $name NO EXISTE</p>";
		}
	}
	?>
	
	<h2>5. .htaccess</h2>
	<?php
	$htaccess = __DIR__ . '/.htaccess';
	if (file_exists($htaccess)) {
		echo "<p class='ok'>✓ .htaccess existe</p>";
		echo "<h3>Contenido:</h3>";
		echo "<pre>" . htmlspecialchars(file_get_contents($htaccess)) . "</pre>";
	} else {
		echo "<p class='error'>✗ .htaccess NO EXISTE</p>";
	}
	?>
	
	<hr>
	<p><small>Test generado: <?php echo date('Y-m-d H:i:s'); ?></small></p>
</body>
</html>
