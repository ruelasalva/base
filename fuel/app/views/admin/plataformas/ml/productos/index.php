<?php
// Helper simple para generar links de orden
function ml_sort_link($label, $field, $current_sort, $current_dir, $config_id, $search)
{
    $dir = 'asc';
    $icon = '';

    if ($current_sort === $field) {
        if ($current_dir === 'asc') {
            $dir = 'desc';
            $icon = ' <i class="fas fa-sort-up"></i>';
        } else {
            $dir = 'asc';
            $icon = ' <i class="fas fa-sort-down"></i>';
        }
    } else {
        $dir = 'asc';
        $icon = ' <i class="fas fa-sort"></i>';
    }

    $url = Uri::create('admin/plataforma/ml/productos', array(), array(
        'config_id' => $config_id,
        'search'    => $search,
        'sort'      => $field,
        'dir'       => $dir,
    ));

    return '<a href="'.$url.'">'.$label.$icon.'</a>';
}
?>

<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">
                        <i class="fa-brands fa-mercadolibre"></i> Productos Mercado Libre
                    </h6>

                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <a href="<?php echo Uri::create('admin'); ?>">
                                    <i class="fas fa-home"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="<?php echo Uri::create('admin/plataforma/ml'); ?>">Mercado Libre</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Productos</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt--6">

    <!-- ========================================= -->
    <!-- FILTROS SUPERIORES -->
    <!-- ========================================= -->
    <div class="card mb-4">
        <div class="card-body">

            <?php echo Form::open(['method' => 'get']); ?>

            <div class="row">

                <!-- Cuenta ML -->
                <div class="col-md-4 mb-3">
                    <label class="form-control-label">Cuenta Mercado Libre</label>
                    <select name="config_id" class="form-control" onchange="this.form.submit()">
                        <?php foreach ($configs as $c): ?>
                            <option value="<?php echo $c->id; ?>"
                                <?php echo ($c->id == $config->id) ? 'selected' : ''; ?>>
                                <?php echo $c->name; ?> (<?php echo $c->mode; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Buscar producto -->
                <div class="col-md-4 mb-3">
                    <label class="form-control-label">Buscar producto</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Código, nombre, marca, familia..."
                           value="<?php echo e($search); ?>">
                </div>

                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>

            </div>

            <?php echo Form::close(); ?>

        </div>
    </div>


    <!-- ========================================= -->
    <!-- TABLA DE PRODUCTOS ERP + ESTADO ML -->
    <!-- ========================================= -->
    <div class="card">
        <div class="card-header border-0">
            <h3 class="mb-0">Listado de productos (ERP → Mercado Libre)</h3>
        </div>

        <div class="table-responsive table-responsive-xl">
            <table class="table align-items-center table-hover">
                <thead class="thead-light">
                <tr>
                    <th width="90">
                        <?php echo ml_sort_link('Código', 'code', $sort, $dir, $config->id, $search); ?>
                    </th>
                    <th>
                        <?php echo ml_sort_link('Producto', 'name', $sort, $dir, $config->id, $search); ?>
                    </th>
                    <th>
                        <?php echo ml_sort_link('Marca', 'brand', $sort, $dir, $config->id, $search); ?>
                    </th>
                    <th>
                        <?php echo ml_sort_link('Familia', 'category', $sort, $dir, $config->id, $search); ?>
                    </th>
                    <th class="text-center">
                        <?php echo ml_sort_link('Existencia', 'available', $sort, $dir, $config->id, $search); ?>
                    </th>
                    <th class="text-center">Estado ML</th>
                    <th width="220" class="text-right">Acciones</th>
                </tr>
                </thead>

                <tbody>

                <?php foreach ($products as $p): ?>
                    <?php
                    $link = isset($links_by_product[$p->id])
                        ? $links_by_product[$p->id]
                        : null;
                    ?>

                    <tr>
                        <!-- Código -->
                        <td>
                            <span class="badge badge-default">
                                <?php echo e($p->code); ?>
                            </span>
                        </td>

                        <!-- Nombre -->
                        <td>
                            <strong><?php echo e($p->name); ?></strong>
                        </td>

                        <!-- Marca -->
                        <td>
                            <?php echo ($p->brand) ? e($p->brand->name) : '-'; ?>
                        </td>

                        <!-- Familia (categoría) -->
                        <td>
                            <?php echo ($p->category) ? e($p->category->name) : '-'; ?>
                        </td>

                        <!-- Existencia -->
                        <td class="text-center">
                            <span class="badge badge-info">
                                <?php echo number_format((int) $p->available); ?>
                            </span>
                        </td>

                        <!-- Estado ML -->
                        <td class="text-center">

                            <?php if (!$link): ?>
                                <span class="badge badge-secondary">No vinculado</span>

                            <?php elseif ($link->ml_enabled == 0): ?>
                                <span class="badge badge-warning">Inactivo</span>

                            <?php elseif ($link->ml_item_id): ?>
                                <span class="badge badge-success">Publicado</span>

                            <?php else: ?>
                                <span class="badge badge-info">Vinculado</span>
                            <?php endif; ?>

                        </td>

                        <!-- Acciones -->
                        <td class="text-right">

                            <?php if (!$link): ?>

                                <!-- Vincular -->
                                <?php echo Html::anchor(
                                    'admin/plataforma/ml/productos/vincular/'.$config->id.'/'.$p->id,
                                    '<i class="fas fa-link"></i>',
                                    [
                                        'class' => 'btn btn-sm btn-success',
                                        'title' => 'Vincular a ML'
                                    ]
                                ); ?>

                            <?php else: ?>

                                <!-- Editar configuración ML -->
                                <?php echo Html::anchor(
                                    'admin/plataforma/ml/productos/editar/'.$link->id,
                                    '<i class="fas fa-edit"></i>',
                                    [
                                        'class' => 'btn btn-sm btn-primary',
                                        'title' => 'Editar configuración ML'
                                    ]
                                ); ?>

                                <!-- Desvincular -->
                                <?php echo Html::anchor(
                                    'admin/plataforma/ml/productos/desvincular/'.$link->id,
                                    '<i class="fas fa-unlink"></i>',
                                    [
                                        'class' => 'btn btn-sm btn-danger',
                                        'title' => 'Desvincular de ML',
                                        'onclick' => "return confirm('¿Eliminar vínculo ML?');"
                                    ]
                                ); ?>

                            <?php endif; ?>

                        </td>
                    </tr>

                <?php endforeach; ?>

                </tbody>
            </table>
        </div>

        <?php if (isset($pagination)): ?>
            <div class="card-footer py-4">
                <?php echo $pagination->render(); ?>
            </div>
        <?php endif; ?>

    </div>
</div>
