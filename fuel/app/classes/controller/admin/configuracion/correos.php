<?php

/**
 * CONTROLADOR ADMIN_CONFIGURACION_CORREOS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Configuracion_Correos extends Controller_Admin
{
    /**
     * BEFORE
     *
     * @return void
     */
    public function before()
    {
        parent::before();

        # Validación de login
        if (!Auth::check())
        {
            Session::set_flash('error', 'Debes iniciar sesión.');
            Response::redirect('admin/login');
        }

        # Validación de permiso
        if (!Helper_Permission::can('config_correos', 'view'))
        {
            Session::set_flash('error', 'No tienes permiso para ver configuración de correos.');
            Response::redirect('admin');
        }
    }

    /**
     * INDEX
     *
     * Pantalla principal del módulo de correos
     *
     * @access  public
     * @return  void
     */
    public function action_index()
    {
        # Variables para la vista
        $data = array(
            'title' => 'Configuración de Correos',
            'options' => array(
                array(
                    'url'   => 'admin/configuracion/correos/roles',
                    'icon'  => 'fa-solid fa-users-gear text-blue',
                    'label' => 'Roles de Correo',
                    'desc'  => 'Definir remitente, reply-to y destinatarios por rol.'
                ),
                array(
                    'url'   => 'admin/configuracion/correos/templates',
                    'icon'  => 'fa-solid fa-file-lines text-green',
                    'label' => 'Plantillas de Correo',
                    'desc'  => 'Definir estructura y asunto de los correos.'
                ),
            ),
        );

        # Cargar vista
        $this->template->title   = 'Correos';
        $this->template->content = View::forge('admin/configuracion/correos/index', $data);
    }
}
