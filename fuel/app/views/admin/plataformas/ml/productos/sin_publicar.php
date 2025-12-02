<div class="header bg-warning pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-3">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">
                        <i class="fa-solid fa-box"></i> Productos vinculados sin publicar
                    </h6>

                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <a href="<?php echo Uri::base(true).'admin'; ?>">
                                    <i class="fas fa-home"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="<?php echo Uri::base(true).'admin/plataforma/ml'; ?>">
                                    Mercado Libre
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Sin publicar
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid mt--6">

    <!-- ============================= -->
    <!-- FILTROS -->
    <!-- ============================= -->
    <div class="card mb-4">
        <div class="card-body">

            <?php echo Form::open(['method' => 'get']); ?>

            <input type="hidden" name="config_id" value="<?php echo $config->id; ?>">

            <div class="row">

                <div class="col-md-3 mb-3">
                    <label class="form-control-label">Buscar producto</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Código, SKU o nombre..."
                           value="<?php echo e($search); ?>">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-control-label">Categoría</label>
                    <select name="category_id" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat->id; ?>"
                                <?php echo ($category_id == $cat->id ? 'selected' : ''); ?>>
                                <?php echo e($cat->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-control-label">Marca</label>
                    <select name="brand_id" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($brands as $b): ?>
                            <option value="<?php echo $b->id; ?>"
                                <?php echo ($brand_id == $b->id ? 'selected' : ''); ?>>
                                <?php echo e($b->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-control-label">Existencia</label>
                    <div class="custom-control custom-checkbox mt-2">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="chk-stock-only"
                               name="stock_only"
                               value="1"
                               <?php echo ($stock_only ? 'checked' : ''); ?>>
                        <label class="custom-control-label" for="chk-stock-only">
                            Solo con existencia &gt; 0
                        </label>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-md-3 mb-3">
                    <label class="form-control-label">Ordenar por</label>
                    <select name="sort" class="form-control">
                        <option value="name"      <?php echo $sort=='name' ? 'selected' : ''; ?>>Nombre</option>
                        <option value="code"      <?php echo $sort=='code' ? 'selected' : ''; ?>>Código</option>
                        <option value="available" <?php echo $sort=='available' ? 'selected' : ''; ?>>Existencia</option>
                        <option value="category"  <?php echo $sort=='category' ? 'selected' : ''; ?>>Categoría</option>
                        <option value="brand"     <?php echo $sort=='brand' ? 'selected' : ''; ?>>Marca</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-control-label">Dirección</label>
                    <select name="dir" class="form-control">
                        <option value="asc"  <?php echo $dir=='asc' ? 'selected' : ''; ?>>Ascendente</option>
                        <option value="desc" <?php echo $dir=='desc' ? 'selected' : ''; ?>>Descendente</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-control-label">Registros por página</label>
                    <select name="per_page" class="form-control">
                        <option value="25"  <?php echo $per_page==25 ? 'selected' : ''; ?>>25</option>
                        <option value="50"  <?php echo $per_page==50 ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?php echo $per_page==100 ? 'selected' : ''; ?>>100</option>
                        <option value="200" <?php echo $per_page==200 ? 'selected' : ''; ?>>200</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Aplicar filtros
                    </button>
                </div>

            </div>

            <?php echo Form::close(); ?>

        </div>
    </div>


    <!-- ============================= -->
    <!-- LISTADO + ACCIONES MASIVAS -->
    <!-- ============================= -->
    <div class="card">

        <div class="card-header border-0 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">
                Productos vinculados sin publicar
                <span class="badge badge-primary">
                    <?php echo (int)$pagination->total_items; ?>
                </span>
            </h3>

            <div>
                <!-- Podrías agregar aquí un resumen o botón general -->
            </div>
        </div>

        <?php echo Form::open(['method' => 'post']); ?>

        <input type="hidden" name="config_id" value="<?php echo $config->id; ?>">
        <input type="hidden" name="search" value="<?php echo e($search); ?>">
        <input type="hidden" name="category_id" value="<?php echo (int)$category_id; ?>">
        <input type="hidden" name="brand_id" value="<?php echo (int)$brand_id; ?>">
        <input type="hidden" name="stock_only" value="<?php echo (int)$stock_only; ?>">
        <input type="hidden" name="sort" value="<?php echo e($sort); ?>">
        <input type="hidden" name="dir" value="<?php echo e($dir); ?>">
        <input type="hidden" name="per_page" value="<?php echo (int)$per_page; ?>">

        <div class="table-responsive">
            <table class="table table-hover align-items-center">

                <thead class="thead-light">
                    <tr>
                        <th width="40">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="chk-all">
                                <label class="custom-control-label" for="chk-all"></label>
                            </div>
                        </th>
                        <th width="90">Código</th>
                        <th>Producto</th>
                        <th width="110">Existencia</th>
                        <th width="160">Categoría</th>
                        <th width="160">Marca</th>
                        <th width="150" class="text-center">Estado ML</th>
                        <th width="170">Último error</th>
                        <th width="180" class="text-right">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($links as $l): ?>
                        <?php $p = $l->product; ?>
                        <tr>
                            <td>
                                <?php if ($p): ?>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               class="custom-control-input row-check"
                                               id="chk-<?php echo $l->id; ?>"
                                               name="selected[]"
                                               value="<?php echo $l->id; ?>">
                                        <label class="custom-control-label" for="chk-<?php echo $l->id; ?>"></label>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($p): ?>
                                    <span class="badge badge-default"><?php echo e($p->code); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($p): ?>
                                    <strong><?php echo e($p->name); ?></strong><br>
                                    <small class="text-muted">SKU: <?php echo $p->sku ?: '—'; ?></small>
                                <?php else: ?>
                                    <span class="text-danger">Producto eliminado</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php echo $p ? number_format($p->available, 0) : '0'; ?>
                            </td>

                            <td>
                                <?php echo ($p && $p->category) ? e($p->category->name) : '—'; ?>
                            </td>

                            <td>
                                <?php echo ($p && $p->brand) ? e($p->brand->name) : '—'; ?>
                            </td>

                            <td class="text-center">
                                <?php if ((int)$l->ml_enabled === 0): ?>
                                    <span class="badge badge-warning">Inactivo</span>
                                <?php else: ?>
                                    <span class="badge badge-info">Vinculado</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($l->last_error_at): ?>
                                    <span class="text-danger">
                                        <?php echo date('d/m/Y H:i', $l->last_error_at); ?>
                                    </span>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>

                            <td class="text-right">

                                <?php if ($p): ?>
                                    <?php echo Html::anchor(
                                        'admin/plataforma/ml/productos/editar/'.$l->id,
                                        '<i class="fas fa-edit"></i>',
                                        [
                                            'class' => 'btn btn-sm btn-primary',
                                            'title' => 'Editar configuración ML'
                                        ]
                                    ); ?>

                                    <?php echo Html::anchor(
                                        'admin/plataforma/ml/productos/publicar/'.$l->id,
                                        '<i class="fa-solid fa-cloud-upload-alt"></i>',
                                        [
                                            'class' => 'btn btn-sm btn-success',
                                            'title' => 'Publicar en Mercado Libre'
                                        ]
                                    ); ?>

                                    <?php echo Html::anchor(
                                        'admin/plataforma/ml/productos/desvincular/'.$l->id,
                                        '<i class="fas fa-unlink"></i>',
                                        [
                                            'class'   => 'btn btn-sm btn-danger',
                                            'title'   => 'Desvincular',
                                            'onclick' => "return confirm('¿Eliminar vínculo con Mercado Libre?');"
                                        ]
                                    ); ?>
                                <?php endif; ?>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">

            <div class="mb-2 mb-md-0">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="bulk_action">Acción masiva</label>
                    </div>
                    <select name="bulk_action" id="bulk_action" class="form-control">
                        <option value="">Seleccionar…</option>
                        <option value="publicar">Publicar seleccionados</option>
                        <option value="desvincular">Desvincular seleccionados</option>
                        <option value="export">Exportar CSV seleccionados</option>
                    </select>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-play"></i> Ejecutar
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <?php echo $pagination->render(); ?>
            </div>

        </div>

        <?php echo Form::close(); ?>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var chkAll = document.getElementById('chk-all');
    if (chkAll) {
        chkAll.addEventListener('change', function () {
            var checks = document.querySelectorAll('.row-check');
            for (var i = 0; i < checks.length; i++) {
                checks[i].checked = chkAll.checked;
            }
        });
    }
});
</script>
