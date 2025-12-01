<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h1><span class="glyphicon glyphicon-search"></span> <?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
			<hr>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<form method="get" action="<?php echo Uri::base(); ?>tienda/buscar">
				<div class="input-group input-group-lg">
					<input type="text" class="form-control" name="q" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Buscar productos...">
					<span class="input-group-btn">
						<button class="btn btn-primary" type="submit">
							<span class="glyphicon glyphicon-search"></span> Buscar
						</button>
					</span>
				</div>
			</form>
		</div>
	</div>
	
	<div class="row" style="margin-top: 30px;">
		<div class="col-md-12">
			<?php if (empty($query)): ?>
			<div class="alert alert-info text-center">
				<span class="glyphicon glyphicon-info-sign"></span>
				Ingresa un término de búsqueda para encontrar productos.
			</div>
			<?php elseif (empty($results)): ?>
			<div class="alert alert-warning text-center">
				<span class="glyphicon glyphicon-exclamation-sign" style="font-size: 48px;"></span>
				<h3>No se encontraron resultados</h3>
				<p>No hay productos que coincidan con "<strong><?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?></strong>"</p>
				<hr>
				<p>Sugerencias:</p>
				<ul class="list-unstyled">
					<li>Verifica que las palabras estén escritas correctamente</li>
					<li>Intenta con términos más generales</li>
					<li>Utiliza menos palabras</li>
				</ul>
				<a href="<?php echo Uri::base(); ?>tienda/catalogo" class="btn btn-primary">
					<span class="glyphicon glyphicon-th"></span> Ver Catálogo Completo
				</a>
			</div>
			<?php else: ?>
			<p class="text-muted">
				Se encontraron <strong><?php echo htmlspecialchars($total_results, ENT_QUOTES, 'UTF-8'); ?></strong> resultados para 
				"<strong><?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?></strong>"
			</p>
			<hr>
			<div class="row">
				<!-- Results will be displayed here -->
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
