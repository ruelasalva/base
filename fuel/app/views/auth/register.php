<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0 text-center">
                        <span class="glyphicon glyphicon-user"></span>
                        Crear Cuenta
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
                            <br><br>
                            <a href="<?php echo \Uri::create('auth/login'); ?>" class="btn btn-success btn-sm">Ir a Iniciar Sesión</a>
                        </div>
                    <?php else: ?>

                    <form method="post" action="<?php echo \Uri::create('auth/register'); ?>">
                        <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="first_name">Nombre *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="<?php echo htmlspecialchars(\Input::post('first_name', '')); ?>" 
                                           placeholder="Nombre" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="last_name">Apellido *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="<?php echo htmlspecialchars(\Input::post('last_name', '')); ?>" 
                                           placeholder="Apellido" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="username">Nombre de Usuario *</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars(\Input::post('username', '')); ?>" 
                                   placeholder="Usuario (mínimo 3 caracteres)" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars(\Input::post('email', '')); ?>" 
                                   placeholder="correo@ejemplo.com" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password">Contraseña *</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Mínimo 6 caracteres" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password_confirm">Confirmar Contraseña *</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                                   placeholder="Repita la contraseña" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-block">
                                Crear Cuenta
                            </button>
                        </div>
                    </form>

                    <?php endif; ?>
                </div>
                <div class="card-footer text-center">
                    ¿Ya tiene cuenta? <a href="<?php echo \Uri::create('auth/login'); ?>">Iniciar Sesión</a>
                </div>
            </div>
        </div>
    </div>
</div>
