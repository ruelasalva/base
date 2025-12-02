
<?php

/**
* CONTROLADOR ADMIN_RASTREO
*
* @package  app
* @extends  Controller_Admin
*/
class Controller_Admin_Crm_Rastreo extends Controller_Admin
{
    /**
	* INDEX
	*
	* MUESTRA UNA LISTADO DE REGISTROS
	*
	* @access  public
	* @return  Void
	*/
    public function action_index()
    {
        # MUESTRA LA VISTA
        $this->template->title       = 'Busqueda de Guias locales';
        $this->template->description = 'Distribuidora Sajor - ';
        $this->template->content     = View::forge('admin/crm/rastreo/index');
    }
}
