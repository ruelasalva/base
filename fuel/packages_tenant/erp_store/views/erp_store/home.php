<!-- Hero Section -->
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

<!-- Featured Products -->
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
		<?php else: ?>
		<?php foreach ($featured_products as $product): ?>
		<div class="col-md-3 col-sm-6">
			<div class="panel panel-default">
				<div class="panel-body text-center">
					<img src="<?php echo Uri::base(); ?>assets/img/placeholder.png" alt="Producto" class="img-responsive" style="max-height: 150px; margin: 0 auto;">
				</div>
				<div class="panel-footer">
					<h4><?php echo htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h4>
					<p class="text-success"><strong>$<?php echo htmlspecialchars($product['price'] ?? '0.00', ENT_QUOTES, 'UTF-8'); ?></strong></p>
					<a href="<?php echo Uri::base(); ?>tienda/producto/<?php echo htmlspecialchars($product['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary btn-block">Ver Producto</a>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
		<?php endif; ?>
	</div>
	
	<!-- Categories -->
	<div class="row" style="margin-top: 30px;">
		<div class="col-md-12">
			<h2><span class="glyphicon glyphicon-th-large"></span> Categorías</h2>
			<hr>
		</div>
	</div>
	
	<div class="row">
		<?php if (empty($categories)): ?>
		<div class="col-md-12">
			<p class="text-muted text-center"><em>No hay categorías disponibles.</em></p>
		</div>
		<?php else: ?>
		<?php foreach ($categories as $category): ?>
		<div class="col-md-4 col-sm-6">
			<a href="<?php echo Uri::base(); ?>tienda/catalogo/categoria/<?php echo htmlspecialchars($category['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-default btn-lg btn-block" style="margin-bottom: 10px;">
				<?php echo htmlspecialchars($category['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
			</a>
		</div>
		<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>
