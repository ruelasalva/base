<?php
/**
 * Configuración del Sitio - Variables Configurables
 * 
 * Este archivo contiene todas las variables personalizables del sitio
 * que se pueden modificar durante la instalación o desde el panel de administración
 */

return array(
    
    /**
     * Información General del Sistema
     */
    'site_name'        => 'ERP Multi-Tenant',
    'site_description' => 'Sistema de Gestión Empresarial Multi-Tenant',
    'site_version'     => '1.0.0',
    'site_author'      => 'Tu Empresa',
    
    /**
     * Colores del Tema (Gradientes Hero Section)
     */
    'theme' => array(
        'primary_color'     => '#667eea',
        'secondary_color'   => '#764ba2',
        'gradient_start'    => '#667eea',
        'gradient_end'      => '#764ba2',
        'gradient_angle'    => '135deg',
    ),
    
    /**
     * Textos del Hero Section
     */
    'hero' => array(
        'title'       => 'ERP Multi-Tenant',
        'subtitle'    => 'Sistema de Gestión Empresarial',
        'description' => 'Gestión integral de múltiples empresas desde una única plataforma',
        'button_text' => 'Comenzar',
    ),
    
    /**
     * Módulos del Sistema
     */
    'modules' => array(
        'admin' => array(
            'name'        => 'Administración',
            'description' => 'Panel de control y configuración del sistema',
            'icon'        => 'glyphicon-cog',
            'color'       => 'success',
            'enabled'     => true,
        ),
        'providers' => array(
            'name'        => 'Proveedores',
            'description' => 'Gestión de proveedores y compras',
            'icon'        => 'glyphicon-briefcase',
            'color'       => 'info',
            'enabled'     => true,
        ),
        'partners' => array(
            'name'        => 'Socios',
            'description' => 'Administración de socios y participaciones',
            'icon'        => 'glyphicon-user',
            'color'       => 'warning',
            'enabled'     => true,
        ),
        'sellers' => array(
            'name'        => 'Vendedores',
            'description' => 'Gestión de vendedores y comisiones',
            'icon'        => 'glyphicon-tags',
            'color'       => 'primary',
            'enabled'     => true,
        ),
        'clients' => array(
            'name'        => 'Clientes',
            'description' => 'Gestión de clientes y ventas',
            'icon'        => 'glyphicon-shopping-cart',
            'color'       => 'default',
            'enabled'     => true,
        ),
        'tools' => array(
            'name'        => 'Herramientas',
            'description' => 'Utilidades del sistema',
            'icon'        => 'glyphicon-wrench',
            'color'       => 'danger',
            'enabled'     => true,
        ),
    ),
    
    /**
     * Frontend Público
     */
    'frontend' => array(
        'store' => array(
            'name'        => 'Tienda',
            'description' => 'Catálogo de productos y ventas online',
            'icon'        => 'glyphicon-shopping-cart',
            'enabled'     => true,
        ),
        'landing' => array(
            'name'        => 'Landing',
            'description' => 'Página de inicio y marketing',
            'icon'        => 'glyphicon-globe',
            'enabled'     => true,
        ),
    ),
    
    /**
     * Arquitectura del Sistema
     */
    'architecture' => array(
        'mvc' => array(
            'title'       => 'MVC',
            'description' => 'Arquitectura Modelo-Vista-Controlador',
            'icon'        => 'glyphicon-th-large',
        ),
        'hmvc' => array(
            'title'       => 'HMVC',
            'description' => 'Módulos jerárquicos independientes',
            'icon'        => 'glyphicon-th',
        ),
        'packages' => array(
            'title'       => 'Packages',
            'description' => 'Sistema de paquetes reutilizables',
            'icon'        => 'glyphicon-folder-open',
        ),
    ),
    
    /**
     * Instalación
     */
    'installation' => array(
        'required'        => true,
        'alert_message'   => '¡Atención! El sistema requiere instalación inicial',
        'alert_details'   => 'Necesitas configurar la base de datos y los módulos antes de comenzar.',
        'button_text'     => 'Instalar Ahora',
    ),
    
    /**
     * Footer
     */
    'footer' => array(
        'text'      => '© 2024 ERP Multi-Tenant. Todos los derechos reservados.',
        'powered_by' => 'Powered by FuelPHP',
    ),
    
);
