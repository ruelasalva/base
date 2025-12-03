<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">
                <i class="fa-solid fa-key text-warning"></i>
                Configurar Credenciales SAT
            </h1>
            <p class="text-muted">Configure su RFC, contraseña y certificados para acceder al portal SAT</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <i class="fa-solid fa-shield-alt"></i> Información de Autenticación
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <!-- RFC y Contraseña Portal SAT -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <strong>1. Portal SAT (CIEC)</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">RFC *</label>
                                            <input type="text" name="rfc" class="form-control" 
                                                   value="<?= $credentials['rfc'] ?? '' ?>" 
                                                   placeholder="XAXX010101000" 
                                                   maxlength="13" 
                                                   required 
                                                   style="text-transform:uppercase">
                                            <small class="text-muted">Su RFC de 12 o 13 caracteres</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Contraseña CIEC *</label>
                                            <input type="password" name="password" class="form-control" 
                                                   placeholder="••••••••" 
                                                   required>
                                            <small class="text-muted">Contraseña del portal SAT</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CSD (Certificado de Sello Digital) -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <strong>2. CSD - Certificado de Sello Digital</strong>
                                <small class="text-muted">(Opcional - para timbrado)</small>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fa-solid fa-info-circle"></i>
                                    <strong>¿Qué es el CSD?</strong> Es el certificado que utiliza para timbrar sus facturas.
                                    Son dos archivos (.cer y .key) que descarga del portal SAT.
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Certificado (.cer)</label>
                                            <input type="file" name="csd_cer" class="form-control" accept=".cer">
                                            <?php if (!empty($credentials['csd_cer'])): ?>
                                                <small class="text-success">
                                                    <i class="fa-solid fa-check"></i> Certificado cargado
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Llave Privada (.key)</label>
                                            <input type="file" name="csd_key" class="form-control" accept=".key">
                                            <?php if (!empty($credentials['csd_key'])): ?>
                                                <small class="text-success">
                                                    <i class="fa-solid fa-check"></i> Llave cargada
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Contraseña CSD</label>
                                            <input type="password" name="csd_password" class="form-control" 
                                                   placeholder="Contraseña del .key">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FIEL (e.firma) -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <strong>3. FIEL / e.firma</strong>
                                <small class="text-muted">(Opcional - para firma electrónica avanzada)</small>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fa-solid fa-info-circle"></i>
                                    <strong>¿Qué es la FIEL?</strong> Es su Firma Electrónica Avanzada.
                                    Se utiliza para descargas masivas y trámites ante el SAT.
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Certificado e.firma (.cer)</label>
                                            <input type="file" name="fiel_cer" class="form-control" accept=".cer">
                                            <?php if (!empty($credentials['fiel_cer'])): ?>
                                                <small class="text-success">
                                                    <i class="fa-solid fa-check"></i> Certificado cargado
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Llave e.firma (.key)</label>
                                            <input type="file" name="fiel_key" class="form-control" accept=".key">
                                            <?php if (!empty($credentials['fiel_key'])): ?>
                                                <small class="text-success">
                                                    <i class="fa-solid fa-check"></i> Llave cargada
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Contraseña FIEL</label>
                                            <input type="password" name="fiel_password" class="form-control" 
                                                   placeholder="Contraseña del .key">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nota de Seguridad -->
                        <div class="alert alert-warning">
                            <i class="fa-solid fa-exclamation-triangle"></i>
                            <strong>Seguridad:</strong> 
                            Todas las contraseñas y certificados se almacenan de forma encriptada en la base de datos.
                            Nunca se almacenan en texto plano.
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= Uri::create('admin/sat') ?>" class="btn btn-secondary">
                                <i class="fa-solid fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fa-solid fa-save"></i> Guardar Credenciales
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Ayuda -->
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <i class="fa-solid fa-question-circle"></i> ¿Cómo obtener mis certificados?
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>CSD (Certificado de Sello Digital)</h6>
                            <ol class="small">
                                <li>Ingresar al portal SAT</li>
                                <li>Trámites > Certificado de Sello Digital</li>
                                <li>Descargar certificado (.cer) y llave (.key)</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6>FIEL / e.firma</h6>
                            <ol class="small">
                                <li>Acudir a un SAT con identificación oficial</li>
                                <li>Solicitar Firma Electrónica (FIEL)</li>
                                <li>Descargar certificados en formato .cer y .key</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
