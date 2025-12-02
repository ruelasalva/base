<div class="header bg-danger pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <h6 class="h2 text-white py-3">Errores Mercado Libre</h6>
        </div>
    </div>
</div>

<div class="container-fluid mt--6">

    <div class="card mb-4">
        <div class="card-body">
            <?php echo Form::open(['method' => 'get']); ?>

            <div class="row">
                <div class="col-md-6">
                    <label>Cuenta ML</label>
                    <select name="config_id" class="form-control" onchange="this.form.submit()">
                        <?php foreach ($configs as $c): ?>
                            <option value="<?php echo $c->id; ?>"
                                <?php echo ($config->id == $c->id) ? 'selected' : ''; ?>>
                                <?php echo e($c->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 d-flex align-items-end">
                    <button class="btn btn-danger w-100">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </div>

            <?php echo Form::close(); ?>
        </div>
    </div>


    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Item ML</th>
                        <th>Código error</th>
                        <th>Mensaje</th>
                        <th>Origen</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($errors as $e): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', $e->created_at); ?></td>
                        <td><?php echo $e->product ? e($e->product->name) : '-'; ?></td>
                        <td><?php echo e($e->ml_item_id ?: '-'); ?></td>
                        <td><span class="badge badge-danger"><?php echo e($e->error_code); ?></span></td>
                        <td><?php echo e(Str::truncate($e->error_message, 100)); ?></td>
                        <td><?php echo e($e->origin); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

            </table>
        </div>

        <div class="card-footer">
            <?php echo $pagination->render(); ?>
        </div>
    </div>
</div>
