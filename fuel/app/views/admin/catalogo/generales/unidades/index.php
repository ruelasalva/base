<!-- HEADER -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Catálogo de Unidades</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
                            <li class="breadcrumb-item active" aria-current="page">Unidades</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php if (Helper_Permission::can('catalogo_unidades', 'create')): ?>
                        <?php echo Html::anchor('admin/catalogo/generales/unidades/agregar', '<i class="fa fa-plus"></i> Agregar', array('class' => 'btn btn-sm btn-neutral')); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">

    <!-- BUSCADOR -->
    <div class="card mb-3">
        <div class="card-body">
            <?php echo Form::open(array('action' => 'admin/catalogo/generales/unidades/buscar', 'method' => 'post')); ?>
                <div class="form-row">
                    <div class="col-md-10 mb-2">
                        <?php echo Form::input('search', isset($search) ? str_replace('%',' ',$search) : '', array('class'=>'form-control','placeholder'=>'Buscar por código, nombre, abreviatura o descripción')); ?>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary btn-block" type="submit"><i class="fa fa-search"></i> Buscar</button>
                    </div>
                </div>
            <?php echo Form::close(); ?>
        </div>
    </div>

    <!-- LISTADO -->
    <div class="card">
        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Abrev.</th>
                        <th>Descripción</th>
                        <th class="text-right">Factor</th>
                        <th>Origen</th>
                        <th>Estatus</th>
                        <th class="text-right">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($units)): foreach ($units as $u): ?>
                        <tr>
                            <td><?php echo e($u['code']); ?></td>
                            <td><?php echo e($u['name']); ?></td>
                            <td><?php echo e($u['abbreviation']); ?></td>
                            <td><?php echo e($u['description']); ?></td>
                            <td class="text-right"><?php echo number_format((float)$u['conversion_factor'], 4); ?></td>
                            <td>
                                <?php if ((int)$u['is_internal'] === 1): ?>
                                    <span class="badge badge-info">Interna</span>
                                <?php else: ?>
                                    <span class="badge badge-success">SAT</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((int)$u['active'] === 1): ?>
                                    <span class="badge badge-primary">Activa</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <?php echo Html::anchor('admin/catalogo/generales/unidades/info/'.$u['id'], '<i class="fas fa-eye"></i>', array('class'=>'btn btn-sm btn-outline-secondary','title'=>'Ver')); ?>
                                <?php if (Helper_Permission::can('catalogo_unidades', 'edit')): ?>
                                    <?php echo Html::anchor('admin/catalogo/generales/unidades/editar/'.$u['id'], '<i class="fas fa-edit"></i>', array('class'=>'btn btn-sm btn-outline-primary','title'=>'Editar')); ?>
                                <?php endif; ?>
                                <?php if (Helper_Permission::can('catalogo_unidades', 'delete')): ?>
                                    <?php echo Html::anchor('admin/catalogo/generales/unidades/eliminar/'.$u['id'], '<i class="fas fa-trash"></i>', array('class'=>'btn btn-sm btn-outline-danger','title'=>'Eliminar','onclick'=>"return confirm('¿Eliminar unidad?');")); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="8" class="text-center text-muted">Sin registros</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($pagination)): ?>
            <div class="card-footer py-4">
                <nav aria-label="..."><?php echo $pagination; ?></nav>
            </div>
        <?php endif; ?>
    </div>
</div>
