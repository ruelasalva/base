
<?php

/**
* CONTROLADOR ADMIN_CORTE
*
* @package  app
* @extends  Controller_Admin
*/
class Controller_Admin_Crm_Corte extends Controller_Admin
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
        $this->template->title       = 'Calcualadora para cortes';
        $this->template->description = 'Distribuidora Sajor - ';
        $this->template->content     = View::forge('admin/crm/corte/index');
    }
}
