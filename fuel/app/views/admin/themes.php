<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Seleccionar Tema - Admin</title>
	
	<!-- GOOGLE FONTS -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">

	<!-- ASSETS -->
	<link rel="stylesheet" href="https://demo.themesberg.com/argon-dashboard/assets/vendor/nucleo/css/nucleo.css" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
	<?php echo Asset::css('admin/argon.css'); ?>
	
	<style>
		.theme-card {
			transition: transform 0.3s, box-shadow 0.3s;
			cursor: pointer;
			height: 100%;
		}
		.theme-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
		}
		.theme-card.active {
			border: 3px solid #5e72e4;
		}
		.theme-preview {
			height: 200px;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			display: flex;
			align-items: center;
			justify-content: center;
			color: white;
			font-size: 3rem;
		}
		.theme-argon { background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%)!important; }
		.theme-adminlte { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%)!important; }
		.theme-coreui { background: linear-gradient(135deg, #321fdb 0%, #1f1fdb 100%)!important; }
	</style>
</head>
<body class="bg-gradient-primary">
	<nav class="navbar navbar-top navbar-expand-md navbar-dark" id="navbar-main">
		<div class="container-fluid">
			<a class="h4 mb-0 text-white text-uppercase d-none d-lg-inline-block" href="<?php echo Uri::base().'admin'; ?>">Seleccionar Tema</a>
			<ul class="navbar-nav align-items-center d-none d-md-flex">
				<li class="nav-item dropdown">
					<a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<div class="media align-items-center">
							<span class="avatar avatar-sm rounded-circle">
								<i class="ni ni-single-02"></i>
							</span>
							<div class="media-body ml-2 d-none d-lg-block">
								<span class="mb-0 text-sm font-weight-bold text-white"><?php echo Auth::get('username'); ?></span>
							</div>
						</div>
					</a>
				</li>
			</ul>
		</div>
	</nav>

	<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
		<div class="container-fluid">
			<div class="header-body">
				<div class="row">
					<div class="col">
						<h1 class="text-white mb-2">Selecciona tu Tema Favorito</h1>
						<p class="text-white text-sm mb-0">Elige entre 3 diseños diferentes para personalizar tu experiencia</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="container-fluid mt--7">
		<div class="row">
			<?php foreach ($themes as $theme): ?>
			<div class="col-xl-4 col-md-6 mb-4">
				<div class="card theme-card <?php echo ($current_theme && $current_theme->id == $theme->id) ? 'active' : ''; ?>" 
					 onclick="selectTheme(<?php echo $theme->id; ?>, '<?php echo $theme->slug; ?>')">
					<div class="theme-preview theme-<?php echo $theme->slug; ?>">
						<?php if ($theme->slug == 'argon'): ?>
							<i class="ni ni-spaceship"></i>
						<?php elseif ($theme->slug == 'adminlte'): ?>
							<i class="fas fa-dashboard"></i>
						<?php else: ?>
							<i class="fas fa-layer-group"></i>
						<?php endif; ?>
					</div>
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-2">
							<h3 class="mb-0"><?php echo $theme->name; ?></h3>
							<?php if ($current_theme && $current_theme->id == $theme->id): ?>
								<span class="badge badge-success">Activo</span>
							<?php endif; ?>
						</div>
						<p class="text-muted mb-3"><?php echo $theme->description; ?></p>
						<button type="button" class="btn btn-primary btn-block" onclick="selectTheme(<?php echo $theme->id; ?>, '<?php echo $theme->slug; ?>')">
							<?php echo ($current_theme && $current_theme->id == $theme->id) ? 'Tema Actual' : 'Seleccionar'; ?>
						</button>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>

		<div class="row">
			<div class="col-12">
				<div class="text-center">
					<a href="<?php echo Uri::base().'admin'; ?>" class="btn btn-secondary">
						<i class="fas fa-arrow-left mr-2"></i> Volver al Dashboard
					</a>
				</div>
			</div>
		</div>
	</div>

	<form id="theme-form" method="post" action="<?php echo Uri::base().'admin/change_theme'; ?>">
		<input type="hidden" name="theme_id" id="theme_id">
	</form>

	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<?php echo Asset::js('admin/argon.js'); ?>

	<script>
		function selectTheme(themeId, themeSlug) {
			if (confirm('¿Cambiar al tema ' + themeSlug.toUpperCase() + '?')) {
				$('#theme_id').val(themeId);
				$('#theme-form').submit();
			}
		}

		<?php if (Session::get_flash('success')): ?>
			alert('<?php echo Session::get_flash('success'); ?>');
		<?php endif; ?>

		<?php if (Session::get_flash('error')): ?>
			alert('<?php echo Session::get_flash('error'); ?>');
		<?php endif; ?>
	</script>
</body>
</html>
