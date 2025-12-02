<!-- HEADER -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white mb-0"><i class="fas fa-balance-scale"></i> Unidades de Medida (SAT)</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?= Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">SAT · Unidades</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?= Html::anchor('admin/formas/unidades/agregar', '<i class="fas fa-plus"></i> Agregar', ['class' => 'btn btn-sm btn-neutral']); ?>
                    <?= Html::anchor('admin/formas/unidades/exportar', '<i class="fas fa-file-export"></i> Exportar', ['class' => 'btn btn-sm btn-success']); ?>
                    <?= Html::anchor('admin/formas/unidades/template', '<i class="fas fa-download"></i> Plantilla', ['class' => 'btn btn-sm btn-info']); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONTENIDO -->
<div class="container-fluid mt--6">
    <?php if (Session::get_flash('error')): ?>
        <div class="alert alert-danger"><?= Session::get_flash('error'); ?></div>
    <?php endif; ?>
    <?php if (Session::get_flash('success')): ?>
        <div class="alert alert-success"><?= Session::get_flash('success'); ?></div>
    <?php endif; ?>

    <!-- BUSCADOR -->
    <div class="card mb-4">
        <div class="card-body">
            <?= Form::open(['action' => 'admin/formas/unidades/buscar', 'method' => 'post', 'class' => 'form-inline']); ?>
                <div class="input-group w-100">
                    <?= Form::input('search', isset($search) ? $search : '', ['class' => 'form-control', 'placeholder' => 'Buscar clave, nombre o descripción...']); ?>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Buscar</button>
                    </div>
                </div>
            <?= Form::close(); ?>
        </div>
    </div>

    <!-- TABLA -->
    <div class="card shadow">
        <div class="card-header border-0">
            <h3 class="mb-0"><i class="fas fa-list"></i> Catálogo de Unidades</h3>
        </div>

        <div class="table-responsive">
            <table class="table table-flush align-items-center">
                <thead class="thead-light">
                    <tr>
                        <th>Clave</th>
                        <th>Nombre</th>
                        <th>Abreviatura</th>
                        <th>Descripción</th>
                        <th>Origen</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($units)): ?>
                        <?php foreach ($units as $u): ?>
                            <tr>
                                <td><b><?= $u['code']; ?></b></td>
                                <td><?= $u['name']; ?></td>
                                <td><?= $u['abbreviation']; ?></td>
                                <td><?= Str::truncate($u['description'], 50); ?></td>
                                <td>
                                    <?php if ($u['is_internal']): ?>
                                        <span class="badge badge-info">Interna</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">SAT</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($u['active']): ?>
                                        <span class="badge badge-success">Activa</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactiva</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <?= Html::anchor('admin/formas/unidades/info/'.$u['id'], '<i class="fas fa-eye"></i>', ['class' => 'btn btn-sm btn-outline-info']); ?>
                                    <?= Html::anchor('admin/formas/unidades/editar/'.$u['id'], '<i class="fas fa-edit"></i>', ['class' => 'btn btn-sm btn-outline-primary']); ?>
                                    <?= Html::anchor('admin/formas/unidades/eliminar/'.$u['id'], '<i class="fas fa-trash-alt"></i>', [
                                        'class' => 'btn btn-sm btn-outline-danger',
                                        'onclick' => "return confirm('¿Seguro que deseas eliminar esta unidad?');"
                                    ]); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">No hay registros.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card-footer py-4">
            <?= $pagination; ?>
        </div>
    </div>

    <!-- IMPORTAR -->
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="mb-0"><i class="fas fa-file-import"></i> Importar Catálogo SAT (CSV)</h3>
        </div>
        <div class="card-body">
            <?= Form::open(['action' => 'admin/formas/unidades/importar', 'method' => 'post', 'enctype' => 'multipart/form-data']); ?>
                <div class="form-group">
                    <?= Form::file('csv_file', ['class' => 'form-control']); ?>
                    <small class="text-muted">Formato: ClaveUnidad, Nombre, Descripción, Abreviatura</small>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Importar CSV</button>
            <?= Form::close(); ?>
        </div>
    </div>
</div>


<!-- ===========================
     SCRIPTS ADICIONALES
=========================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {

    // === Mostrar nombre de archivo en importación ===
    const csvInput = document.querySelector('input[name="csv_file"]');
    if (csvInput) {
        csvInput.addEventListener('change', function(e) {
            const fileName = e.target.files.length
                ? e.target.files[0].name
                : 'Seleccionar archivo CSV...';
            const label = e.target.nextElementSibling;
            if (label && label.tagName === 'LABEL') {
                label.textContent = fileName;
            }
        });
    }

    // === Confirmación de eliminación ===
    const deleteButtons = document.querySelectorAll('a.btn-outline-danger');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(ev) {
            if (!confirm('¿Seguro que deseas eliminar esta unidad?')) {
                ev.preventDefault();
            }
        });
    });

});
</script>
