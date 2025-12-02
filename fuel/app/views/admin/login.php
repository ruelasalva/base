<!DOCTYPE html>
<html lang="es">

<head>
	<!-- META -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<?php
	// Cargar configuración del sitio
	$site_config = Model_SiteConfig::get_config(1); // TODO: tenant_id dinámico
	?>
	<meta name="description" content="<?php echo $site_config ? $site_config->meta_description : 'Panel administrativo'; ?>">
	<meta name="author" content="<?php echo $site_config ? $site_config->meta_author : 'Admin'; ?>">
	<?php if ($site_config && $site_config->theme_color): ?>
		<meta name="theme-color" content="<?php echo $site_config->theme_color; ?>">
	<?php endif; ?>

	<!-- TITLE -->
	<title><?php echo $site_config ? $site_config->site_name : 'Panel administrativo'; ?> - Login</title>
	
	<!-- FAVICONS -->
	<?php if ($site_config && $site_config->favicon_32): ?>
		<link rel="icon" href="<?php echo $site_config->favicon_32; ?>" type="image/png">
	<?php else: ?>
		<link rel="icon" href="<?php echo Uri::base(false).'assets/img/admin/favicon.png'; ?>" type="image/png">
	<?php endif; ?>

	<!-- GOOGLE FONTS -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">

	<!-- ASSETS -->
	<link rel="stylesheet" href="https://demo.themesberg.com/argon-dashboard/assets/vendor/nucleo/css/nucleo.css" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
	<?php echo Asset::css('admin/argon.css'); ?>
	
	<!-- Scripts de Tracking -->
	<?php if ($site_config): ?>
		<?php echo $site_config->get_all_head_scripts(); ?>
	<?php endif; ?>
</head>

<body class="bg-default">

	<!-- MAIN CONTENT -->
	<div class="main-content">

		<!-- HEADER -->
		<div class="header bg-gradient-primary py-5">
			<div class="container">
				<div class="header-body text-center mb-7"></div>
			</div>
		</div>

		<!-- PAGE CONTENT -->
		<div class="container mt--8 pb-5">
			<div class="row justify-content-center">
				<div class="col-lg-5 col-md-7">
					<div class="card bg-secondary border-0 mb-0">
						<div class="card-body px-lg-5 py-lg-5">
						<div class="text-center mb-4">
							<h2 class="text-dark">Panel Admin</h2>
							<small class="text-muted">Ingresa los datos de tu cuenta</small>
						</div>
							<?php echo Form::open(array('method' => 'post')); ?>
								<div class="form-group mb-3">
									<div class="input-group input-group-merge input-group-alternative">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="ni ni-email-83"></i></span>
										</div>
										<?php echo Form::input('username', $data['username'], array('id' => 'username', 'class' => 'form-control', 'placeholder' => 'Email')); ?>
									</div>
								</div>
								<div class="form-group">
									<div class="input-group input-group-merge input-group-alternative">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
										</div>
										<?php echo Form::password('password', '', array('id' => 'password', 'class' => 'form-control', 'placeholder' => 'Contraseña')); ?>
										<div class="input-group-append">
											<span class="input-group-text" style="cursor: pointer;" onclick="togglePassword()">
												<i class="fas fa-eye" id="toggleIcon"></i>
											</span>
										</div>
									</div>
								</div>
								<div class="custom-control custom-control-alternative custom-checkbox">
									<?php echo Form::checkbox('rememberme', 'true', array('id' => 'rememberme', 'class' => 'custom-control-input')); ?>
									<label class="custom-control-label" for="rememberme">
										<span class="text-muted">Recordar sesión</span>
									</label>
								</div>
								
								<!-- reCAPTCHA dinámico -->
								<?php if ($site_config && $site_config->recaptcha_enabled && $site_config->recaptcha_site_key): ?>
									<div class="text-center mb-3">
										<?php if ($site_config->recaptcha_version === 'v2'): ?>
											<div class="g-recaptcha" data-sitekey="<?php echo $site_config->recaptcha_site_key; ?>"></div>
										<?php endif; ?>
									</div>
								<?php endif; ?>
								
								<div class="text-center">
									<?php echo Form::button('submit', 'Iniciar sesión', array('class' => 'btn btn-primary my-4')); ?>
								</div>
							<?php echo Form::close(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- FOOTER -->
	<footer class="py-5">
		<div class="container">
			<div class="row align-items-center justify-content-xl-between">
				<div class="col-xl-6">
					<div class="copyright text-center text-xl-left text-muted">
						<?php echo Html::anchor('/', '<i class="fas fa-long-arrow-alt-left"></i> Regresar al proyecto', array('class' => 'font-weight-bold ml-1', 'Regresar al proyecto')); ?>
					</div>
				</div>
				<div class="col-xl-6">
					<div class="nav justify-content-center justify-content-xl-end copyright text-center text-xl-right text-muted">
						© <?php echo date('Y'); ?> Panel Administrativo. Todos los derechos reservados.
					</div>
				</div>
			</div>
		</div>
	</footer>

	<!-- JAVASCRIPT -->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<?php echo Asset::js('admin/argon.js'); ?>
	
	<!-- reCAPTCHA script solo si está habilitado -->
	<?php if ($site_config && $site_config->recaptcha_enabled && $site_config->recaptcha_site_key): ?>
		<?php echo $site_config->get_recaptcha_script(); ?>
	<?php endif; ?>

	<script type="text/javascript">
		// Toggle password visibility
		function togglePassword() {
			const passwordField = document.getElementById('password');
			const toggleIcon = document.getElementById('toggleIcon');
			
			if (passwordField.type === 'password') {
				passwordField.type = 'text';
				toggleIcon.classList.remove('fa-eye');
				toggleIcon.classList.add('fa-eye-slash');
			} else {
				passwordField.type = 'password';
				toggleIcon.classList.remove('fa-eye-slash');
				toggleIcon.classList.add('fa-eye');
			}
		}
		
		$(document).ready(function() {
			<?php if($data['username'] != ''): ?>
				$('#password').focus();
			<?php else: ?>
				$('#username').focus();
			<?php endif; ?>

			<?php if(Session::get_flash('error')): ?>
				var Notify = (function() {
					function notify(placement, align, icon, type, animIn, animOut) {
						$.notify({
							icon: icon,
							title: ' Aviso importante',
							message: '<?php echo Session::get_flash('error'); ?>',
							url: ''
						}, {
							element: 'body',
							type: type,
							allow_dismiss: true,
							placement: {
								from: placement,
								align: align
							},
							offset: {
								x: 15,
								y: 15
							},
							spacing: 10,
							z_index: 1080,
							delay: 2500,
							timer: 25000,
							url_target: '_blank',
							mouse_over: false,
							animate: {
								enter: animIn,
								exit: animOut
							},
							template: '<div data-notify="container" class="alert alert-dismissible alert-{0} alert-notify" role="alert">' +
							'<span class="alert-icon" data-notify="icon"></span> ' +
							'<div class="alert-text"</div> ' +
							'<span class="alert-title" data-notify="title">{1}</span> ' +
							'<span data-notify="message">{2}</span>' +
							'</div>' +
							'<button type="button" class="close" data-notify="dismiss" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
							'</div>'
						});
					}

					notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut');
				})();
			<?php endif; ?>
		});
	</script>
</body>
</html>
