<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0 text-center">
                        <span class="glyphicon glyphicon-log-in"></span>
                        Iniciar Sesión
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?php echo \Uri::create('auth/login'); ?>">
                        <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">

                        <div class="form-group mb-3">
                            <label for="username">Usuario o Email</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo \Input::post('username', ''); ?>" 
                                   placeholder="Ingrese su usuario o email" required autofocus>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Ingrese su contraseña" required>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                                <label class="form-check-label" for="remember">Recordarme</label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-block">
                                Iniciar Sesión
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <a href="<?php echo \Uri::create('auth/forgot'); ?>">¿Olvidó su contraseña?</a>
                    <br>
                    <a href="<?php echo \Uri::create('auth/register'); ?>">Crear una cuenta</a>
                </div>
            </div>
        </div>
    </div>
</div>
