<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Documentos Legales</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Documentos Legales
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/legal/documentos/agregar', '<i class="fa-solid fa-plus"></i> Nuevo Documento', ['class' => 'btn btn-sm btn-neutral']); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <div class="card">

                
                <!-- CARD HEADER -->
				<div class="card-header border-0">
					<?php echo Form::open(['action' => 'admin/legal/documentos/buscar', 'method' => 'get']); ?>
					<div class="form-row align-items-center">
						<!-- TÍTULO -->
						<div class="col-md-3 mb-2">
							<?php echo Form::input('title', isset($filters['title']) ? $filters['title'] : '', [
								'class' => 'form-control form-control-sm',
								'placeholder' => 'Buscar por título'
							]); ?>
						</div>

						<!-- TIPO -->
						<div class="col-md-2 mb-2">
							<?php echo Form::select('type', isset($filters['type']) ? $filters['type'] : '', [
								''                => 'Tipo...',
								'aviso_privacidad'=> 'Aviso Privacidad',
								'terminos'        => 'Términos',
								'politicas'       => 'Políticas',
								'cookies'         => 'Cookies',
								'newsletter'      => 'Newsletter',
								'medidas'         => 'Medidas',
								'codigo'          => 'Código',
								'otros'           => 'Otros',
							], ['class' => 'form-control form-control-sm']); ?>
						</div>

						<!-- CATEGORÍA -->
						<div class="col-md-2 mb-2">
							<?php echo Form::select('category', isset($filters['category']) ? $filters['category'] : '', [
								''          => 'Categoría...',
								'cliente'   => 'Cliente',
								'proveedor' => 'Proveedor',
								'socio'     => 'Socio',
								'empleado'  => 'Empleado',
								'visitante' => 'Visitante',
								'general'   => 'General',
							], ['class' => 'form-control form-control-sm']); ?>
						</div>

						<!-- ESTADO ACTIVO -->
						<div class="col-md-2 mb-2">
							<?php echo Form::select('active', isset($filters['active']) ? $filters['active'] : '', [
								''  => 'Estado...',
								'0' => 'Activo',
								'1' => 'Inactivo'
							], ['class' => 'form-control form-control-sm']); ?>
						</div>

						<!-- ARCHIVO -->
						<div class="col-md-2 mb-2">
							<?php echo Form::select('has_file', isset($filters['has_file']) ? $filters['has_file'] : '', [
								''  => 'Archivo...',
								'1' => 'Con archivo',
								'0' => 'Sin archivo'
							], ['class' => 'form-control form-control-sm']); ?>
						</div>

						<!-- BOTONES -->
						<div class="col-md-1 mb-2 text-right">
							<?php echo Form::submit('submit', 'Buscar', [
									'id' => 'button-addon',
									'class'=>'btn btn-sm btn-outline-primary'
								]); ?>
						</div>
					</div>
					<?php echo Form::close(); ?>
				</div>


                <!-- FLASH MESSAGES -->
                <div class="px-3">
                    <?php if (Session::get_flash('success')): ?>
                        <div class="alert alert-success"><?php echo Session::get_flash('success'); ?></div>
                    <?php endif; ?>
                    <?php if (Session::get_flash('error')): ?>
                        <div class="alert alert-danger"><?php echo Session::get_flash('error'); ?></div>
                    <?php endif; ?>
                </div>

                <!-- TABLE -->
                <div class="table-responsive">
                    <table class="table align-items-center table-flush table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Título</th>
                                <th>Categoría</th>
                                <th>Tipo</th>
                                <th>Versión</th>
                                <th>Activo</th>
                                <th>Editable</th>
                                <th>Descargable</th>
                                <th>Obligatorio</th>
                                <th>Archivo</th>
                                <th>Última actualización</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($documents)): ?>
                                <?php foreach ($documents as $doc): ?>
                                    <tr>
                                        <td><?php echo  Html::anchor('admin/legal/documentos/info/'.$doc['id'],$doc['title']); ?></td>
                                        <td><span class="badge badge-info"><?php echo ucfirst($doc['category']); ?></span></td>
                                        <td><span class="badge badge-secondary"><?php echo str_replace('_',' ', ucfirst($doc['type'])); ?></span></td>
                                        <td><?php echo $doc['version']; ?></td>
                                        <td><?php echo $doc['active'] == 0 ? '<span class="badge badge-success">Sí</span>' : '<span class="badge badge-danger">No</span>'; ?></td>
                                        <td><?php echo $doc['allow_edit'] == 0 ? '<i class="fa-solid fa-check text-success"></i>' : '<i class="fa-solid fa-xmark text-danger"></i>'; ?></td>
                                        <td><?php echo $doc['allow_download'] == 0 ? '<i class="fa-solid fa-check text-success"></i>' : '<i class="fa-solid fa-xmark text-danger"></i>'; ?></td>
                                        <td><?php echo $doc['required'] == 1 ? '<span class="badge badge-success">Sí</span>' : '<span class="badge badge-danger">No</span>'; ?></td>
                                        <td>
                                            <?php if ($doc['upload_path']): ?>
                                                <span class="badge badge-primary"><i class="fa-solid fa-file"></i> Sí</span>
                                            <?php else: ?>
                                                <span class="badge badge-light text-muted"><i class="fa-solid fa-file-slash"></i> No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo  $doc['updated_at']; ?> </td>
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <?php echo Html::anchor('admin/legal/documentos/info/'.$doc['id'], '<i class="fa-solid fa-eye"></i> Ver', ['class'=>'dropdown-item']); ?>
                                                    <?php echo Html::anchor('admin/legal/documentos/editar/'.$doc['id'], '<i class="fa-solid fa-pen-to-square"></i> Editar', ['class'=>'dropdown-item']); ?>
                                                    <?php echo Html::anchor('admin/legal/documentos/eliminar/'.$doc['id'], '<i class="fa-solid fa-trash"></i> Eliminar', [
                                                        'class'=>'dropdown-item',
                                                        'onclick'=>"return confirm('¿Seguro que deseas inactivar este documento?');"
                                                    ]); ?>
                                                    <?php if ($doc['allow_download'] == 0): ?>
                                                        <?php echo Html::anchor('admin/legal/documentos/download/'.$doc['id'].'?mode=download', '<i class="fa-solid fa-file-arrow-down"></i> Descargar PDF', ['class'=>'dropdown-item']); ?>
                                                        <?php echo Html::anchor('admin/legal/documentos/download/'.$doc['id'].'?mode=preview', '<i class="fa-solid fa-eye"></i> Ver en navegador', [
                                                            'class'=>'dropdown-item',
                                                            'target'=>'_blank'
                                                        ]); ?>
                                                    <?php endif; ?>
                                                    <?php if ($doc['upload_path']): ?>
                                                        <?php echo Html::anchor('admin/legal/documentos/file/'.$doc['id'], '<i class="fa-solid fa-file"></i> Descargar archivo original', [
                                                            'class'=>'dropdown-item'
                                                        ]); ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted">No hay documentos legales registrados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINACIÓN (si aplica) -->
                <?php if(isset($pagination) && $pagination != ''): ?>
                    <div class="card-footer py-4">
                        <?php echo $pagination; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
