<?php
/**
 * Topbar Layout Component
 * Barra superior del sistema
 */
$user = Auth::get_user_info();
?>
<header class="header header-sticky mb-4">
    <div class="container-fluid">
        <button class="header-toggler d-lg-none" type="button" onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="header-nav ms-auto">
            <!-- Notificaciones -->
            <div class="nav-item dropdown">
                <a class="nav-link" href="#" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <span class="badge badge-pill bg-danger">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <div class="dropdown-header text-center">
                        <strong>Notificaciones</strong>
                    </div>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-info-circle text-info"></i> Nueva actualización disponible
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-center" href="<?php echo Uri::create('admin/notifications'); ?>">
                        Ver todas
                    </a>
                </div>
            </div>

            <!-- Usuario -->
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i>
                    <span class="d-none d-md-inline"><?php echo $user['username']; ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <div class="dropdown-header text-center">
                        <strong><?php echo $user['username']; ?></strong>
                        <br>
                        <small class="text-muted"><?php echo $user['email']; ?></small>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo Uri::create('admin/profile'); ?>">
                        <i class="fas fa-user"></i> Mi Perfil
                    </a>
                    <a class="dropdown-item" href="<?php echo Uri::create('admin/configuracion'); ?>">
                        <i class="fas fa-cog"></i> Configuración
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo Uri::create('admin/logout'); ?>">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
.header {
    background: #fff;
    border-bottom: 1px solid #d8dbe0;
    padding: 0;
    position: sticky;
    top: 0;
    z-index: 999;
}

.header .container-fluid {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
}

.header-toggler {
    padding: 0.5rem;
    border: none;
    background: transparent;
    font-size: 1.25rem;
    color: #768192;
}

.header-nav {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-nav .nav-link {
    color: #768192;
    text-decoration: none;
    position: relative;
    padding: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.header-nav .nav-link:hover {
    color: #2c3e50;
}

.header-nav .badge {
    position: absolute;
    top: 0;
    right: 0;
    font-size: 0.625rem;
    padding: 0.25em 0.4em;
}

.dropdown-menu {
    min-width: 200px;
}

.dropdown-header {
    padding: 0.75rem 1rem;
    background-color: #f8f9fa;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}
</style>
