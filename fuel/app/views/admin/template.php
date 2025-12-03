<!DOCTYPE html>
<html lang="es">

<head>
	<!-- META -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Panel administrativo">
	<meta name="author" content="Admin">

	<!-- TITLE -->
	<title><?php echo $title; ?></title>
	<link rel="icon" href="<?php echo Uri::base(false).'assets/img/admin/favicon.png'; ?>" type="image/png">

	<!-- MANIFIEST -->
	<link rel="manifest" href="<?php echo Uri::base(false).'assets/manifest.json'; ?>">
	<meta name="theme-color" content="#008ad5">

	<!-- GOOGLE FONTS -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">

	<!-- ASSETS -->
	<link rel="stylesheet" href="https://demo.themesberg.com/argon-dashboard/assets/vendor/nucleo/css/nucleo.css" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css" />
	<?php echo Asset::css('admin/argon.css'); ?>
	<?php echo Asset::css('admin/main.css'); ?>
	<?php echo Asset::css('admin/add.css'); ?>

</head>

<body>
	<!-- URL-LOCATION -->
	<span id="url-location" data-url="<?php echo Uri::create('admin/ajax/'); ?>" data-id="<?php echo Auth::get('id'); ?>" data-token="<?php echo md5(Auth::get('login_hash')); ?>"></span>
	<span id="url-current" data-url="<?php echo Uri::current(); ?>"></span>
	<span id="url" data-url="<?php echo Uri::base(false);?>"></span>

	<!-- jQuery desde CDN -->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	
	<!-- Bootstrap JS desde CDN -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

	<div id="general-vue-app"></div>
	<div id="cookies-app"></div>

	<!-- SIDENAV -->
	<nav class="sidenav navbar navbar-vertical fixed-left navbar-expand-xs navbar-light bg-white" id="sidenav-main">
		<div class="scrollbar-inner">
			<!-- BRAND -->
			<div class="sidenav-header d-flex flex-column align-items-center py-3 position-relative" style="min-width:100%;">
				<?php echo Html::anchor(
					'admin',
					Asset::img('admin/logo.png', array(
						'class' => 'navbar-brand-img',
						'style' => 'max-height:56px; height:56px; width:auto; display:block; margin:auto;'
					)),
					array('class' => 'navbar-brand p-0 m-0 text-center', 'style' => 'width:100%; display:flex; justify-content:center;')
				); ?>
				<!-- Botón flecha solo móvil -->
				<button class="btn d-xl-none p-0 border-0 bg-transparent position-absolute"
				type="button"
				data-toggle="collapse"
				data-target="#sidenav-collapse-main"
				aria-controls="sidenav-collapse-main"
				aria-expanded="true"
				aria-label="Ocultar menú"
				style="right:12px; top:12px; z-index:10;">
				<i class="fa fa-chevron-left fa-2x text-dark"></i>
			</button>
			<!-- Botón flecha escritorio (opcional, si quieres que el usuario pueda ocultar el menú también en desktop) -->
			<div class="ml-auto d-none d-xl-block" style="position:absolute; right:16px;">
				<div class="sidenav-toggler" data-action="sidenav-unpin" data-target="#sidenav-main">
					<div class="sidenav-toggler-inner">
						<span class="sidenav-toggler-line"></span>
						<span class="sidenav-toggler-line"></span>
						<span class="sidenav-toggler-line"></span>
					</div>
				</div>
			</div>
		</div>

		<div class="navbar-inner">
			<div class="collapse navbar-collapse show" id="sidenav-collapse-main">
				<ul class="navbar-nav">

					<!-- DASHBOARD -->
					<li class="nav-item">
						<?php echo Html::anchor('admin', '<i class="fa-solid fa-house text-primary"></i><span class="nav-link-text ml-2">Dashboard</span>', array('class' => (Uri::segment(2) == '') ? 'nav-link active': 'nav-link')); ?>
					</li>

					<!-- SALA DE JUNTAS -->
					<?php if (Helper_Permission::can('sala_juntas', 'view')): ?>
						<li class="nav-item">
							<?php echo Html::anchor('admin/sala_juntas', '<i class="fa-solid fa-calendar-days text-warning"></i><span class="nav-link-text ml-2">Sala de Juntas</span>', array('class' => (Uri::segment(2) == 'sala_juntas') ? 'nav-link active': 'nav-link')); ?>
						</li>
					<?php endif; ?>

					<!-- GESTIÓN -->
					<?php if (
						Helper_Permission::can('slides', 'view') ||
						Helper_Permission::can('banners_productos', 'view') ||
						Helper_Permission::can('banners_laterales', 'view') ||
						Helper_Permission::can('blog_categorias', 'view') ||
						Helper_Permission::can('blog_etiquetas', 'view') ||
						Helper_Permission::can('blog_publicacion', 'view') ||
						Helper_Permission::can('editor_diseno', 'view') ||
						Helper_Permission::can('apariencia_footer', 'view') ||
						Helper_Permission::can('apariencia_header', 'view') ||
						Helper_Permission::can('legal_documentos', 'view') ||
						Helper_Permission::can('legal_consents', 'view') ||
						Helper_Permission::can('legal_contratis', 'view')
						): ?>
						<li class="nav-item">
							<a class="nav-link <?php echo (
								in_array(Uri::segment(2), [
									'editordiseno','slides',
									'banners','banners_laterales',
									'blog','blog_categorias','blog_etiquetas','blog_publicacion',
									'legal','consentimientos','apariencia','apariencia_footer'
								])
								) ? 'active' : ''; ?>" href="#navbar-gestion" data-toggle="collapse"
								role="button" aria-expanded="<?php echo (
									in_array(Uri::segment(2), [
										'editordiseno','slides',
										'banners','banners_laterales',
										'blog','blog_categorias','blog_etiquetas','blog_publicacion',
										'legal','consentimientos','apariencia','apariencia_footer'
									])
									) ? 'true' : 'false'; ?>"
									aria-controls="navbar-gestion">
									<i class="fa-solid fa-gears text-dark"></i>
									<span class="nav-link-text ml-2">Gestión</span>
									<i class="fa-solid fa-chevron-down float-right"></i>
								</a>
								<div class="collapse <?php echo (
									in_array(Uri::segment(2), [
										'editordiseno','slides',
										'banners','banners_laterales',
										'blog','blog_categorias','blog_etiquetas','blog_publicacion',
										'legal','consentimientos','apariencia','apariencia_footer'
									])
									) ? 'show' : ''; ?>" id="navbar-gestion">
									<ul class="nav nav-sm flex-column ml-3">

										<!-- Apariencia (submenu) -->
										<?php if (
											Helper_Permission::can('apariencia_footer', 'view') ||
											Helper_Permission::can('apariencia_header', 'view')
											): ?>
											<li class="nav-item">
												<a class="nav-link <?php echo (in_array(Uri::segment(2), ['apariencia'])) ? 'active' : ''; ?>"
													href="#navbar-apariencia" data-toggle="collapse"
													role="button" aria-expanded="<?php echo (in_array(Uri::segment(2), ['apariencia'])) ? 'true' : 'false'; ?>"
													aria-controls="navbar-apariencia">
													<i class="fa-solid fa-palette text-warning"></i> <span>Apariencia</span>
													<i class="fa-solid fa-chevron-down float-right"></i>
												</a>
												<div class="collapse <?php echo (in_array(Uri::segment(2), ['apariencia'])) ? 'show' : ''; ?>" id="navbar-apariencia">
													<ul class="nav nav-sm flex-column ml-3">
														<?php if (Helper_Permission::can('apariencia_header', 'view')): ?>
															<li class="nav-item">
																<?php echo Html::anchor('admin/apariencia/header', '<i class="fa-solid fa-heading text-primary"></i> <span>Header</span>', ['class' => 'nav-link']); ?>
															</li>
														<?php endif; ?>
														<?php if (Helper_Permission::can('apariencia_footer', 'view')): ?>
															<li class="nav-item">
																<?php echo Html::anchor('admin/apariencia/footer', '<i class="fa-solid fa-shoe-prints text-warning"></i> <span>Footer</span>', ['class' => 'nav-link']); ?>
															</li>
														<?php endif; ?>
													</ul>
												</div>
											</li>
										<?php endif; ?>


										<!-- Editor de diseño -->
										<?php if (Helper_Permission::can('editor_diseno', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/editordiseno', '<i class="fa-solid fa-paint-brush text-info"></i> <span>Editor de Diseño</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>

										<!-- Slides -->
										<?php if (Helper_Permission::can('slides', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/slides', '<i class="fa-regular fa-images text-info"></i> <span>Slides</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>

										<!-- Banners -->
										<?php if (
											Helper_Permission::can('banners_productos', 'view') ||
											Helper_Permission::can('banners_laterales', 'view')
											): ?>
											<li class="nav-item">
												<a class="nav-link <?php echo (in_array(Uri::segment(2), ['banners','banners_laterales'])) ? 'active' : ''; ?>"
													href="#navbar-banners" data-toggle="collapse"
													role="button" aria-expanded="<?php echo (in_array(Uri::segment(2), ['banners','banners_laterales'])) ? 'true' : 'false'; ?>"
													aria-controls="navbar-banners">
													<i class="fa-solid fa-image text-success"></i> <span>Banners</span>
													<i class="fa-solid fa-chevron-down float-right"></i>
												</a>
												<div class="collapse <?php echo (in_array(Uri::segment(2), ['banners','banners_laterales'])) ? 'show' : ''; ?>" id="navbar-banners">
													<ul class="nav nav-sm flex-column ml-3">
														<?php if (Helper_Permission::can('banners_productos', 'view')): ?>
															<li class="nav-item">
																<?php echo Html::anchor('admin/banners', '<i class="fa-solid fa-image text-success"></i> <span>Banners productos</span>', ['class' => 'nav-link']); ?>
															</li>
														<?php endif; ?>
														<?php if (Helper_Permission::can('banners_laterales', 'view')): ?>
															<li class="nav-item">
																<?php echo Html::anchor('admin/banners_laterales/', '<i class="fa-solid fa-image text-success"></i> <span>Banners laterales</span>', ['class' => 'nav-link']); ?>
															</li>
														<?php endif; ?>
													</ul>
												</div>
											</li>
										<?php endif; ?>

										<!-- Blog -->
										<?php if (
											Helper_Permission::can('blog_categorias', 'view') ||
											Helper_Permission::can('blog_etiquetas', 'view') ||
											Helper_Permission::can('blog_publicacion', 'view')
											): ?>
											<li class="nav-item">
												<a class="nav-link <?php echo (in_array(Uri::segment(2), ['blog','blog_categorias','blog_etiquetas','blog_publicacion'])) ? 'active' : ''; ?>"
													href="#navbar-blog" data-toggle="collapse"
													role="button" aria-expanded="<?php echo (in_array(Uri::segment(2), ['blog','blog_categorias','blog_etiquetas','blog_publicacion'])) ? 'true' : 'false'; ?>"
													aria-controls="navbar-blog">
													<i class="fa-solid fa-blog text-danger"></i> <span>Blog</span>
													<i class="fa-solid fa-chevron-down float-right"></i>
												</a>
												<div class="collapse <?php echo (in_array(Uri::segment(2), ['blog','blog_categorias','blog_etiquetas','blog_publicacion'])) ? 'show' : ''; ?>" id="navbar-blog">
													<ul class="nav nav-sm flex-column ml-3">
														<?php if (Helper_Permission::can('blog_categorias', 'view')): ?>
															<li class="nav-item">
																<?php echo Html::anchor('admin/blog/categorias', '<i class="fa-solid fa-folder-open text-info"></i> <span>Categorías</span>', ['class' => 'nav-link']); ?>
															</li>
														<?php endif; ?>
														<?php if (Helper_Permission::can('blog_etiquetas', 'view')): ?>
															<li class="nav-item">
																<?php echo Html::anchor('admin/blog/etiquetas', '<i class="fa-solid fa-tags text-success"></i> <span>Etiquetas</span>', ['class' => 'nav-link']); ?>
															</li>
														<?php endif; ?>
														<?php if (Helper_Permission::can('blog_publicacion', 'view')): ?>
															<li class="nav-item">
																<?php echo Html::anchor('admin/blog/publicaciones', '<i class="fa-solid fa-newspaper text-danger"></i> <span>Publicaciones</span>', ['class' => 'nav-link']); ?>
															</li>
														<?php endif; ?>
													</ul>
												</div>
											</li>
										<?php endif; ?>

										<!-- Legal -->
										<?php if (
											Helper_Permission::can('legal_documentos', 'view') ||
											Helper_Permission::can('legal_consents', 'view') ||
											Helper_Permission::can('legal_cookies', 'view') ||
											Helper_Permission::can('legal_contratos', 'view')
											): ?>
											<li class="nav-item">
												<a class="nav-link <?php echo (in_array(Uri::segment(2), ['legal','consentimientos','cookies'])) ? 'active' : ''; ?>"
													href="#navbar-legal" data-toggle="collapse"
													role="button" aria-expanded="<?php echo (in_array(Uri::segment(2), ['legal','consentimientos','cookies'])) ? 'true' : 'false'; ?>"
													aria-controls="navbar-legal">
													<i class="fa-solid fa-scale-balanced text-primary"></i> <span>Legal</span>
													<i class="fa-solid fa-chevron-down float-right"></i>
												</a>
												<div class="collapse <?php echo (in_array(Uri::segment(2), ['legal','consentimientos','cookies'])) ? 'show' : ''; ?>" id="navbar-legal">
													<ul class="nav nav-sm flex-column ml-3">
														<?php if (Helper_Permission::can('legal_documentos', 'view')): ?>
															<li class="nav-item">
																<?php echo Html::anchor('admin/legal/documentos', '<i class="fa-regular fa-file-lines text-info"></i> <span>Documentos</span>', ['class' => 'nav-link']); ?>
															</li>
														<?php endif; ?>
														<?php if (Helper_Permission::can('legal_consents', 'view')): ?>
															<li class="nav-item">
																<?php echo Html::anchor('admin/legal/consentimientos', '<i class="fa-solid fa-user-check text-danger"></i> <span>Consentimientos</span>', ['class' => 'nav-link']); ?>
															</li>
														<?php endif; ?>
														<?php if (Helper_Permission::can('legal_cookies', 'view')): ?>
															<li class="nav-item">
																<?php echo Html::anchor('admin/legal/cookies', '<i class="fa-solid fa-cookie text-success"></i> <span>Cookies</span>', ['class' => 'nav-link']); ?>
															</li>
														<?php endif; ?>
														<?php if (Helper_Permission::can('legal_contratos', 'view')): ?>
															<li class="nav-item">
																<?php echo Html::anchor('admin/legal/contratos', '<i class="fa-solid fa-file text-info"></i> <span>Contratos</span>', ['class' => 'nav-link']); ?>
															</li>
														<?php endif; ?>
													</ul>
												</div>
											</li>
										<?php endif; ?>

									</ul>
								</div>
							</li>
						<?php endif; ?>

						<!-- Plataformas -->
						<?php if (
							Helper_Permission::can('plataformas_ml', 'view') ||
							Helper_Permission::can('plataformas_amazon', 'view') ||
							Helper_Permission::can('plataformas_walmart', 'view') ||
							Helper_Permission::can('plataformas_tiktok', 'view') ||
							Helper_Permission::can('plataformas_shopify', 'view') ||
							Helper_Permission::can('plataformas_temu', 'view') ||
							Helper_Permission::can('plataformas_aliexpress', 'view') ||
							Helper_Permission::can('plataformas_logs', 'view') ||
							Helper_Permission::can('plataformas_errores', 'view')
						): ?>

							<?php
								// Detectar si estamos dentro de /admin/plataforma/*
								$isPlatformSection = (Uri::segment(2) === 'plataforma');
							?>

							<li class="nav-item">
								<a class="nav-link <?php echo $isPlatformSection ? 'active' : ''; ?>"
								href="#navbar-plataformas"
								data-toggle="collapse"
								role="button"
								aria-expanded="<?php echo $isPlatformSection ? 'true' : 'false'; ?>"
								aria-controls="navbar-plataformas">

									<i class="fa-solid fa-store text-warning"></i>
									<span>Plataformas</span>
									<i class="fa-solid fa-chevron-down float-right"></i>
								</a>

								<div class="collapse <?php echo $isPlatformSection ? 'show' : ''; ?>" id="navbar-plataformas">
									<ul class="nav nav-sm flex-column ml-3">

										<?php if (Helper_Permission::can('plataformas_ml', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor(
													'admin/plataforma/ml',
													'<i class="fa-brands fa-mercadolibre text-warning"></i> <span>Mercado Libre</span>',
													['class' => 'nav-link']
												); ?>
											</li>
										<?php endif; ?>

										<?php if (Helper_Permission::can('plataformas_amazon', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor(
													'admin/plataforma/amazon',
													'<i class="fa-brands fa-amazon text-orange"></i> <span>Amazon</span>',
													['class' => 'nav-link']
												); ?>
											</li>
										<?php endif; ?>

										<?php if (Helper_Permission::can('plataformas_walmart', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor(
													'admin/plataforma/walmart',
													'<i class="fa-solid fa-cart-shopping text-primary"></i> <span>Walmart</span>',
													['class' => 'nav-link']
												); ?>
											</li>
										<?php endif; ?>

										<?php if (Helper_Permission::can('plataformas_tiktok', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor(
													'admin/plataforma/tiktok',
													'<i class="fa-brands fa-tiktok text-info"></i> <span>TikTok Shop</span>',
													['class' => 'nav-link']
												); ?>
											</li>
										<?php endif; ?>

										<?php if (Helper_Permission::can('plataformas_shopify', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor(
													'admin/plataforma/shopify',
													'<i class="fa-brands fa-shopify text-success"></i> <span>Shopify</span>',
													['class' => 'nav-link']
												); ?>
											</li>
										<?php endif; ?>

										<?php if (Helper_Permission::can('plataformas_temu', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor(
													'admin/plataforma/temu',
													'<i class="fa-solid fa-bag-shopping text-warning"></i> <span>Temu</span>',
													['class' => 'nav-link']
												); ?>
											</li>
										<?php endif; ?>

										<?php if (Helper_Permission::can('plataformas_aliexpress', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor(
													'admin/plataforma/aliexpress',
													'<i class="fa-brands fa-alipay text-danger"></i> <span>AliExpress</span>',
													['class' => 'nav-link']
												); ?>
											</li>
										<?php endif; ?>

										<!-- Logs -->
										<?php if (Helper_Permission::can('plataformas_logs', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor(
													'admin/plataforma/ml/logs',
													'<i class="fa-solid fa-list text-info"></i> <span>Logs</span>',
													['class' => 'nav-link']
												); ?>
											</li>
										<?php endif; ?>

										<!-- Errores -->
										<?php if (Helper_Permission::can('plataformas_errores', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor(
													'admin/plataforma/ml/errores',
													'<i class="fa-solid fa-circle-exclamation text-danger"></i> <span>Errores</span>',
													['class' => 'nav-link']
												); ?>
											</li>
										<?php endif; ?>

									</ul>
								</div>
							</li>

						<?php endif; ?>




						<!-- CATÁLOGO DE PRODUCTOS -->
						<?php if (
							Helper_Permission::can('catalogo_productos', 'view') ||
							Helper_Permission::can('catalogo_marcas', 'view') ||
							Helper_Permission::can('catalogo_categorias', 'view') ||
							Helper_Permission::can('catalogo_subcategorias', 'view') ||
							Helper_Permission::can('catalogo_montos', 'view')
							): ?>
							<li class="nav-item">
								<a class="nav-link <?php echo (
									in_array(Uri::segment(3), ['productos','marcas','categorias','subcategorias','montos'])
									) ? 'active' : ''; ?>" href="#navbar-productos" data-toggle="collapse"
									role="button" aria-expanded="<?php echo (in_array(Uri::segment(3), ['productos','marcas','categorias','subcategorias','montos'])) ? 'true' : 'false'; ?>"
									aria-controls="navbar-productos">
									<i class="fa-solid fa-boxes-stacked text-info"></i>
									<span class="nav-link-text ml-2">Catálogo de Productos</span>
									<i class="fa-solid fa-chevron-down float-right"></i>
								</a>
								<div class="collapse <?php echo (in_array(Uri::segment(3), ['productos','marcas','categorias','subcategorias','montos'])) ? 'show' : ''; ?>" id="navbar-productos">
									<ul class="nav nav-sm flex-column ml-3">
										<?php if (Helper_Permission::can('catalogo_productos', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/catalogo/productos', '<i class="fa-solid fa-cube text-primary"></i> <span>Productos</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('catalogo_marcas', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/catalogo/marcas', '<i class="fa-solid fa-tags text-success"></i> <span>Marcas</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('catalogo_categorias', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/catalogo/categorias', '<i class="fa-solid fa-layer-group text-primary"></i> <span>Categorías</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('catalogo_subcategorias', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/catalogo/subcategorias', '<i class="fa-solid fa-object-group text-danger"></i> <span>Subcategorías</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('catalogo_montos', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/catalogo/montos', '<i class="fa-solid fa-coins text-warning"></i> <span>Montos</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
									</ul>
								</div>
							</li>
						<?php endif; ?>

						<!-- CATÁLOGOS GENERALES -->
						<?php if (
							Helper_Permission::can('catalogo_monedas', 'view') ||
							Helper_Permission::can('catalogo_tipodecambio', 'view') ||
							Helper_Permission::can('catalogo_bancos', 'view') ||
							Helper_Permission::can('catalogo_cuentas_bancarias', 'view') ||
							Helper_Permission::can('catalogo_impuestos', 'view') ||
							Helper_Permission::can('catalogo_retenciones', 'view') ||
							Helper_Permission::can('catalogo_descuentos', 'view') ||
							Helper_Permission::can('catalogo_unidades', 'view') ||
							Helper_Permission::can('catalogo_condiciones_pago', 'view') ||
							Helper_Permission::can('catalogo_tiposdedocumento', 'view')
							): ?>
							<li class="nav-item">
								<a class="nav-link <?php echo (in_array(Uri::segment(4), ['monedas','bancos','cuentas_bancarias','impuestos','retenciones','descuentos','unidades','condiciones_pago'])) ? 'active' : ''; ?>" href="#navbar-catalogos" data-toggle="collapse"
									role="button" aria-expanded="<?php echo (in_array(Uri::segment(4), ['monedas','bancos','cuentas_bancarias','impuestos','retenciones','descuentos','unidades','condiciones_pago'])) ? 'true' : 'false'; ?>"
									aria-controls="navbar-catalogos">
									<i class="fa-solid fa-folder-open text-info"></i>
									<span class="nav-link-text ml-2">Catálogos Generales</span>
									<i class="fa-solid fa-chevron-down float-right"></i>
								</a>
								<div class="collapse <?php echo (in_array(Uri::segment(4), ['monedas','bancos','cuentas_bancarias','impuestos','retenciones','descuentos','unidades','condiciones_pago'])) ? 'show' : ''; ?>" id="navbar-catalogos">
									<ul class="nav nav-sm flex-column ml-3">
										<?php if (Helper_Permission::can('catalogo_monedas', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/catalogo/generales/monedas', '<i class="fa-solid fa-coins text-warning"></i> <span>Monedas</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('catalogo_tipodecambio', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/catalogo/generales/tipodecambio', '<i class="fa-solid fa-coins text-warning"></i> <span>Tipo de Cambio</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('catalogo_bancos', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/catalogo/generales/bancos', '<i class="fa-solid fa-building-columns text-primary"></i> <span>Bancos</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('catalogo_cuentas_bancarias', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/catalogo/generales/cuentas_bancarias', '<i class="fa-solid fa-money-check-dollar text-success"></i> <span>Cuentas Bancarias</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('catalogo_impuestos', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/catalogo/generales/impuestos', '<i class="fa-solid fa-file-invoice-dollar text-danger"></i> <span>Impuestos</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('catalogo_retenciones', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/formas_retenciones', '<i class="fa-solid fa-file-circle-minus text-warning"></i> <span>Retenciones</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('catalogo_descuentos', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/catalogo/generales/descuentos', '<i class="fa-solid fa-tags text-warning"></i> <span>Descuentos</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('catalogo_unidades', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/catalogo/generales/unidades', '<i class="fa-solid fa-scale-balanced text-success"></i> <span>Unidades de Medida</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('catalogo_condiciones_pago', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/catalogo/generales/condiciones_pago', '<i class="fa-solid fa-file-invoice-dollar text-info"></i> <span>Condiciones de Pago</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('catalogo_tipodedocumento', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/catalogo/generales/tipodedocumento', '<i class="fa-solid fa-file-invoice-dollar text-succes"></i> <span>Tipos de Documento</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
									</ul>
								</div>
							</li>
						<?php endif; ?>


						<!-- VENTAS Y CLIENTES -->
						<?php if (
							Helper_Permission::can('ventas_precotizacion', 'view') ||
							Helper_Permission::can('ventas_cotizaciones', 'view') ||
							Helper_Permission::can('ventas_ventas', 'view') ||
							Helper_Permission::can('ventas_abandonados', 'view') ||
							Helper_Permission::can('ventas_deseados', 'view') ||
							Helper_Permission::can('ventas_cupones', 'view') ||
							Helper_Permission::can('config_clientes_web', 'view') ||
							Helper_Permission::can('config_socios', 'view')
							): ?>
							<li class="nav-item">
								<a class="nav-link <?php echo (
									in_array(Uri::segment(2), ['precotizacion','cotizaciones','ventas','abandonados','deseados','cupones','usuarios','socios'])
									) ? 'active' : ''; ?>" href="#navbar-ventas" data-toggle="collapse"
									role="button" aria-expanded="<?php echo (in_array(Uri::segment(2), ['precotizacion','cotizaciones','ventas','abandonados','deseados','cupones','usuarios','socios'])) ? 'true' : 'false'; ?>"
									aria-controls="navbar-ventas">
									<i class="fa-solid fa-cart-shopping text-danger"></i>
									<span class="nav-link-text ml-2">Ventas y Clientes</span>
									<i class="fa-solid fa-chevron-down float-right"></i>
								</a>
								<div class="collapse <?php echo (in_array(Uri::segment(2), ['precotizacion','cotizaciones','ventas','abandonados','deseados','cupones','usuarios','socios'])) ? 'show' : ''; ?>" id="navbar-ventas">
									<ul class="nav nav-sm flex-column ml-3">
										<?php if (Helper_Permission::can('ventas_precotizacion', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/precotizacion', '<i class="fa-solid fa-file-invoice text-red"></i> <span>Precotización</span>', ['class' => 'nav-link']); ?>
											</li>
											<li class="nav-item">
												<?php echo Html::anchor('admin/cotizaciones', '<i class="fa-solid fa-file-invoice text-green"></i> <span>Cotizaciones</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('ventas_ventas', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/ventas', '<i class="fa-solid fa-sack-dollar text-yellow"></i> <span>Ventas</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('ventas_abandonados', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/abandonados', '<i class="fa-solid fa-cart-shopping text-blue"></i> <span>Carritos</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('ventas_deseados', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/deseados', '<i class="fa-solid fa-heart text-red"></i> <span>Deseados</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('ventas_cupones', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/cupones', '<i class="fa-solid fa-ticket text-green"></i> <span>Cupones</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('config_clientes_web', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/usuarios', '<i class="fa-solid fa-earth-americas text-primary"></i> <span>Clientes Web</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('config_socios', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/socios', '<i class="fa-solid fa-users text-success"></i> <span>Clientes SAP</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
									</ul>
								</div>
							</li>
						<?php endif; ?>

						<!-- COMPRAS Y PROVEEDORES -->
						<?php if (
							Helper_Permission::can('compras_dashboard', 'view') ||
							Helper_Permission::can('compras_ordenes', 'view') ||
							Helper_Permission::can('compras_facturas', 'view') ||
							Helper_Permission::can('compras_por_proveedor', 'view') ||
							Helper_Permission::can('compras_contrarecibos', 'view') ||
							Helper_Permission::can('compras_rep', 'view') ||
							Helper_Permission::can('compras_notasdecredito', 'view') ||
							Helper_Permission::can('config_proveedores', 'view')
							): ?>
							<li class="nav-item">
								<a class="nav-link <?php echo (
									in_array(Uri::segment(2), ['dashboard','compras','ordenes','por proveedor','contrarecibos','notasdecredito','proveedores'])
									) ? 'active' : ''; ?>" href="#navbar-compras" data-toggle="collapse"
									role="button" aria-expanded="<?php echo (in_array(Uri::segment(2), ['dashboard','compras','ordenes','por proveedor','contrarecibos','notasdecredito','proveedores'])) ? 'true' : 'false'; ?>"
									aria-controls="navbar-compras">
									<i class="fa-solid fa-truck text-purple"></i>
									<span class="nav-link-text ml-2">Compras y Proveedores</span>
									<i class="fa-solid fa-chevron-down float-right"></i>
								</a>
								<div class="collapse <?php echo (in_array(Uri::segment(2), ['dashboard','compras','ordenes','por proveedor','contrarecibos','notasdecredito','proveedores'])) ? 'show' : ''; ?>" id="navbar-compras">
									<ul class="nav nav-sm flex-column ml-3">
										<?php if (Helper_Permission::can('compras_dashboard', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/compras', '<i class="fa-solid fa-chart-line text-green"></i> <span>Dashboard</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('compras_ordenes', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/compras/ordenes', '<i class="fa-solid fa-file-invoice text-blue"></i> <span>Órdenes de Compra</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('compras_facturas', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/compras/facturas', '<i class="fa-solid fa-receipt text-green"></i> <span>Facturas de<br> Compras</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('compras_contrarecibos', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/compras/contrarecibos', '<i class="fa-solid fa-file-contract text-black"></i> <span>Contrarecibos</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('compras_rep', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/compras/rep', '<i class="fa-solid fa-file-circle-check text-orange"></i> <span>Recibo Electrónico<br> de Pago</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('compras_notasdecredito', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/compras/notasdecredito', '<i class="fa-solid fa-note-sticky text-teal"></i> <span>Nota de Crédito</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('compras_por_proveedor', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/compras/proveedores', '<i class="fa-solid fa-user-tie text-black"></i> <span>Por Proveedor</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('config_proveedores', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/proveedores', '<i class="fa-solid fa-truck-field text-red"></i> <span>Proveedores</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
									</ul>
								</div>
							</li>
						<?php endif; ?>


						<!-- LOGÍSTICA -->
						<?php if (
							Helper_Permission::can('logistica_orders', 'view') ||
							Helper_Permission::can('logistica_paqueterias', 'view')
							): ?>
							<li class="nav-item">
								<a class="nav-link <?php echo (
									in_array(Uri::segment(3), ['orders','paqueterias'])
									) ? 'active' : ''; ?>" href="#navbar-logistica" data-toggle="collapse"
									role="button" aria-expanded="<?php echo (in_array(Uri::segment(3), ['orders','paqueterias'])) ? 'true' : 'false'; ?>"
									aria-controls="navbar-logistica">
									<i class="fa-solid fa-plane-departure text-purple"></i>
									<span class="nav-link-text ml-2">Logística</span>
									<i class="fa-solid fa-chevron-down float-right"></i>
								</a>
								<div class="collapse <?php echo (in_array(Uri::segment(3), ['orders','paqueterias'])) ? 'show' : ''; ?>" id="navbar-logistica">
									<ul class="nav nav-sm flex-column ml-3">
										<?php if (Helper_Permission::can('logistica_orders', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/catalogo/orders', '<i class="fa-solid fa-clipboard-check text-green"></i> <span>Estatus Pedido</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('logistica_paqueterias', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/catalogo/paqueterias', '<i class="fa-solid fa-truck text-blue"></i> <span>Paqueterías</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
									</ul>
								</div>
							</li>
						<?php endif; ?>





						<!-- BANCOS Y FINANZAS (CATÁLOGO) -->
						<?php if (
							Helper_Permission::can('banco_bbva', 'view') ||
							Helper_Permission::can('banco_logs', 'view') ||
							Helper_Permission::can('banco_datos_transf', 'view') ||
							Helper_Permission::can('config_procesadores', 'view') ||
							Helper_Permission::can('config_listas_precios', 'view')
							): ?>
							<li class="nav-item">
								<a class="nav-link <?php echo (
									in_array(Uri::segment(2), ['bbva','logs','datos_transferencia','procesadores_pago']) || Uri::segment(3) == 'listas_precios'
									) ? 'active' : ''; ?>" href="#navbar-bancos" data-toggle="collapse"
									role="button" aria-expanded="<?php echo ((in_array(Uri::segment(2), ['bbva','logs','datos_transferencia','procesadores_pago']) || Uri::segment(3) == 'listas_precios')) ? 'true' : 'false'; ?>"
									aria-controls="navbar-bancos">
									<i class="fa-solid fa-dollar-sign text-success"></i>
									<span class="nav-link-text ml-2">Bancos y Finanzas<br> (Catalogo)</span>
									<i class="fa-solid fa-chevron-down float-right"></i>
								</a>
								<div class="collapse <?php echo ((in_array(Uri::segment(2), ['bbva','logs','datos_transferencia','procesadores_pago']) || Uri::segment(3) == 'listas_precios')) ? 'show' : ''; ?>" id="navbar-bancos">
									<ul class="nav nav-sm flex-column ml-3">
										<?php if (Helper_Permission::can('banco_bbva', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/bbva', '<i class="fa-brands fa-cc-visa text-blue"></i> <span>BBVA</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('banco_logs', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/logs', '<i class="fa-solid fa-align-left text-orange"></i> <span>Logs</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('banco_datos_transf', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/datos_transferencia', '<i class="fa-solid fa-money-bill-transfer text-green"></i> <span>Datos Transferencia</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('config_procesadores', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/procesadores_pago', '<i class="fa-solid fa-credit-card text-primary"></i> <span>Procesadores de Pago</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
										<?php if (Helper_Permission::can('config_listas_precios', 'view')): ?>
											<li class="nav-item">
												<?php echo Html::anchor('admin/catalogo/listas_precios', '<i class="fa-solid fa-money-check-dollar text-danger"></i> <span>Listas de Precios</span>', ['class' => 'nav-link']); ?>
											</li>
										<?php endif; ?>
									</ul>
								</div>
							</li>
						<?php endif; ?>


						<!-- BANCOS Y FINANZAS (OPERACIÓN) -->
						<?php if (
							Helper_Permission::can('mov_bancarios', 'view') ||
							Helper_Permission::can('plan_cuentas', 'view') ||
							Helper_Permission::can('conciliacion', 'view') ||
							Helper_Permission::can('cuentas_pagar', 'view') ||
							Helper_Permission::can('cuentas_cobrar', 'view') ||
							Helper_Permission::can('reportes_financieros', 'view') ||
							Helper_Permission::can('cajas_fondos', 'view')
							): ?>
							<li class="nav-item">
								<a class="nav-link <?php echo (
									in_array(Uri::segment(2), ['movimientos','conciliacion','cuentas_pagar','cuentas_cobrar','reportes_financieros','cajas_fondos','finanzas'])
									|| in_array(Uri::segment(3), ['plancuentas'])
									) ? 'active' : ''; ?>"
									href="#navbar-finanzas" data-toggle="collapse"
									role="button" aria-expanded="<?php echo (
										in_array(Uri::segment(2), ['movimientos','conciliacion','cuentas_pagar','cuentas_cobrar','reportes_financieros','cajas_fondos','finanzas'])
										|| in_array(Uri::segment(3), ['plancuentas'])
										) ? 'true' : 'false'; ?>"
										aria-controls="navbar-finanzas">
										<i class="fa-solid fa-hand-holding-usd text-success"></i>
										<span class="nav-link-text ml-2">Bancos y Finanzas<br> (Operación)</span>
										<i class="fa-solid fa-chevron-down float-right"></i>
									</a>
									<div class="collapse <?php echo (
										in_array(Uri::segment(2), ['movimientos','conciliacion','cuentas_pagar','cuentas_cobrar','reportes_financieros','cajas_fondos','finanzas'])
										|| in_array(Uri::segment(3), ['plancuentas'])
										) ? 'show' : ''; ?>" id="navbar-finanzas">

										<ul class="nav nav-sm flex-column ml-3">
											<?php if (Helper_Permission::can('mov_bancarios', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/movimientos', '<i class="fa-solid fa-money-check-dollar text-primary"></i> <span>Movimientos Bancarios</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('plan_cuentas', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/finanzas/plancuentas', '<i class="fa-solid fa-list-ol text-primary"></i> <span>Plan de Cuentas</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('conciliacion', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/conciliacion', '<i class="fa-solid fa-handshake text-success"></i> <span>Conciliaciones</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('cuentas_pagar', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/cuentas_pagar', '<i class="fa-solid fa-arrow-down text-danger"></i> <span>Cuentas por Pagar</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('cuentas_cobrar', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/cuentas_cobrar', '<i class="fa-solid fa-arrow-up text-success"></i> <span>Cuentas por Cobrar</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('reportes_financieros', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/reportes_financieros', '<i class="fa-solid fa-chart-line text-info"></i> <span>Reportes Financieros</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('cajas_fondos', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/cajas_fondos', '<i class="fa-solid fa-cash-register text-warning"></i> <span>Cajas y Fondos</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
										</ul>
									</div>
								</li>
							<?php endif; ?>

							<!-- DATOS FISCALES (SAT) -->
							<?php if (
								Helper_Permission::can('sat_formas_pago', 'view') ||
								Helper_Permission::can('sat_usos_cfdi', 'view') ||
								Helper_Permission::can('sat_regimen_fiscal', 'view') ||
								Helper_Permission::can('sat_retenciones', 'view') || 
								Helper_Permission::can('sat_unidades', 'view') 
								): ?>
								<li class="nav-item">
									<a class="nav-link <?php echo (in_array(Uri::segment(2), ['formas_pago','formas_usoscfdi','formas_regimenfiscal','retenciones'])) ? 'active' : ''; ?>" href="#navbar-fiscal" data-toggle="collapse"
										role="button" aria-expanded="<?php echo (in_array(Uri::segment(2), ['formas_pago','formas_usoscfdi','formas_regimenfiscal','retenciones'])) ? 'true' : 'false'; ?>"
										aria-controls="navbar-fiscal">
										<i class="fa-solid fa-file-invoice-dollar text-info"></i>
										<span class="nav-link-text ml-2">Datos Fiscales</span>
										<i class="fa-solid fa-chevron-down float-right"></i>
									</a>
									<div class="collapse <?php echo (in_array(Uri::segment(2), ['formas_pago','formas_usoscfdi','formas_regimenfiscal','retenciones'])) ? 'show' : ''; ?>" id="navbar-fiscal">
										<ul class="nav nav-sm flex-column ml-3">
											<?php if (Helper_Permission::can('sat_formas_pago', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/formas_pago', '<i class="fa-regular fa-credit-card text-blue"></i> <span>Formas de Pago</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('sat_usos_cfdi', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/formas_usoscfdi', '<i class="fa-regular fa-file-invoice text-orange"></i> <span>Usos CFDI</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('sat_regimen_fiscal', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/formas_regimenfiscal', '<i class="fa-solid fa-book text-green"></i> <span>Régimen Fiscal</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('sat_retenciones', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/formas_retenciones', '<i class="fa-solid fa-hand-holding-dollar text-warning"></i> <span>Retenciones</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('sat_unidades', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/formas_unidades', '<i class="fa-solid fa-building-un text-warning"></i> <span>Unidades de Medida</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
										</ul>
									</div>
								</li>
							<?php endif; ?>

							<?php if (
								Helper_Permission::can('config_empleados', 'view') ||
								Helper_Permission::can('config_departamento', 'view') ||
								Helper_Permission::can('rrhh_asistencia', 'view') ||
								Helper_Permission::can('rrhh_nominas', 'view') ||
								Helper_Permission::can('rrhh_reportes', 'view')
								): ?>
								<li class="nav-item">
									<a class="nav-link <?php echo (in_array(Uri::segment(2), ['empleados','departamento','asistencia','nominas','reportes'])) ? 'active' : ''; ?>" href="#navbar-rrhh" data-toggle="collapse"
										role="button" aria-expanded="<?php echo (in_array(Uri::segment(2), ['empleados','departamento','asistencia','nominas','reportes'])) ? 'true' : 'false'; ?>"
										aria-controls="navbar-rrhh">
										<i class="fa-solid fa-users text-primary"></i>
										<span class="nav-link-text ml-2">Recursos Humanos</span>
										<i class="fa-solid fa-chevron-down float-right"></i>
									</a>
									<div class="collapse <?php echo (in_array(Uri::segment(2), ['empleados','departamento','asistencia','nominas','reportes'])) ? 'show' : ''; ?>" id="navbar-rrhh">
										<ul class="nav nav-sm flex-column ml-3">
											<?php if (Helper_Permission::can('config_empleados', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/empleados', '<i class="fa-solid fa-user-tie text-green"></i> <span>Empleados</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('config_departamento', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/empleados/departamento', '<i class="fa-solid fa-people-group text-purple"></i> <span>Departamentos</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('rrhh_asistencia', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/rrhh/asistencia', '<i class="fa-solid fa-clock text-orange"></i> <span>Asistencia</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('rrhh_nominas', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/rrhh/nominas', '<i class="fa-solid fa-file-invoice-dollar text-green"></i> <span>Nóminas</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('rrhh_reportes', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/rrhh/reportes', '<i class="fa-solid fa-chart-bar text-blue"></i> <span>Reportes</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
										</ul>
									</div>
								</li>
							<?php endif; ?>


							<!-- CONFIGURACIÓN -->
							<?php if (
								Helper_Permission::can('config_general', 'view') ||
								Helper_Permission::can('config_usuarios', 'view') ||
								Helper_Permission::can('config_notificaciones', 'view') ||
								Helper_Permission::can('config_permisos_grupo', 'view') ||
								Helper_Permission::can('config_permisos_usuario', 'view') ||
								Helper_Permission::can('config_empleados', 'view') ||
								Helper_Permission::can('config_departamento', 'view') ||
								Helper_Permission::can('config_correos', 'view')
								): ?>
								<li class="nav-item">
									<a class="nav-link <?php echo (in_array(Uri::segment(2), ['configuracion','administradores','notificaciones','empleados','departamento','correos'])) ? 'active' : ''; ?>"
										href="#navbar-configuracion" data-toggle="collapse"
										role="button" aria-expanded="<?php echo (in_array(Uri::segment(2), ['configuracion','administradores','notificaciones','empleados','departamento','correos'])) ? 'true' : 'false'; ?>"
										aria-controls="navbar-configuracion">
										<i class="fa-solid fa-gear text-dark"></i>
										<span class="nav-link-text ml-2">Configuración</span>
										<i class="fa-solid fa-chevron-down float-right"></i>
									</a>
									<div class="collapse <?php echo (in_array(Uri::segment(2), ['configuracion','administradores','notificaciones','empleados','departamento','correos'])) ? 'show' : ''; ?>"
										id="navbar-configuracion">
										<ul class="nav nav-sm flex-column ml-3">
											<li class="nav-item"><span class="text-xs text-muted ml-2">Sistema</span></li>
											
											<!-- CONFIGURACIÓN DEL SITIO -->
											<li class="nav-item">
												<?php echo Html::anchor('admin/configuracion', '<i class="fa-solid fa-globe text-primary"></i> <span>Configuración Sitio</span>', ['class' => 'nav-link']); ?>
											</li>
											
											<!-- SELECTOR DE TEMAS -->
											<li class="nav-item">
												<?php echo Html::anchor('admin/themes', '<i class="fa-solid fa-palette text-purple"></i> <span>Cambiar Tema</span>', ['class' => 'nav-link']); ?>
											</li>
											<?php if (Helper_Permission::can('config_general', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/configuracion/general', '<i class="fa-solid fa-cogs text-black"></i> <span>General</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('config_usuarios', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/administradores', '<i class="fa-solid fa-user-gear text-orange"></i> <span>Usuarios Acceso</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('config_notificaciones', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/notificaciones', '<i class="fa-solid fa-bell text-blue"></i> <span>Notificaciones</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('config_permisos_grupo', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/configuracion/permisos/grupo', '<i class="fa-solid fa-user-lock text-black"></i> <span>Permisos por Grupo</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('config_permisos_usuario', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/configuracion/permisos/usuario', '<i class="fa-solid fa-user-shield text-black"></i> <span>Permisos por Usuario</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('config_correos', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/configuracion/correos', '<i class="fa-solid fa-envelope text-red"></i> <span>Correos</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>

											<li class="nav-item"><span class="text-xs text-muted ml-2">Recursos Humanos</span></li>
											<?php if (Helper_Permission::can('config_empleados', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/empleados', '<i class="fa-solid fa-user-tie text-green"></i> <span>Empleados</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('config_departamento', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/empleados/departamento', '<i class="fa-solid fa-building text-purple"></i> <span>Departamentos</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
										</ul>
									</div>
								</li>
							<?php endif; ?>




							<!-- CRM -->
							<?php if (
								Helper_Permission::can('crm_encuestas', 'view') ||
								Helper_Permission::can('crm_reportes_diarios', 'view') ||
								Helper_Permission::can('crm_tareas', 'view') ||
								Helper_Permission::can('crm_tickets', 'view') ||
								Helper_Permission::can('crm_tickets_socios', 'view') ||
								Helper_Permission::can('crm_rastreo_local', 'view') ||
								Helper_Permission::can('crm_corte', 'view')
								): ?>
								<li class="nav-item">
									<a class="nav-link <?php echo (
										Uri::segment(2) == 'crm'
										) ? 'active' : ''; ?>" href="#navbar-crm" data-toggle="collapse"
										role="button" aria-expanded="<?php echo (Uri::segment(2) == 'crm') ? 'true' : 'false'; ?>"
										aria-controls="navbar-crm">
										<i class="fa-solid fa-briefcase text-warning"></i>
										<span class="nav-link-text ml-2">CRM</span>
										<i class="fa-solid fa-chevron-down float-right"></i>
									</a>
									<div class="collapse <?php echo (Uri::segment(2) == 'crm') ? 'show' : ''; ?>" id="navbar-crm">
										<ul class="nav nav-sm flex-column ml-3">
											<?php if (Helper_Permission::can('crm_encuestas', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/crm/survey/results', '<i class="fa-solid fa-chart-pie text-blue"></i> <span>Encuestas</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('crm_reportes_diarios', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/crm/activity/index', '<i class="fa-solid fa-calendar-day text-orange"></i> <span>Reportes Diarios</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('crm_tareas', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/crm/task/index', '<i class="fa-solid fa-tasks text-orange"></i> <span>Tareas</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('crm_tickets', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/crm/ticket/index', '<i class="fa-solid fa-ticket text-green"></i> <span>Tickets</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('crm_tickets_socios', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/crm/socios/ticket/index', '<i class="fa-solid fa-handshake-angle text-success"></i> <span>Tickets Socios</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('crm_rastreo_local', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/crm/rastreo/index', '<i class="fa-solid fa-map-location-dot text-purple"></i> <span>Rastreo Local</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('crm_corte', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/crm/corte/index', '<i class="fa-solid fa-calculator text-red"></i> <span>Calculadora de Corte</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
										</ul>
									</div>
								</li>
							<?php endif; ?>

							<!-- HELPDESK -->
							<?php if (
								Helper_Permission::can('helpdesk_reportes_crm', 'view') ||
								Helper_Permission::can('helpdesk_tareas', 'view') ||
								Helper_Permission::can('helpdesk_tickets', 'view') ||
								Helper_Permission::can('helpdesk_mis_asignados', 'view') ||
								Helper_Permission::can('helpdesk_incidencias', 'view') ||
								Helper_Permission::can('helpdesk_tipos_ticket', 'view')
								): ?>
								<li class="nav-item">
									<a class="nav-link <?php echo (
										Uri::segment(2) == 'helpdesk'
										) ? 'active' : ''; ?>" href="#navbar-helpdesk" data-toggle="collapse"
										role="button" aria-expanded="<?php echo (Uri::segment(2) == 'helpdesk') ? 'true' : 'false'; ?>"
										aria-controls="navbar-helpdesk">
										<i class="fa-solid fa-headset text-black"></i>
										<span class="nav-link-text ml-2">Helpdesk</span>
										<i class="fa-solid fa-chevron-down float-right"></i>
									</a>
									<div class="collapse <?php echo (Uri::segment(2) == 'helpdesk') ? 'show' : ''; ?>" id="navbar-helpdesk">
										<ul class="nav nav-sm flex-column ml-3">
											<?php if (Helper_Permission::can('helpdesk_reportes_crm', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/helpdesk/activity/index', '<i class="fa-solid fa-chart-line text-orange"></i> <span>Reportes CRM</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('helpdesk_tareas', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/helpdesk/task/index', '<i class="fa-solid fa-list-check text-warning"></i> <span>Tareas Pendientes</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('helpdesk_tickets', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/helpdesk/ticket/index', '<i class="fa-solid fa-ticket text-blue"></i> <span>Tickets</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('helpdesk_mis_asignados', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/helpdesk/ticket/asignados', '<i class="fa-solid fa-user-check text-primary"></i> <span>Mis Asignados</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('helpdesk_incidencias', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/helpdesk/incident/index', '<i class="fa-solid fa-triangle-exclamation text-purple"></i> <span>Tipos de Incidencia</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
											<?php if (Helper_Permission::can('helpdesk_tipos_ticket', 'view')): ?>
												<li class="nav-item">
													<?php echo Html::anchor('admin/helpdesk/type/index', '<i class="fa-solid fa-list-check text-danger"></i> <span>Tipos de Ticket</span>', ['class' => 'nav-link']); ?>
												</li>
											<?php endif; ?>
										</ul>
									</div>
								</li>
							<?php endif; ?>

						<!-- REPORTES GENERALES -->
						<?php if (
							Helper_Permission::can('reportes_generales', 'view') ||
							Helper_Permission::can('reportes_generales_departamento', 'view') ||
							Helper_Permission::can('reportes_generales_financieros', 'view') ||
							Helper_Permission::can('reportes_generales_operativos', 'view')
						): ?>
						<li class="nav-item">
						<a class="nav-link <?php echo (Uri::segment(2) == 'reportes_generales') ? 'active' : ''; ?>"
							href="#navbar-reportes-generales"
							data-toggle="collapse"
							role="button"
							aria-expanded="<?php echo (Uri::segment(2) == 'reportes_generales') ? 'true' : 'false'; ?>"
							aria-controls="navbar-reportes-generales">
							<i class="fa-solid fa-chart-column text-primary"></i>
							<span class="nav-link-text ml-2">Reportes Generales</span>
							<i class="fa-solid fa-chevron-down float-right"></i>
						</a>

						<div class="collapse <?php echo (Uri::segment(2) == 'reportes_generales') ? 'show' : ''; ?>"
							id="navbar-reportes-generales">
							<ul class="nav nav-sm flex-column ml-3">

							<?php if (Helper_Permission::can('reportes_generales', 'view')): ?>
								<li class="nav-item">
								<?php echo Html::anchor(
									'admin/reportes',
									'<i class="fa-solid fa-table text-info"></i> <span>Módulo de Reportes</span>',
									['class' => 'nav-link']
								); ?>
								</li>
							<?php endif; ?>

							<?php if (Helper_Permission::can('reportes_generales_departamento', 'view')): ?>
								<li class="nav-item">
								<?php echo Html::anchor(
									'admin/reportes_generales/departamento',
									'<i class="fa-solid fa-users text-success"></i> <span>Por Departamento</span>',
									['class' => 'nav-link']
								); ?>
								</li>
							<?php endif; ?>

							<?php if (Helper_Permission::can('reportes_generales_financieros', 'view')): ?>
								<li class="nav-item">
								<?php echo Html::anchor(
									'admin/reportes_generales/financieros',
									'<i class="fa-solid fa-file-invoice-dollar text-warning"></i> <span>Financieros</span>',
									['class' => 'nav-link']
								); ?>
								</li>
							<?php endif; ?>

							<?php if (Helper_Permission::can('reportes_generales_operativos', 'view')): ?>
								<li class="nav-item">
								<?php echo Html::anchor(
									'admin/reportes_generales/operativos',
									'<i class="fa-solid fa-gears text-purple"></i> <span>Operativos</span>',
									['class' => 'nav-link']
								); ?>
								</li>
							<?php endif; ?>

							</ul>
						</div>
						</li>
						<?php endif; ?>

					<!-- SEGURIDAD Y RBAC -->
					<?php if (
						Helper_Permission::can('users', 'view') ||
						Helper_Permission::can('roles', 'view') ||
						Helper_Permission::can('permissions', 'view') ||
						Helper_Permission::can('logs', 'view') ||
						Helper_Permission::can('notifications', 'view')
					): ?>
					<li class="nav-item">
						<a class="nav-link <?php echo (in_array(Uri::segment(2), ['users', 'roles', 'permissions', 'logs', 'notifications'])) ? 'active' : ''; ?>"
							href="#navbar-security"
							data-toggle="collapse"
							role="button"
							aria-expanded="<?php echo (in_array(Uri::segment(2), ['users', 'roles', 'permissions', 'logs', 'notifications'])) ? 'true' : 'false'; ?>"
							aria-controls="navbar-security">
							<i class="fa-solid fa-shield-halved text-danger"></i>
							<span class="nav-link-text ml-2">Seguridad</span>
							<i class="fa-solid fa-chevron-down float-right"></i>
						</a>

						<div class="collapse <?php echo (in_array(Uri::segment(2), ['users', 'roles', 'permissions', 'logs', 'notifications'])) ? 'show' : ''; ?>"
							id="navbar-security">
							<ul class="nav nav-sm flex-column ml-3">

								<?php if (Helper_Permission::can('users', 'view')): ?>
									<li class="nav-item">
										<?php echo Html::anchor(
											'admin/users',
											'<i class="fa-solid fa-users text-primary"></i> <span>Usuarios</span>',
											['class' => 'nav-link']
										); ?>
									</li>
								<?php endif; ?>

								<?php if (Helper_Permission::can('roles', 'view')): ?>
									<li class="nav-item">
										<?php echo Html::anchor(
											'admin/roles',
											'<i class="fa-solid fa-user-tag text-success"></i> <span>Roles</span>',
											['class' => 'nav-link']
										); ?>
									</li>
								<?php endif; ?>

								<?php if (Helper_Permission::can('permissions', 'view')): ?>
									<li class="nav-item">
										<?php echo Html::anchor(
											'admin/permissions',
											'<i class="fa-solid fa-key text-warning"></i> <span>Permisos</span>',
											['class' => 'nav-link']
										); ?>
									</li>
								<?php endif; ?>

								<?php if (Helper_Permission::can('logs', 'view')): ?>
									<li class="nav-item">
										<?php echo Html::anchor(
											'admin/logs',
											'<i class="fa-solid fa-list-check text-info"></i> <span>Auditoría</span>',
											['class' => 'nav-link']
										); ?>
									</li>
								<?php endif; ?>

								<?php if (Helper_Permission::can('notifications', 'view')): ?>
									<li class="nav-item">
										<?php echo Html::anchor(
											'admin/notifications',
											'<i class="fa-solid fa-bell text-purple"></i> <span>Notificaciones</span>',
											['class' => 'nav-link']
										); ?>
									</li>
								<?php endif; ?>

							</ul>
						</div>
					</li>
					<?php endif; ?>

						</ul>
					</div>
				</div>
			</div>
		</nav>


		<!-- MAIN CONTENT -->
		<div class="main-content" id="panel">
			<!-- TOPNAV -->
			<nav class="navbar navbar-top navbar-expand navbar-dark bg-primary border-bottom">
				<div class="container-fluid">
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
						<!-- IZQUIERDA: Título -->
						<ul class="navbar-nav align-items-center">
							<li class="nav-item">
								<h1 class="h2 text-white d-inline-block mb-0">Panel Admin <span class="h6 text-white">v2.0</span></h1>
							</li>
						</ul>

						<?php if(Uri::segment(2) == 'precotizacion' || Uri::segment(2) == 'solicitud_precotizacion'): ?>
							<!-- CENTRO: Buscador -->
							<div class="flex-grow-1 px-3">
								<?php echo Form::open(array(
									'action'     => 'admin/precotizacion/busqueda',
									'method'     => 'post',
									'class'      => '',
									'id'         => 'form_search',
									'novalidate' => true
								)); ?>
								<div class="input-group input-group-sm w-100">
									<?php echo Form::input('search', '', array(
										'placeholder' => 'Buscar producto...',
										'class'       => 'form-control'
									)); ?>
									<div class="input-group-append">
										<button class="btn btn-secondary" type="submit" title="Buscar">
											<i class="fa fa-search"></i>
										</button>
									</div>
								</div>
								<?php echo Form::close(); ?>
							</div>

							<!-- DERECHA: Cotización -->
							<ul class="navbar-nav align-items-center ml-auto">

								<!-- Botón Cotización -->
								<li class="nav-item mr-3">
									<a href="<?php echo Uri::create('admin/solicitud_precotizacion'); ?>" class="btn btn-quote">
										<i class="fas fa-file-invoice-dollar fa-lg mr-1 text-white"></i>
										<span class="d-none d-sm-inline font-weight-bold text-white">Solicitud de Precotización</span>
										<span class="badge badge-light quote-qty ml-2"><?php echo $total_products_quantity; ?></span>
									</a>

								</li>

							</ul>
						<?php endif; ?>
						<ul class="navbar-nav align-items-center ml-auto ml-md-0 order-3 order-md-3">
							<div id="notifications-app"></div>
							<li class="nav-item dropdown">
								<a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<div class="media align-items-center">
										<span class="avatar avatar-sm rounded-circle">
											<img src="https://ui-avatars.com/api/?background=172b4d&color=fff&length=1&name=<?php echo Auth::get('email'); ?>">
										</span>
										<div class="media-body ml-2 d-none d-lg-block">
											<span class="mb-0 text-sm  font-weight-bold"><?php echo Auth::get('email'); ?></span>
										</div>
									</div>
								</a>
								<div class="dropdown-menu dropdown-menu-right">
									<div class="dropdown-header noti-title">
										<h6 class="text-overflow m-0">Opciones de usuario</h6>
									</div>
									<?php echo Html::anchor('admin/perfil', '<i class="fa-solid fa-user"></i><span>Editar perfil</span>', array('class' => 'dropdown-item')) ?>
									<div class="dropdown-divider"></div>
									<?php echo Html::anchor('admin/logout', '<i class="fa-solid fa-right-from-bracket"></i><span>Cerrar sesión</span>', array('class' => 'dropdown-item')) ?>
								</div>
							</li>
						</ul>
						<ul class="navbar-nav align-items-center ml-md-auto order-1 order-md-2">
							<li class="nav-item d-xl-none">
								<!-- SIDENAV TOGGLER -->
								<div class="pr-3 sidenav-toggler sidenav-toggler-dark" data-action="sidenav-pin" data-target="#sidenav-main">
									<div class="sidenav-toggler-inner">
										<span class="sidenav-toggler-line"></span>
										<span class="sidenav-toggler-line"></span>
										<span class="sidenav-toggler-line"></span>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</nav>


			<!-- CONTENT -->
			<?php echo $content; ?>

			<!-- FOOTER -->
			<div class="container-fluid">
				<footer class="footer pt-0">
					<div class="row align-items-center justify-content-lg-between">
						<div class="col-lg-6 d-none d-sm-block">
							<div class="copyright text-center text-lg-left text-muted">
								Página renderizada en <strong>{exec_time}</strong> seg, usando <strong>{mem_usage}</strong> MB de memoria.
							</div>
						</div>
						<div class="col-lg-6">
							<div class="copyright text-center text-xl-right text-muted">
								© <?php echo date('Y'); ?> Panel Administrativo. Todos los derechos reservados.
							</div>
						</div>
					</div>
				</footer>
			</div>
		</div>

		<!-- JAVASCRIPT -->

		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.5/js.cookie.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
		
		<!-- Scripts admin locales (solo si existen) -->
		<script>
		// Placeholder para scripts personalizados
		console.log('Admin template loaded');
		</script>
		
		<?php if(Session::get_flash('error')): ?>
			<script type="text/javascript">
			$(document).ready(function() {
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
							'<div class="alert-text"></div> ' +
							'<span class="alert-title" data-notify="title">{1}</span> ' +
							'<span data-notify="message">{2}</span>' +
							'</div>' +
							'<button type="button" class="close" data-notify="dismiss" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
							'</div>'
						});
					}

					notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut');
				})();
			});
			</script>
		<?php endif; ?>

		<?php if(Session::get_flash('success')): ?>
			<script type="text/javascript">
			$(document).ready(function() {
				var Notify = (function() {

					function notify(placement, align, icon, type, animIn, animOut) {
						$.notify({
							icon: icon,
							title: ' Aviso importante',
							message: '<?php echo Session::get_flash('success'); ?>',
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
							'<div class="alert-text"></div> ' +
							'<span class="alert-title" data-notify="title">{1}</span> ' +
							'<span data-notify="message">{2}</span>' +
							'</div>' +
							'<button type="button" class="close" data-notify="dismiss" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
							'</div>'
						});
					}

					notify('top', 'center', 'ni ni-check-bold', 'success', 'animated bounceIn', 'animated bounceOut');
				})();
			});
			</script>
		<?php endif; ?>
		<?php if(Session::get_flash('warning')): ?>
			<script type="text/javascript">
			$(document).ready(function() {
				var Notify = (function() {
					function notify(placement, align, icon, type, animIn, animOut) {
						$.notify({
							icon: icon,
							title: ' Aviso importante',
							message: '<?php echo Session::get_flash('warning'); ?>',
							url: ''
						}, {
							element: 'body',
							type: type,
							allow_dismiss: true,
							placement: {
								from: placement,
								align: align
							},
							offset: { x: 15, y: 15 },
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
							template:
								'<div data-notify="container" class="alert alert-dismissible alert-{0} alert-notify" role="alert">' +
								'<span class="alert-icon" data-notify="icon"></span> ' +
								'<div class="alert-text"></div> ' +
								'<span class="alert-title" data-notify="title">{1}</span> ' +
								'<span data-notify="message">{2}</span>' +
								'</div>' +
								'<button type="button" class="close" data-notify="dismiss" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
								'</div>'
						});
					}
					notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut');
				})();
			});
			</script>
			<?php endif; ?>

			<?php if(Session::get_flash('info')): ?>
			<script type="text/javascript">
			$(document).ready(function() {
				var Notify = (function() {
					function notify(placement, align, icon, type, animIn, animOut) {
						$.notify({
							icon: icon,
							title: ' Información',
							message: '<?php echo Session::get_flash('info'); ?>',
							url: ''
						}, {
							element: 'body',
							type: type,
							allow_dismiss: true,
							placement: {
								from: placement,
								align: align
							},
							offset: { x: 15, y: 15 },
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
							template:
								'<div data-notify="container" class="alert alert-dismissible alert-{0} alert-notify" role="alert">' +
								'<span class="alert-icon" data-notify="icon"></span> ' +
								'<div class="alert-text"></div> ' +
								'<span class="alert-title" data-notify="title">{1}</span> ' +
								'<span data-notify="message">{2}</span>' +
								'</div>' +
								'<button type="button" class="close" data-notify="dismiss" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
								'</div>'
						});
					}
					notify('top', 'center', 'ni ni-chat-round', 'info', 'animated bounceIn', 'animated bounceOut');
				})();
			});
			</script>
			<?php endif; ?>


		<?php if(Uri::segment(4) == 'graficas'): ?>
			<script type="text/javascript">
			$(document).ready(function() {
				var BarsChart = (function() {

					var $chart = $('#tick-user');

					function initChart($chart) {

						var colors = ['red', 'blue', 'green', 'yellow', 'orange']; // Agrega más colores si es necesario

						var ordersChart = new Chart($chart, {
							type: 'pie',
							data: {
								labels: $chart_labels,
								datasets: [{
									label: $chart_label,
									data: $chart_data,
									backgroundColor: colors,//llama la variable
								}]
							},
							options: {//Agregado opciones
								responsive: true, // Hacer que el gráfico sea responsive
								maintainAspectRatio: false, // Mantener el aspect ratio
								legend: {
									display: true, // Mostrar la leyenda
									position: 'bottom', // Posición de la leyenda
								},
								title: {
									display: true,
									text: 'Tickets por personal de TI',
								},
								tooltips: {
									mode: 'index',
									intersect: false,

								},
							}//opciones cerrado
						});

						$chart.data('chart', ordersChart);
					}

					if ($chart.length) {
						initChart($chart);
					}

				})();
			});
			</script>
		<?php endif; ?>

		<?php if(Uri::segment(4) == 'graficas'): ?>
			<script type="text/javascript">
			$(document).ready(function() {
				var BarsChart = (function() {

					var $chart = $('#tick-status');

					function initChart($chart) {

						var colors = ['orange', 'yellow', 'pink', 'red', 'orange']; // Agrega más colores si es necesario

						var ordersChart = new Chart($chart, {
							type: 'pie',
							data: {
								labels: $chart_labels1,
								datasets: [{
									label: $chart_label1,
									data: $chart_data1,
									backgroundColor: colors,//llama la variable
								}]
							},
							options: {//Agregado opciones
								responsive: true, // Hacer que el gráfico sea responsive
								maintainAspectRatio: false, // Mantener el aspect ratio
								legend: {
									display: true, // Mostrar la leyenda
									position: 'bottom', // Posición de la leyenda
								},
								title: {
									display: true,
									text: 'Tickets por estatus',
								},
								tooltips: {
									mode: 'index',
									intersect: false,

								},
							}//opciones cerrado
						});

						$chart.data('chart', ordersChart);
					}

					if ($chart.length) {
						initChart($chart);
					}

				})();
			});
			</script>
		<?php endif; ?>

		<?php if(Uri::segment(4) == 'graficas'): ?>
			<script type="text/javascript">
			$(document).ready(function() {
				var BarsChart = (function() {

					var $chart = $('#tick-types');

					function initChart($chart) {

						var colors = ['orange', 'yellow', 'pink', 'red', 'orange', 'black', 'purple','gray','mint','kelly','magenta','forest','marine']; // Agrega más colores si es necesario

						var ordersChart = new Chart($chart, {
							type: 'bar',
							data: {
								labels: $chart_labels3,
								datasets: [{
									label: $chart_label3,
									data: $chart_data3,
									backgroundColor: colors,//llama la variable
								}]
							},
							options: {//Agregado opciones
								responsive: true, // Hacer que el gráfico sea responsive
								maintainAspectRatio: false, // Mantener el aspect ratio
								legend: {
									display: true, // Mostrar la leyenda
									position: 'bottom', // Posición de la leyenda
								},
								title: {
									display: true,
									text: 'Tickets por tipos',
								},
								tooltips: {
									mode: 'index',
									intersect: false,

								},
							}//opciones cerrado
						});

						$chart.data('chart', ordersChart);
					}

					if ($chart.length) {
						initChart($chart);
					}

				})();
			});
			</script>
		<?php endif; ?>

		<?php if(Uri::segment(4) == 'graficas'): ?>
			<script type="text/javascript">
			$(document).ready(function() {
				var BarsChart = (function() {

					var $chart = $('#tick-department');

					function initChart($chart) {

						var colors = ['orange', 'red', 'orange', 'black', 'purple', 'yellow', 'pink', 'gray','mint','kelly','magenta','forest','marine']; // Agrega más colores si es necesario

						var ordersChart = new Chart($chart, {
							type: 'bar',
							data: {
								labels: $chart_labels4,
								datasets: [{
									label: $chart_label4,
									data: $chart_data4,
									backgroundColor: colors,//llama la variable
								}]
							},
							options: {//Agregado opciones
								responsive: true, // Hacer que el gráfico sea responsive
								maintainAspectRatio: false, // Mantener el aspect ratio
								legend: {
									display: true, // Mostrar la leyenda
									position: 'bottom', // Posición de la leyenda
								},
								title: {
									display: true,
									text: 'Tickets por departamentos',
								},
								tooltips: {
									mode: 'index',
									intersect: false,

								},
							}//opciones cerrado
						});

						$chart.data('chart', ordersChart);
					}

					if ($chart.length) {
						initChart($chart);
					}

				})();
			});
			</script>
		<?php endif; ?>

		<!-- SERVICE WORKER REGISTRATION ESTO ES PARA PODER TRABAJAR OFFILINE-->
		<script>
		if ('serviceWorker' in navigator) {
			window.addEventListener('load', function() {
				navigator.serviceWorker.register('<?php echo Uri::base(false); ?>service-worker.js', {
					scope: '<?php echo Uri::base(false); ?>'
				})
				.then(reg => console.log('[SW] Registrado:', reg.scope))
				.catch(err => console.error('[SW] Error:', err));
			});
		}
	</script>








</body>
</html>
