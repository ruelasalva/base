<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ol class="breadcrumb">
				<li><a href="<?php echo Uri::base(); ?>tienda">Tienda</a></li>
				<li><a href="<?php echo Uri::base(); ?>tienda/catalogo">Catálogo</a></li>
				<li class="active"><?php echo htmlspecialchars($category ?? 'Categoría', ENT_QUOTES, 'UTF-8'); ?></li>
			</ol>
			<h1><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?>: <?php echo htmlspecialchars($category ?? 'Sin especificar', ENT_QUOTES, 'UTF-8'); ?></h1>
			<hr>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Filtros</h3>
				</div>
				<div class="panel-body">
					<h5>Precio</h5>
					<div class="form-group">
						<input type="range" class="form-control" min="0" max="1000">
						<p class="text-muted">$0 - $1000</p>
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-md-9">
			<?php if (empty($products)): ?>
			<div class="alert alert-info text-center">
				<span class="glyphicon glyphicon-info-sign" style="font-size: 48px;"></span>
				<h3>No hay productos en esta categoría</h3>
				<p>Explora otras categorías o vuelve al catálogo principal.</p>
				<a href="<?php echo Uri::base(); ?>tienda/catalogo" class="btn btn-primary">
					<span class="glyphicon glyphicon-th"></span> Ver Todo el Catálogo
				</a>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
