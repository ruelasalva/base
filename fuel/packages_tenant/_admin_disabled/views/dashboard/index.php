<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo isset($page_title) ? $page_title : 'Admin'; ?> - <?php echo isset($module_name) ? $module_name : 'ERP Multi-Tenant'; ?></title>
	<style>
		* { margin: 0; padding: 0; box-sizing: border-box; }
		body { font-family: Arial, sans-serif; background: #f5f5f5; }
		.header { background: #007bff; color: white; padding: 20px; }
		.header h1 { font-size: 24px; }
		.container { max-width: 1200px; margin: 20px auto; padding: 0 20px; }
		.stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0; }
		.stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
		.stat-card h3 { color: #666; font-size: 14px; margin-bottom: 10px; }
		.stat-card .number { font-size: 32px; font-weight: bold; color: #007bff; }
		.quick-links { margin: 20px 0; }
		.quick-links h2 { margin-bottom: 15px; color: #333; }
		.links-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
		.link-card { background: white; padding: 20px; border-radius: 8px; text-decoration: none; color: #333; display: block; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.2s; }
		.link-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
		.link-card h4 { color: #007bff; margin-bottom: 5px; }
		.success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
	</style>
</head>
<body>
	<div class="header">
		<div class="container">
			<h1>🏢 <?php echo isset($module_name) ? $module_name : 'Panel de Administración'; ?></h1>
		</div>
	</div>
	
	<div class="container">
		<div class="success">
			<strong>✓ Módulo Admin cargado correctamente!</strong><br>
			El módulo tenant está funcionando. HTTP_HOST: <?php echo isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'; ?>
		</div>
		
		<h2>Estadísticas</h2>
		<div class="stats">
			<?php if (isset($stats) && is_array($stats)): ?>
				<?php foreach ($stats as $stat): ?>
					<div class="stat-card">
						<h3><?php echo $stat['title']; ?></h3>
						<div class="number"><?php echo $stat['count']; ?></div>
						<a href="<?php echo $stat['link']; ?>" style="color: #007bff; text-decoration: none;">Ver más →</a>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		
		<div class="quick-links">
			<h2>Accesos Rápidos</h2>
			<div class="links-grid">
				<?php if (isset($quick_links) && is_array($quick_links)): ?>
					<?php foreach ($quick_links as $link): ?>
						<a href="<?php echo $link['url']; ?>" class="link-card">
							<h4><?php echo $link['title']; ?></h4>
							<p style="color: #666; font-size: 14px;">Ir al módulo</p>
						</a>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</body>
</html>
