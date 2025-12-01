<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-usd"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<!-- Stats Cards -->
<div class="row">
	<?php foreach ($stats as $key => $stat): ?>
	<div class="col-md-3 col-sm-6">
		<div class="panel panel-info">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="glyphicon glyphicon-<?php echo htmlspecialchars($stat['icon'], ENT_QUOTES, 'UTF-8'); ?> fa-5x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge"><?php echo htmlspecialchars($stat['count'], ENT_QUOTES, 'UTF-8'); ?></div>
						<div><?php echo htmlspecialchars($stat['title'], ENT_QUOTES, 'UTF-8'); ?></div>
					</div>
				</div>
			</div>
			<a href="<?php echo Uri::base() . htmlspecialchars($stat['link'], ENT_QUOTES, 'UTF-8'); ?>">
				<div class="panel-footer">
					<span class="pull-left">Ver Detalles</span>
					<span class="pull-right"><i class="glyphicon glyphicon-chevron-right"></i></span>
					<div class="clearfix"></div>
				</div>
			</a>
		</div>
	</div>
	<?php endforeach; ?>
</div>

<!-- Quick Links -->
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-link"></span> Acceso Rápido</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<?php foreach ($quick_links as $link): ?>
					<div class="col-md-3 col-sm-6">
						<a href="<?php echo Uri::base() . htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-info btn-lg btn-block" style="margin-bottom: 10px;">
							<span class="glyphicon glyphicon-<?php echo htmlspecialchars($link['icon'], ENT_QUOTES, 'UTF-8'); ?>"></span>
							<?php echo htmlspecialchars($link['title'], ENT_QUOTES, 'UTF-8'); ?>
						</a>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</div>
