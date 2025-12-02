<?php

/**
* CONTROLADOR ADMIN_DATOS_TRANSFERENCIA
*
* @package  app
* @extends  Controller_Admin
*/
class Controller_Admin_Datos_Transferencia extends Controller_Admin
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
    * PERMITE EDITAR LA INFORMACION DE LOS DATOS DE TRANSFERENCIA
    *
    * @access  public
    * @return  Void
    */
    public function action_index()
    {
        # SE INICIALIZAN LAS VARIABLES
		$data             = array();
		$classes          = array();
		$fields           = array('content');

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
		$transfer_data = Model_Transfer_Datum::query()
		->where('id', 1)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($transfer_data))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['content'] = $transfer_data->info;
        }
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('transfer_data');
			$val->add_callable('Rules');
			$val->add_field('content', 'contenido', 'required|min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE ESTEBLECE LA NUEVA INFORMACION
				$transfer_data->info  = $val->validated('content');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($transfer_data->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información de los <b>Datos de transferencia</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/datos_transferencia');
				}
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

				# SI LA DIVISA ES PESOS
				if(Input::post('badge') == 0)
				{
					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes['exchange_rate']['form-group']   = '';
					$classes['exchange_rate']['form-control'] = '';
				}
			}
		}

        # SE ALMACENA LA INFORMACION PARA LA VISTA
        $data['classes'] = $classes;

        # SE CARGA LA VISTA
        $this->template->title   = 'Datos de transferencia';
        $this->template->content = View::forge('admin/datos_transferencia/index', $data, false);
    }
}
