<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white mb-0">Logs Mercado Libre</h6>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt--6">
    <div class="card mb-4">
        <div class="card-body">
            <?php echo Form::open(['method' => 'get']); ?>
            
            <div class="row">

                <div class="col-md-4">
                    <label>Cuenta ML</label>
                    <select name="config_id" class="form-control" onchange="this.form.submit()">
                        <?php foreach ($configs as $c): ?>
                            <option value="<?php echo $c->id ?>"
                                <?php echo ($config->id == $c->id) ? 'selected' : ''; ?>>
                                <?php echo e($c->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Fecha</label>
                    <input type="date" name="fecha" class="form-control"
                           value="<?php echo e($fecha); ?>">
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Buscar
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
                        <th>Recurso</th>
                        <th>ID</th>
                        <th>Operación</th>
                        <th>Status</th>
                        <th>Mensaje</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($logs as $l): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', $l->created_at); ?></td>
                        <td><?php echo e($l->resource); ?></td>
                        <td><?php echo e($l->resource_id); ?></td>
                        <td><?php echo e($l->operation); ?></td>
                        <td>
                            <?php if ($l->status >= 400): ?>
                                <span class="badge badge-danger"><?php echo $l->status; ?></span>
                            <?php else: ?>
                                <span class="badge badge-success"><?php echo $l->status; ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e(Str::truncate($l->message, 120)); ?></td>
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
