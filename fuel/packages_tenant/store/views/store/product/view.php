<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ol class="breadcrumb">
				<li><a href="<?php echo Uri::base(); ?>tienda">Tienda</a></li>
				<li><a href="<?php echo Uri::base(); ?>tienda/catalogo">Catálogo</a></li>
				<li class="active"><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></li>
			</ol>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="panel-body text-center">
					<span class="glyphicon glyphicon-picture" style="font-size: 200px; color: #ddd;"></span>
				</div>
			</div>
		</div>
		
		<div class="col-md-6">
			<h1><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
			<hr>
			<?php if ($product === null): ?>
			<div class="alert alert-warning">
				<span class="glyphicon glyphicon-warning-sign"></span>
				Producto no encontrado.
			</div>
			<a href="<?php echo Uri::base(); ?>tienda/catalogo" class="btn btn-primary">
				<span class="glyphicon glyphicon-arrow-left"></span> Volver al Catálogo
			</a>
			<?php else: ?>
			<p class="lead">Descripción del producto no disponible.</p>
			<h2 class="text-primary">$0.00</h2>
			<hr>
			<form method="post" action="<?php echo Uri::base(); ?>tienda/carrito/agregar/<?php echo htmlspecialchars($product_id, ENT_QUOTES, 'UTF-8'); ?>">
				<div class="form-group">
					<label for="cantidad">Cantidad:</label>
					<input type="number" class="form-control" id="cantidad" name="cantidad" value="1" min="1" style="width: 100px;">
				</div>
				<button type="submit" class="btn btn-success btn-lg">
					<span class="glyphicon glyphicon-shopping-cart"></span> Agregar al Carrito
				</button>
			</form>
			<?php endif; ?>
		</div>
	</div>
	
	<div class="row" style="margin-top: 40px;">
		<div class="col-md-12">
			<h3><span class="glyphicon glyphicon-star"></span> Productos Relacionados</h3>
			<hr>
			<?php if (empty($related_products)): ?>
			<div class="alert alert-info text-center">
				<em>No hay productos relacionados disponibles.</em>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
