<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 100%;
            padding: 60px 40px;
            text-align: center;
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .icon-container {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 20px rgba(102, 126, 234, 0);
            }
        }
        
        .icon-container i {
            font-size: 60px;
            color: white;
        }
        
        h1 {
            color: #2d3748;
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 10px;
        }
        
        .category {
            color: #667eea;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 30px;
        }
        
        .status-badge {
            display: inline-block;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            margin-bottom: 30px;
            font-size: 14px;
            letter-spacing: 0.5px;
        }
        
        .description {
            color: #4a5568;
            font-size: 18px;
            line-height: 1.8;
            margin-bottom: 40px;
        }
        
        .features {
            background: #f7fafc;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 40px;
            text-align: left;
        }
        
        .features h3 {
            color: #2d3748;
            font-size: 20px;
            margin: 0 0 20px;
            text-align: center;
        }
        
        .features ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .features li {
            color: #4a5568;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
        }
        
        .features li:last-child {
            border-bottom: none;
        }
        
        .features li i {
            color: #667eea;
            margin-right: 15px;
            font-size: 18px;
            width: 20px;
        }
        
        .timeline {
            background: #edf2f7;
            border-left: 4px solid #667eea;
            padding: 20px 25px;
            border-radius: 10px;
            text-align: left;
            margin-bottom: 30px;
        }
        
        .timeline h4 {
            color: #2d3748;
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 15px;
        }
        
        .timeline-item {
            color: #4a5568;
            padding: 8px 0;
            display: flex;
            align-items: center;
        }
        
        .timeline-item i {
            color: #667eea;
            margin-right: 10px;
        }
        
        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-secondary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        .progress-container {
            background: #e2e8f0;
            border-radius: 10px;
            height: 8px;
            margin: 30px 0;
            overflow: hidden;
        }
        
        .progress-bar {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            height: 100%;
            width: 25%;
            border-radius: 10px;
            animation: progressAnimation 2s ease-out;
        }
        
        @keyframes progressAnimation {
            from {
                width: 0%;
            }
            to {
                width: 25%;
            }
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .info-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }
        
        .info-card i {
            color: #667eea;
            font-size: 30px;
            margin-bottom: 10px;
        }
        
        .info-card h4 {
            color: #2d3748;
            font-size: 14px;
            margin: 10px 0 5px;
            font-weight: 600;
        }
        
        .info-card p {
            color: #718096;
            font-size: 13px;
            margin: 0;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 40px 20px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-container">
            <i class="<?php echo $module_icon; ?>"></i>
        </div>
        
        <h1><?php echo $module_name; ?></h1>
        
        <div class="category">
            <i class="fas fa-folder"></i> <?php echo $module_category; ?>
        </div>
        
        <div class="status-badge">
            <i class="fas fa-code"></i> MÓDULO EN DESARROLLO
        </div>
        
        <div class="description">
            Este módulo está actualmente en fase de desarrollo activo. Nuestro equipo de ingeniería 
            está trabajando para crear una experiencia completa y profesional que cumpla con los 
            más altos estándares de calidad.
        </div>
        
        <div class="progress-container">
            <div class="progress-bar"></div>
        </div>
        
        <div class="features">
            <h3><i class="fas fa-star"></i> Características Planificadas</h3>
            <ul>
                <li>
                    <i class="fas fa-check-circle"></i>
                    Interfaz moderna e intuitiva con diseño responsive
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    Operaciones CRUD completas (Crear, Leer, Actualizar, Eliminar)
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    Sistema de permisos y roles de usuario
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    Exportación a Excel, PDF y otros formatos
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    Búsqueda avanzada y filtros personalizables
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    Integración completa con otros módulos del sistema
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    API REST para integraciones externas
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    Auditoría y registro de cambios
                </li>
            </ul>
        </div>
        
        <div class="timeline">
            <h4><i class="fas fa-clock"></i> Proceso de Desarrollo</h4>
            <div class="timeline-item">
                <i class="fas fa-circle"></i>
                <span><strong>Fase 1:</strong> Análisis de requerimientos y diseño</span>
            </div>
            <div class="timeline-item">
                <i class="fas fa-circle"></i>
                <span><strong>Fase 2:</strong> Desarrollo del backend y base de datos</span>
            </div>
            <div class="timeline-item">
                <i class="fas fa-circle"></i>
                <span><strong>Fase 3:</strong> Implementación de la interfaz de usuario</span>
            </div>
            <div class="timeline-item">
                <i class="fas fa-circle"></i>
                <span><strong>Fase 4:</strong> Pruebas de calidad y corrección de errores</span>
            </div>
            <div class="timeline-item">
                <i class="fas fa-circle"></i>
                <span><strong>Fase 5:</strong> Documentación y capacitación</span>
            </div>
        </div>
        
        <div class="info-grid">
            <div class="info-card">
                <i class="fas fa-shield-alt"></i>
                <h4>Seguro</h4>
                <p>Cumple con estándares de seguridad</p>
            </div>
            <div class="info-card">
                <i class="fas fa-mobile-alt"></i>
                <h4>Responsive</h4>
                <p>Funciona en todos los dispositivos</p>
            </div>
            <div class="info-card">
                <i class="fas fa-rocket"></i>
                <h4>Rápido</h4>
                <p>Optimizado para rendimiento</p>
            </div>
        </div>
        
        <div class="actions">
            <a href="/admin" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Volver al Dashboard
            </a>
            <a href="/admin/modules" class="btn btn-secondary">
                <i class="fas fa-th"></i>
                Ver Todos los Módulos
            </a>
        </div>
    </div>
</body>
</html>
