<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h1><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
			<hr>
		</div>
	</div>
	
	<!-- Search Bar -->
	<div class="row">
		<div class="col-md-12">
			<form action="<?php echo Uri::base(); ?>tienda/buscar" method="GET" class="form-inline">
				<div class="form-group" style="width: 100%;">
					<div class="input-group" style="width: 100%;">
						<input type="text" class="form-control" name="q" placeholder="Buscar productos..." value="<?php echo htmlspecialchars($query ?? '', ENT_QUOTES, 'UTF-8'); ?>">
						<span class="input-group-btn">
							<button class="btn btn-primary" type="submit">
								<span class="glyphicon glyphicon-search"></span> Buscar
							</button>
						</span>
					</div>
				</div>
			</form>
		</div>
	</div>
	
	<div class="row" style="margin-top: 20px;">
		<!-- Sidebar with Filters -->
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><span class="glyphicon glyphicon-filter"></span> Filtros</h3>
				</div>
				<div class="panel-body">
					<h5>Categorías</h5>
					<?php if (empty($categories)): ?>
					<p class="text-muted"><em>No hay categorías disponibles.</em></p>
					<?php else: ?>
					<ul class="list-unstyled">
						<?php foreach ($categories as $category): ?>
						<li>
							<a href="<?php echo Uri::base(); ?>tienda/catalogo/categoria/<?php echo htmlspecialchars($category['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
								<?php echo htmlspecialchars($category['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
							</a>
						</li>
						<?php endforeach; ?>
					</ul>
					<?php endif; ?>
					
					<hr>
					
					<h5>Precio</h5>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Mínimo">
					</div>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Máximo">
					</div>
					<button class="btn btn-default btn-block">Aplicar Filtro</button>
				</div>
			</div>
		</div>
		
		<!-- Product Grid -->
		<div class="col-md-9">
			<div class="row">
				<?php if (empty($products)): ?>
				<div class="col-md-12">
					<div class="alert alert-info text-center">
						<p><em>No hay productos disponibles en este momento.</em></p>
					</div>
				</div>
				<?php else: ?>
				<?php foreach ($products as $product): ?>
				<div class="col-md-4 col-sm-6">
					<div class="panel panel-default">
						<div class="panel-body text-center">
							<img src="<?php echo Uri::base(); ?>assets/img/placeholder.png" alt="Producto" class="img-responsive" style="max-height: 150px; margin: 0 auto;">
						</div>
						<div class="panel-footer">
							<h5><?php echo htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h5>
							<p class="text-success"><strong>$<?php echo htmlspecialchars($product['price'] ?? '0.00', ENT_QUOTES, 'UTF-8'); ?></strong></p>
							<a href="<?php echo Uri::base(); ?>tienda/producto/<?php echo htmlspecialchars($product['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary btn-sm">Ver</a>
							<a href="<?php echo Uri::base(); ?>tienda/carrito/agregar/<?php echo htmlspecialchars($product['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-success btn-sm">
								<span class="glyphicon glyphicon-shopping-cart"></span>
							</a>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
