<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-warning">
                    <h4 class="mb-0 text-center">
                        <span class="glyphicon glyphicon-lock"></span>
                        Recuperar Contraseña
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                        </div>
                    <?php else: ?>

                    <p class="text-muted mb-3">
                        Ingrese su email y le enviaremos un enlace para restablecer su contraseña.
                    </p>

                    <form method="post" action="<?php echo \Uri::create('auth/forgot'); ?>">
                        <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">

                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars(\Input::post('email', '')); ?>" 
                                   placeholder="correo@ejemplo.com" required autofocus>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning btn-block">
                                Enviar Enlace
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
