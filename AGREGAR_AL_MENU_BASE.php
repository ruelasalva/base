<?php
/**
 * CÓDIGO PARA AGREGAR AL MENÚ DEL PROYECTO BASE
 * Insertar en: base/fuel/app/views/admin/template.php
 * Ubicación: Después de la sección "Datos Fiscales" (buscar la línea que contiene "Datos Fiscales")
 *            Antes del cierre del menú
 * 
 * INSTRUCCIONES:
 * 1. Abrir: base/fuel/app/views/admin/template.php
 * 2. Buscar la sección de "Datos Fiscales" en el menú lateral
 * 3. Insertar este código DESPUÉS de esa sección
 * 4. Guardar y probar en el navegador
 */
?>

<!-- FACTURACIÓN ELECTRÓNICA CFDI -->
<?php if (Helper_Permission::can('facturacion', 'view') || Helper_Permission::can('sat', 'view')): ?>
	<li class="nav-item">
		<a class="nav-link <?php echo (in_array(Uri::segment(2), ['facturacion','sat'])) ? 'active' : ''; ?>" 
		   href="#navbar-facturacion-cfdi" data-toggle="collapse"
		   role="button" aria-expanded="<?php echo (in_array(Uri::segment(2), ['facturacion','sat'])) ? 'true' : 'false'; ?>"
		   aria-controls="navbar-facturacion-cfdi">
			<i class="fa-solid fa-file-invoice-dollar text-success"></i>
			<span class="nav-link-text ml-2">Facturación CFDI</span>
			<i class="fa-solid fa-chevron-down float-right"></i>
		</a>
		<div class="collapse <?php echo (in_array(Uri::segment(2), ['facturacion','sat'])) ? 'show' : ''; ?>" id="navbar-facturacion-cfdi">
			<ul class="nav nav-sm flex-column ml-3">
				<?php if (Helper_Permission::can('facturacion', 'view')): ?>
					<li class="nav-item">
						<?php echo Html::anchor('admin/facturacion', '<i class="fa-solid fa-file-invoice text-primary"></i> <span>Facturas</span>', ['class' => 'nav-link']); ?>
					</li>
				<?php endif; ?>
				
				<?php if (Helper_Permission::can('facturacion', 'create')): ?>
					<li class="nav-item">
						<?php echo Html::anchor('admin/facturacion/create', '<i class="fa-solid fa-plus-circle text-success"></i> <span>Nueva Factura</span>', ['class' => 'nav-link']); ?>
					</li>
				<?php endif; ?>
				
				<?php if (Helper_Permission::can('facturacion', 'edit')): ?>
					<li class="nav-item">
						<?php echo Html::anchor('admin/facturacion/configuracion', '<i class="fa-solid fa-cog text-warning"></i> <span>Configuración</span>', ['class' => 'nav-link']); ?>
					</li>
				<?php endif; ?>
				
				<?php if (Helper_Permission::can('sat', 'view')): ?>
					<li class="nav-item"><span class="text-xs text-muted ml-2">Catálogos SAT</span></li>
					<li class="nav-item">
						<?php echo Html::anchor('admin/sat/productos', '<i class="fa-solid fa-box text-info"></i> <span>Productos/Servicios</span>', ['class' => 'nav-link']); ?>
					</li>
					<li class="nav-item">
						<?php echo Html::anchor('admin/sat/unidades', '<i class="fa-solid fa-ruler text-info"></i> <span>Unidades</span>', ['class' => 'nav-link']); ?>
					</li>
					<li class="nav-item">
						<?php echo Html::anchor('admin/sat/uso_cfdi', '<i class="fa-solid fa-list-check text-info"></i> <span>Uso de CFDI</span>', ['class' => 'nav-link']); ?>
					</li>
					<li class="nav-item">
						<?php echo Html::anchor('admin/sat/formas_pago', '<i class="fa-regular fa-credit-card text-info"></i> <span>Formas de Pago</span>', ['class' => 'nav-link']); ?>
					</li>
					<li class="nav-item">
						<?php echo Html::anchor('admin/sat/metodos_pago', '<i class="fa-solid fa-money-bill-wave text-info"></i> <span>Métodos de Pago</span>', ['class' => 'nav-link']); ?>
					</li>
					<li class="nav-item">
						<?php echo Html::anchor('admin/sat/regimenes', '<i class="fa-solid fa-building text-info"></i> <span>Regímenes Fiscales</span>', ['class' => 'nav-link']); ?>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</li>
<?php endif; ?>

<!-- CONTABILIDAD -->
<?php if (
	Helper_Permission::can('cuentascontables', 'view') ||
	Helper_Permission::can('polizas', 'view') ||
	Helper_Permission::can('libromayor', 'view') ||
	Helper_Permission::can('reportesfinancieros', 'view')
	): ?>
	<li class="nav-item">
		<a class="nav-link <?php echo (in_array(Uri::segment(2), ['cuentascontables','polizas','libromayor','reportesfinancieros'])) ? 'active' : ''; ?>" 
		   href="#navbar-contabilidad" data-toggle="collapse"
		   role="button" aria-expanded="<?php echo (in_array(Uri::segment(2), ['cuentascontables','polizas','libromayor','reportesfinancieros'])) ? 'true' : 'false'; ?>"
		   aria-controls="navbar-contabilidad">
			<i class="fa-solid fa-calculator text-primary"></i>
			<span class="nav-link-text ml-2">Contabilidad</span>
			<i class="fa-solid fa-chevron-down float-right"></i>
		</a>
		<div class="collapse <?php echo (in_array(Uri::segment(2), ['cuentascontables','polizas','libromayor','reportesfinancieros'])) ? 'show' : ''; ?>" id="navbar-contabilidad">
			<ul class="nav nav-sm flex-column ml-3">
				<?php if (Helper_Permission::can('cuentascontables', 'view')): ?>
					<li class="nav-item">
						<?php echo Html::anchor('admin/cuentascontables', '<i class="fa-solid fa-list-alt text-primary"></i> <span>Catálogo de Cuentas</span>', ['class' => 'nav-link']); ?>
					</li>
				<?php endif; ?>
				
				<?php if (Helper_Permission::can('polizas', 'view')): ?>
					<li class="nav-item">
						<?php echo Html::anchor('admin/polizas', '<i class="fa-solid fa-file-invoice text-success"></i> <span>Pólizas</span>', ['class' => 'nav-link']); ?>
					</li>
				<?php endif; ?>
				
				<?php if (Helper_Permission::can('libromayor', 'view')): ?>
					<li class="nav-item">
						<?php echo Html::anchor('admin/libromayor', '<i class="fa-solid fa-book text-warning"></i> <span>Libro Mayor</span>', ['class' => 'nav-link']); ?>
					</li>
				<?php endif; ?>
				
				<?php if (Helper_Permission::can('reportesfinancieros', 'view')): ?>
					<li class="nav-item"><span class="text-xs text-muted ml-2">Reportes</span></li>
					<li class="nav-item">
						<?php echo Html::anchor('admin/reportesfinancieros/balance', '<i class="fa-solid fa-balance-scale text-info"></i> <span>Balance General</span>', ['class' => 'nav-link']); ?>
					</li>
					<li class="nav-item">
						<?php echo Html::anchor('admin/reportesfinancieros/resultados', '<i class="fa-solid fa-chart-line text-info"></i> <span>Estado de Resultados</span>', ['class' => 'nav-link']); ?>
					</li>
					<li class="nav-item">
						<?php echo Html::anchor('admin/reportesfinancieros/flujo', '<i class="fa-solid fa-hand-holding-usd text-info"></i> <span>Flujo de Efectivo</span>', ['class' => 'nav-link']); ?>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</li>
<?php endif; ?>

<!-- CATÁLOGOS ADICIONALES -->
<?php if (
	Helper_Permission::can('almacenes', 'view') ||
	Helper_Permission::can('proveedores', 'view') ||
	Helper_Permission::can('marcas', 'view') ||
	Helper_Permission::can('categorias', 'view')
	): ?>
	<li class="nav-item">
		<a class="nav-link <?php echo (in_array(Uri::segment(2), ['almacenes','proveedores','marcas','categorias'])) ? 'active' : ''; ?>" 
		   href="#navbar-catalogos-adicionales" data-toggle="collapse"
		   role="button" aria-expanded="<?php echo (in_array(Uri::segment(2), ['almacenes','proveedores','marcas','categorias'])) ? 'true' : 'false'; ?>"
		   aria-controls="navbar-catalogos-adicionales">
			<i class="fa-solid fa-folder-open text-warning"></i>
			<span class="nav-link-text ml-2">Catálogos</span>
			<i class="fa-solid fa-chevron-down float-right"></i>
		</a>
		<div class="collapse <?php echo (in_array(Uri::segment(2), ['almacenes','proveedores','marcas','categorias'])) ? 'show' : ''; ?>" id="navbar-catalogos-adicionales">
			<ul class="nav nav-sm flex-column ml-3">
				<?php if (Helper_Permission::can('almacenes', 'view')): ?>
					<li class="nav-item">
						<?php echo Html::anchor('admin/almacenes', '<i class="fa-solid fa-warehouse text-primary"></i> <span>Almacenes</span>', ['class' => 'nav-link']); ?>
					</li>
				<?php endif; ?>
				
				<?php if (Helper_Permission::can('proveedores', 'view')): ?>
					<li class="nav-item">
						<?php echo Html::anchor('admin/proveedores', '<i class="fa-solid fa-truck text-success"></i> <span>Proveedores</span>', ['class' => 'nav-link']); ?>
					</li>
				<?php endif; ?>
				
				<?php if (Helper_Permission::can('marcas', 'view')): ?>
					<li class="nav-item">
						<?php echo Html::anchor('admin/marcas', '<i class="fa-solid fa-tag text-info"></i> <span>Marcas</span>', ['class' => 'nav-link']); ?>
					</li>
				<?php endif; ?>
				
				<?php if (Helper_Permission::can('categorias', 'view')): ?>
					<li class="nav-item">
						<?php echo Html::anchor('admin/categorias', '<i class="fa-solid fa-folder text-warning"></i> <span>Categorías</span>', ['class' => 'nav-link']); ?>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</li>
<?php endif; ?>
