<!-- Page Header -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0"><i class="fas fa-cog me-2"></i>Configuración del Sitio</h2>
                <p class="text-body-secondary">Administra todos los aspectos de tu sitio web</p>
            </div>
            <div>
                <a href="<?php echo Uri::create('admin/configuracion/templates'); ?>" class="btn btn-outline-primary">
                    <i class="fas fa-palette me-1"></i>Cambiar Template
                </a>
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
            <button class="nav-link active" id="general-tab" data-coreui-toggle="tab" data-coreui-target="#general" type="button">
                <i class="fas fa-home me-1"></i>General
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="seo-tab" data-coreui-toggle="tab" data-coreui-target="#seo" type="button">
                <i class="fas fa-search me-1"></i>SEO
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="analytics-tab" data-coreui-toggle="tab" data-coreui-target="#analytics" type="button">
                <i class="fas fa-chart-line me-1"></i>Analytics
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="smtp-tab" data-coreui-toggle="tab" data-coreui-target="#smtp" type="button">
                <i class="fas fa-envelope me-1"></i>SMTP
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="social-tab" data-coreui-toggle="tab" data-coreui-target="#social" type="button">
                <i class="fas fa-share-alt me-1"></i>Redes Sociales
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="security-tab" data-coreui-toggle="tab" data-coreui-target="#security" type="button">
                <i class="fas fa-shield-alt me-1"></i>Seguridad
            </button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="configTabsContent">
        
        <!-- GENERAL TAB -->
        <div class="tab-pane fade show active" id="general" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Configuración General</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre del Sitio</label>
                            <input type="text" class="form-control" name="site_name" 
                                   value="<?php echo htmlspecialchars($config['site_name'] ?? ''); ?>" 
                                   placeholder="Mi Empresa">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">URL del Logo</label>
                            <input type="text" class="form-control" name="site_logo" 
                                   value="<?php echo htmlspecialchars($config['site_logo'] ?? ''); ?>" 
                                   placeholder="/assets/img/logo.png">
                            <small class="text-muted">Logo principal del sitio (recomendado: 200x60px)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">URL del Favicon</label>
                            <input type="text" class="form-control" name="site_favicon" 
                                   value="<?php echo htmlspecialchars($config['site_favicon'] ?? ''); ?>" 
                                   placeholder="/assets/img/favicon.ico">
                            <small class="text-muted">Icono que aparece en la pestaña del navegador</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO TAB -->
        <div class="tab-pane fade" id="seo" role="tabpanel">
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
