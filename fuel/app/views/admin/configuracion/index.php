<!-- Page Header -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0"><i class="fas fa-cog me-2"></i>Configuración del Sistema</h2>
                <p class="text-body-secondary">Administra todos los aspectos del sistema</p>
            </div>
            <div>
                <!-- Estadísticas rápidas -->
                <div class="badge bg-info">
                    <?php echo $stats['total']; ?> Configuraciones
                </div>
                <div class="badge bg-success">
                    <?php echo $stats['categories']; ?> Categorías
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form -->
<form method="POST" action="<?php echo Uri::create('admin/configuracion/save'); ?>" id="configForm">
    <?php echo Form::csrf(); ?>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" id="configTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab == 'general' ? 'active' : ''; ?>" href="<?php echo Uri::create('admin/configuracion/general'); ?>">
                <i class="fas fa-home me-1"></i>General
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab == 'email' ? 'active' : ''; ?>" href="<?php echo Uri::create('admin/configuracion/email'); ?>">
                <i class="fas fa-envelope me-1"></i>Email/SMTP
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab == 'facturacion' ? 'active' : ''; ?>" href="<?php echo Uri::create('admin/configuracion/facturacion'); ?>">
                <i class="fas fa-file-invoice-dollar me-1"></i>Facturación
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab == 'notificaciones' ? 'active' : ''; ?>" href="<?php echo Uri::create('admin/configuracion/notificaciones'); ?>">
                <i class="fas fa-bell me-1"></i>Notificaciones
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab == 'seguridad' ? 'active' : ''; ?>" href="<?php echo Uri::create('admin/configuracion/seguridad'); ?>">
                <i class="fas fa-shield-alt me-1"></i>Seguridad
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" href="<?php echo Uri::create('admin/configuracion/templates'); ?>">
                <i class="fas fa-palette me-1"></i>Templates
            </a>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="configTabsContent">
        
        <!-- Resumen General -->
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Bienvenido al Centro de Configuración</strong><br>
                    Desde aquí puedes administrar todas las configuraciones del sistema organizadas por categorías.
                    Selecciona una pestaña arriba para comenzar.
                </div>
            </div>
        </div>

        <!-- Cards de Categorías -->
        <div class="row g-4 mt-2">
            <div class="col-md-4">
                <div class="card h-100 hover-shadow">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-sliders-h text-primary"></i> General
                        </h5>
                        <p class="card-text">
                            Configuración básica del sistema: nombre, logo, zona horaria, formato de fechas.
                        </p>
                        <a href="<?php echo Uri::create('admin/configuracion/general'); ?>" class="btn btn-sm btn-primary">
                            Configurar <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 hover-shadow">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-envelope text-info"></i> Email
                        </h5>
                        <p class="card-text">
                            Configuración de servidor SMTP para envío de correos electrónicos.
                        </p>
                        <a href="<?php echo Uri::create('admin/configuracion/email'); ?>" class="btn btn-sm btn-info">
                            Configurar <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 hover-shadow">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-file-invoice-dollar text-success"></i> Facturación
                        </h5>
                        <p class="card-text">
                            Parámetros del módulo de proveedores: términos de pago, validación SAT, días festivos.
                        </p>
                        <a href="<?php echo Uri::create('admin/configuracion/facturacion'); ?>" class="btn btn-sm btn-success">
                            Configurar <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 hover-shadow">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-bell text-warning"></i> Notificaciones
                        </h5>
                        <p class="card-text">
                            Configuración de canales de notificación: email, SMS, push, horarios silenciosos.
                        </p>
                        <a href="<?php echo Uri::create('admin/configuracion/notificaciones'); ?>" class="btn btn-sm btn-warning">
                            Configurar <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 hover-shadow">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-shield-alt text-danger"></i> Seguridad
                        </h5>
                        <p class="card-text">
                            Política de contraseñas, intentos de login, reCAPTCHA, autenticación de dos factores.
                        </p>
                        <a href="<?php echo Uri::create('admin/configuracion/seguridad'); ?>" class="btn btn-sm btn-danger">
                            Configurar <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 hover-shadow">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-palette text-purple"></i> Templates
                        </h5>
                        <p class="card-text">
                            Administración de plantillas visuales y temas del sistema.
                        </p>
                        <a href="<?php echo Uri::create('admin/configuracion/templates'); ?>" class="btn btn-sm btn-secondary">
                            Configurar <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Última actualización -->
        <?php if (!empty($stats['last_updated'])): ?>
        <div class="row mt-4">
            <div class="col-md-12">
                <p class="text-muted text-center">
                    <i class="fas fa-clock"></i> Última actualización: <?php echo date('d/m/Y H:i:s', strtotime($stats['last_updated'])); ?>
                </p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.card-title i {
    font-size: 1.5rem;
    margin-right: 0.5rem;
}
</style>
            <div class="card">
                <div class="card-header">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="seo_enabled" value="1" 
                               <?php echo (!empty($config['seo_enabled'])) ? 'checked' : ''; ?>>
                        <label class="form-check-label"><strong>Activar SEO</strong></label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Importante:</strong> El SEO solo se aplica a las páginas públicas (Landing, Blog, Tienda). 
                        El admin NO debe indexarse por seguridad.
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Título SEO</label>
                            <input type="text" class="form-control" name="seo_title" 
                                   value="<?php echo htmlspecialchars($config['seo_title'] ?? ''); ?>" 
                                   placeholder="Mi Empresa - Líder en...">
                            <small class="text-muted">Máximo 60 caracteres</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripción SEO</label>
                            <textarea class="form-control" name="seo_description" rows="3" 
                                      placeholder="Descripción breve de tu sitio para motores de búsqueda"><?php echo htmlspecialchars($config['seo_description'] ?? ''); ?></textarea>
                            <small class="text-muted">Máximo 160 caracteres</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Palabras Clave (Keywords)</label>
                            <input type="text" class="form-control" name="seo_keywords" 
                                   value="<?php echo htmlspecialchars($config['seo_keywords'] ?? ''); ?>" 
                                   placeholder="empresa, productos, servicios">
                            <small class="text-muted">Separadas por comas</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Imagen Open Graph (OG:Image)</label>
                            <input type="text" class="form-control" name="seo_og_image" 
                                   value="<?php echo htmlspecialchars($config['seo_og_image'] ?? ''); ?>" 
                                   placeholder="/assets/img/og-image.jpg">
                            <small class="text-muted">Imagen para compartir en redes sociales (1200x630px)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ANALYTICS TAB -->
        <div class="tab-pane fade" id="analytics" role="tabpanel">
            <div class="row g-4">
                <!-- Google Analytics 4 -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="ga_enabled" value="1" 
                                       <?php echo (!empty($config['ga_enabled'])) ? 'checked' : ''; ?>>
                                <label class="form-check-label"><strong>Google Analytics 4</strong></label>
                            </div>
                        </div>
                        <div class="card-body">
                            <label class="form-label">Tracking ID (G-XXXXXXXXXX)</label>
                            <input type="text" class="form-control" name="ga_tracking_id" 
                                   value="<?php echo htmlspecialchars($config['ga_tracking_id'] ?? ''); ?>" 
                                   placeholder="G-XXXXXXXXXX">
                            <small class="text-muted">Solo para landing pages públicas</small>
                        </div>
                    </div>
                </div>

                <!-- Google Tag Manager -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="gtm_enabled" value="1" 
                                       <?php echo (!empty($config['gtm_enabled'])) ? 'checked' : ''; ?>>
                                <label class="form-check-label"><strong>Google Tag Manager</strong></label>
                            </div>
                        </div>
                        <div class="card-body">
                            <label class="form-label">Container ID (GTM-XXXXXXX)</label>
                            <input type="text" class="form-control" name="gtm_container_id" 
                                   value="<?php echo htmlspecialchars($config['gtm_container_id'] ?? ''); ?>" 
                                   placeholder="GTM-XXXXXXX">
                        </div>
                    </div>
                </div>

                <!-- Facebook Pixel -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="fb_pixel_enabled" value="1" 
                                       <?php echo (!empty($config['fb_pixel_enabled'])) ? 'checked' : ''; ?>>
                                <label class="form-check-label"><strong>Facebook Pixel</strong></label>
                            </div>
                        </div>
                        <div class="card-body">
                            <label class="form-label">Pixel ID</label>
                            <input type="text" class="form-control" name="fb_pixel_id" 
                                   value="<?php echo htmlspecialchars($config['fb_pixel_id'] ?? ''); ?>" 
                                   placeholder="XXXXXXXXXXXXXXX">
                            <small class="text-muted">Para campañas de Facebook Ads</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SMTP TAB -->
        <div class="tab-pane fade" id="smtp" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="smtp_enabled" value="1" 
                               <?php echo (!empty($config['smtp_enabled'])) ? 'checked' : ''; ?>>
                        <label class="form-check-label"><strong>Activar SMTP</strong></label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Host SMTP</label>
                            <input type="text" class="form-control" name="smtp_host" 
                                   value="<?php echo htmlspecialchars($config['smtp_host'] ?? ''); ?>" 
                                   placeholder="smtp.gmail.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Puerto</label>
                            <input type="number" class="form-control" name="smtp_port" 
                                   value="<?php echo htmlspecialchars($config['smtp_port'] ?? '587'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Usuario SMTP</label>
                            <input type="text" class="form-control" name="smtp_user" 
                                   value="<?php echo htmlspecialchars($config['smtp_user'] ?? ''); ?>" 
                                   placeholder="tu@email.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="smtp_password" 
                                   value="<?php echo htmlspecialchars($config['smtp_password'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Encriptación</label>
                            <select class="form-select" name="smtp_encryption">
                                <option value="tls" <?php echo ($config['smtp_encryption'] ?? 'tls') == 'tls' ? 'selected' : ''; ?>>TLS</option>
                                <option value="ssl" <?php echo ($config['smtp_encryption'] ?? '') == 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                <option value="none" <?php echo ($config['smtp_encryption'] ?? '') == 'none' ? 'selected' : ''; ?>>Ninguna</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email "From"</label>
                            <input type="email" class="form-control" name="smtp_from_email" 
                                   value="<?php echo htmlspecialchars($config['smtp_from_email'] ?? ''); ?>" 
                                   placeholder="noreply@tusitio.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nombre "From"</label>
                            <input type="text" class="form-control" name="smtp_from_name" 
                                   value="<?php echo htmlspecialchars($config['smtp_from_name'] ?? ''); ?>" 
                                   placeholder="Mi Empresa">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SOCIAL MEDIA TAB -->
        <div class="tab-pane fade" id="social" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Enlaces de Redes Sociales</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><i class="fab fa-facebook me-2"></i>Facebook</label>
                            <input type="url" class="form-control" name="social_facebook" 
                                   value="<?php echo htmlspecialchars($config['social_facebook'] ?? ''); ?>" 
                                   placeholder="https://facebook.com/tupagina">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="fab fa-twitter me-2"></i>Twitter / X</label>
                            <input type="url" class="form-control" name="social_twitter" 
                                   value="<?php echo htmlspecialchars($config['social_twitter'] ?? ''); ?>" 
                                   placeholder="https://twitter.com/tuusuario">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="fab fa-instagram me-2"></i>Instagram</label>
                            <input type="url" class="form-control" name="social_instagram" 
                                   value="<?php echo htmlspecialchars($config['social_instagram'] ?? ''); ?>" 
                                   placeholder="https://instagram.com/tuperfil">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="fab fa-linkedin me-2"></i>LinkedIn</label>
                            <input type="url" class="form-control" name="social_linkedin" 
                                   value="<?php echo htmlspecialchars($config['social_linkedin'] ?? ''); ?>" 
                                   placeholder="https://linkedin.com/company/tuempresa">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECURITY TAB -->
        <div class="tab-pane fade" id="security" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="recaptcha_enabled" value="1" 
                               <?php echo (!empty($config['recaptcha_enabled'])) ? 'checked' : ''; ?>>
                        <label class="form-check-label"><strong>Activar reCAPTCHA v3</strong></label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        reCAPTCHA se aplica en formularios públicos (contacto, registro). 
                        El login de admin usa su propio sistema de seguridad.
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Site Key</label>
                            <input type="text" class="form-control" name="recaptcha_site_key" 
                                   value="<?php echo htmlspecialchars($config['recaptcha_site_key'] ?? ''); ?>" 
                                   placeholder="6Le...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Secret Key</label>
                            <input type="text" class="form-control" name="recaptcha_secret_key" 
                                   value="<?php echo htmlspecialchars($config['recaptcha_secret_key'] ?? ''); ?>" 
                                   placeholder="6Le...">
                        </div>
                        <div class="col-12">
                            <small class="text-muted">
                                Obtén tus keys en: <a href="https://www.google.com/recaptcha/admin" target="_blank">https://www.google.com/recaptcha/admin</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <?php if ($can_edit): ?>
    <div class="row mt-4">
        <div class="col-12">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save me-2"></i>Guardar Configuración
            </button>
        </div>
    </div>
    <?php endif; ?>
</form>
