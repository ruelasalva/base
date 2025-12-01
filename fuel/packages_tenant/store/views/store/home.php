<div class="jumbotron" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
	<div class="container">
		<h1><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
		<p>Descubre nuestra amplia selección de productos de calidad.</p>
		<p>
			<a class="btn btn-primary btn-lg" href="<?php echo Uri::base(); ?>tienda/catalogo" role="button">
				<span class="glyphicon glyphicon-shopping-cart"></span> Ver Catálogo
			</a>
		</p>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h2><span class="glyphicon glyphicon-star"></span> Productos Destacados</h2>
			<hr>
		</div>
	</div>
	
	<div class="row">
		<?php if (empty($featured_products)): ?>
		<div class="col-md-12">
			<div class="alert alert-info text-center">
				<p><em>No hay productos destacados disponibles en este momento.</em></p>
				<a href="<?php echo Uri::base(); ?>tienda/catalogo" class="btn btn-primary">
					<span class="glyphicon glyphicon-search"></span> Explorar Catálogo
				</a>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>
