<?php

/**
* CONTROLADOR ADMIN_PROCESADORES_PAGO
*
* @package  app
* @extends  Controller_Admin
*/
class Controller_Admin_Procesadores_Pago extends Controller_Admin
{
    /**
    * BEFORE
    *
    * @return Void
    */
    public function before()
    {
        # REQUERIDA PARA EL TEMPLATING
        parent::before();

        # SI EL USUARIO NO TIENE PERMISOS
        if(!Auth::member(100) && !Auth::member(50))
        {
            # SE ESTABLECE EL MENSAJE DE ERROR
            Session::set_flash('error', 'No tienes los permisos para acceder a esta sección.');

            # SE REDIRECCIONA AL USUARIO
            Response::redirect('admin');
        }
    }


    /**
    * INDEX
    *
    * PERMITE ACTIVAR O DESCATIVAR LOS PROCESADORES DE PAGO
    *
    * @access  public
    * @return  Void
    */
    public function action_index()
    {
        # SE INICIALIZAN LAS VARIABLES
        $data    = array();
        $classes = array();
        $fields  = array('bbva', 'transfer');

        # SE RECORRE CAMPO POR CAMPO
        foreach($fields as $field)
        {
            # SE CREAN LAS CLASES DEL CAMPO
            $classes[$field] = array (
                'form-group'   => null,
                'form-control' => null,
            );
        }

        # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$payments_processors = Model_Payments_Processor::query()
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($payments_processors))
		{
            # SE RECORRE ELEMENTO POR ELEMENTO
            foreach($payments_processors as $payments_processor)
            {
                # DEPENDIENDO DEL ID
                switch($payments_processor->id)
                {
                    # BBVA
                    case 1:
                        # SE ALMACENA LA INFORMACION PARA LA VISTA
                        $data['bbva'] = $payments_processor->status;
                    break;

                    # TRANSFERENCIA/DEPOSITO
                    case 2:
                        # SE ALMACENA LA INFORMACION PARA LA VISTA
                        $data['transfer'] = $payments_processor->status;
                    break;
                }
            }
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/categorias');
		}

        # SI SE UTILIZA EL METODO POST
        if(Input::method() == 'POST')
        {
            # SE CREA LA VALIDACION DE LOS CAMPOS
            $val = Validation::forge('bbva');
            $val->add_callable('Rules');
            $val->add_field('bbva', 'BBVA', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');
            $val->add_field('transfer', 'transferencia/depósito', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

            # SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
            if($val->run())
            {
                # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
        		$payments_processors = Model_Payments_Processor::query()
        		->get();

                # SI SE OBTIENE INFORMACION
        		if(!empty($payments_processors))
        		{
                    # SE RECORRE ELEMENTO POR ELEMENTO
                    foreach($payments_processors as $payments_processor)
                    {
                        # DEPENDIENDO DEL ID
                        switch($payments_processor->id)
                        {
                            # BBVA
                            case 1:
                                # SE ESTABLECE LA NUEVA INFORMACION
                                $payments_processor->status = $val->validated('bbva');
                            break;

                            # TRANSFERENCIA/DEPOSITO
                            case 2:
                                # SE ESTABLECE LA NUEVA INFORMACION
                                $payments_processor->status = $val->validated('transfer');
                            break;
                        }

                        # SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
                        $payments_processor->save();
                    }
                }

                # SE ESTABLECE EL MENSAJE DE EXITO
                Session::set_flash('success', 'Se actualizó la información de los <b>procedadores de pago</b> correctamente.');

                # SE REDIRECCIONA AL USUARIO
                Response::redirect('admin/procesadores_pago');
            }
            else
            {
                # SE ESTABLECE EL MENSAJE DE ERROR
                Session::set_flash('error', 'Encontramos algunos errores en el formulario, por favor verifícalo.');

                # SE ALMACENA LOS ERRORES DETECTADOS
                $data['errors'] = $val->error();

                # SE RECORRE CLASE POR CLASE
                foreach($classes as $name => $class)
                {
                    # SE ESTABLECE EL VALOR DE LAS CLASES
                    $classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
                    $classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';

                    # SE ALMACENA LA INFORMACION PARA LA VISTA
                    $data[$name] = Input::post($name);
                }
            }
        }

        # SE ALMACENA LA INFORMACION PARA LA VISTA
        $data['classes'] = $classes;

        # SE CARGA LA VISTA
        $this->template->title   = 'Procesadores de pago';
        $this->template->content = View::forge('admin/procesadores_pago/index', $data);
    }
}
