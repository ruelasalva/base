<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h1><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
			<hr>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-heading"><h3 class="panel-title">Filtros</h3></div>
				<div class="panel-body">
					<h5>Categorías</h5>
					<?php if (empty($categories)): ?>
					<p class="text-muted"><em>No hay categorías disponibles.</em></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
		
		<div class="col-md-9">
			<?php if (empty($products)): ?>
			<div class="alert alert-info text-center">
				<p><em>No hay productos disponibles en este momento.</em></p>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
