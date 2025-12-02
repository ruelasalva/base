<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0 text-center">
                        <span class="glyphicon glyphicon-refresh"></span>
                        Restablecer Contraseña
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($error) && empty($success)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                            <br><br>
                            <a href="<?php echo \Uri::create('auth/forgot'); ?>" class="btn btn-warning btn-sm">
                                Solicitar nuevo enlace
                            </a>
                        </div>
                    <?php elseif (!empty($success)): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                            <br><br>
                            <a href="<?php echo \Uri::create('auth/login'); ?>" class="btn btn-success btn-sm">
                                Ir a Iniciar Sesión
                            </a>
                        </div>
                    <?php else: ?>

                    <p class="text-muted mb-3">
                        Ingrese su nueva contraseña.
                    </p>

                    <form method="post" action="<?php echo \Uri::create('auth/reset/' . $token); ?>">
                        <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">

                        <div class="form-group mb-3">
                            <label for="password">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Mínimo 6 caracteres" required autofocus>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password_confirm">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                                   placeholder="Repita la contraseña" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-info btn-block text-white">
                                Restablecer Contraseña
                            </button>
                        </div>
                    </form>

                    <?php endif; ?>
                </div>
                <div class="card-footer text-center">
                    <a href="<?php echo \Uri::create('auth/login'); ?>">Volver a Iniciar Sesión</a>
                </div>
            </div>
        </div>
    </div>
</div>
