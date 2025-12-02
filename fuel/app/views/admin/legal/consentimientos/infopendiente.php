<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Consentimientos Pendientes</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/legal/consentimientos', 'Consentimientos'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Pendientes de <?php echo $user->username; ?>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/legal/consentimientos', '<i class="fa-solid fa-arrow-left"></i> Volver', ['class'=>'btn btn-sm btn-neutral']); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <h3 class="mb-0"><i class="fa-solid fa-clock text-warning"></i> Documentos pendientes de aceptación</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($pendientes)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Título</th>
                                        <th>Categoría</th>
                                        <th>Tipo</th>
                                        <th>Versión</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendientes as $doc): ?>
                                    <tr>
                                        <td><?php echo $doc->title; ?></td>
                                        <td><?php echo ucfirst($doc->category); ?></td>
                                        <td><?php echo str_replace('_',' ', ucfirst($doc->type)); ?></td>
                                        <td><?php echo $doc->version; ?></td>
                                        <td>
                                            <?php echo Html::anchor('admin/legal/documentos/info/'.$doc->id, '<i class="fa-solid fa-eye"></i> Ver', [
                                                'class'=>'btn btn-sm btn-info',
                                                'target'=>'_blank'
                                            ]); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">El usuario no tiene consentimientos pendientes.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
