<!-- ENCABEZADO -->
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
                            <li class="breadcrumb-item active" aria-current="page">Documentos Legales</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/perfil/legal', 'Actualizar', ['class' => 'btn btn-sm btn-neutral']); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONTENIDO -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <div class="card">

                <!-- BÚSQUEDA -->
                <div class="card-header border-0">
                    <?php echo Form::open(['action' => 'admin/perfil/legal/buscar', 'method' => 'post']); ?>
                    <div class="form-row">
                        <div class="col-md-9">
                            <h3 class="mb-0">Lista de documentos legales</h3>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-sm mt-3 mt-md-0">
                                <?php echo Form::input('search', (isset($search) ? $search : ''), [
                                    'id'          => 'search',
                                    'class'       => 'form-control',
                                    'placeholder' => 'Buscar por título o versión',
                                    'aria-describedby' => 'button-addon'
                                ]); ?>
                                <div class="input-group-append">
                                    <?php echo Form::submit([
                                        'value'=> 'Buscar',
                                        'name' => 'submit',
                                        'id'   => 'button-addon',
                                        'class'=> 'btn btn-outline-primary'
                                    ]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo Form::close(); ?>
                </div>

                <!-- TABLA -->
                <div class="table-responsive" 
                     data-toggle="lists" 
                     data-list-values='["id","title","version","created_at"]'>
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Versión</th>
                                <th>Fecha</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            <?php if (!empty($documents)): ?>
                                <?php foreach ($documents as $doc): ?>
                                    <tr>
                                        <td><?php echo Html::anchor('admin/perfil/legal/info/'.$doc->id, $doc->id); ?></td>
                                        <td><?php echo $doc->title; ?></td>
                                        <td><?php echo $doc->version; ?></td>
                                        <td><?php echo $doc->created_at ? date('d/m/Y', $doc->created_at) : '-'; ?></td>
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <?php echo Html::anchor('admin/perfil/legal/info/'.$doc->id, 'Ver', ['class' => 'dropdown-item']); ?>
                                                    <?php echo Html::anchor('admin/perfil/legal/imprimir/'.$doc->id, 'Imprimir', ['class' => 'dropdown-item', 'target' => '_blank']); ?>
                                                    <?php echo Html::anchor('admin/perfil/legal/ver_pdf/'.$doc->id,'Ver en PDF',  ['class' => 'dropdown-item']); ?>
                                                    <?php echo Html::anchor('admin/perfil/legal/descargar/'.$doc->id,'Descargar en PDF',  ['class' => 'dropdown-item']); ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">No se encontraron documentos legales.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($pagination)): ?>
                <div class="card-footer py-4">
                    <?php echo $pagination; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
