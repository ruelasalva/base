<?php

class Controller_Admin_Helpdesk_Activity extends Controller_Admin
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
        if(!Auth::member(100))
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
    * MUESTRA UNA LISTADO DE REGISTROS
    *
    * @access  public
    * @return  Void
    */
    public function action_index($search = '')
    {
        # SE INICIALIZAN LAS VARIABLES
        $data            = array();
        $activities_info = array();
        $per_page        = 100;

        # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
        $activities = Model_Activitys_Num::query();


        # SI HAY UNA BUSQUEDA
        if($search != '')
        {
            # SE ALMACENA LA BUSQUEDA ORIGINAL
            $original_search = $search;

            # SE LIMPIA LA CADENA DE BUSQUEDA
            $search = str_replace('+', ' ', rawurldecode($search));

            # SE REEMPLAZA LOS ESPACIOS POR PORCENTAJES
            $search = str_replace(' ', '%', $search);

            # SE AGREGA LA CLAUSULA
            $activities = $activities->where(DB::expr("CONCAT(`t0`.`act_num`)"), 'like', '%'.$search.'%');
        }

        # SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
        $config = array(
            'name'           => 'admin',
            'pagination_url' => Uri::current(),
            'total_items'    => $activities->count(),
            'per_page'       => $per_page,
            'uri_segment'    => 'pagina',
        );

        # SE CREA LA INSTANCIA DE LA PAGINACION
        $pagination = Pagination::forge('activities', $config);

        # SE EJECUTA EL QUERY
        $activities = $activities->order_by('id', 'desc')
        ->rows_limit($pagination->per_page)
        ->rows_offset($pagination->offset)
        ->get();

        # SI SE OBTIENE INFORMACION
        if(!empty($activities))
        {
            # SE RECORRE ELEMENTO POR ELEMENTO
            foreach($activities as $activity)
            {

                // BUSCAR LA PRIMERA ACTIVIDAD ASOCIADA PARA OBTENER EL EMPLEADO
                $first_activity = reset($activity->activities); // OBTIENE LA PRIMERA ACTIVIDAD RELACIONADA
                $agent_name = $first_activity && isset($first_activity->employee) ? $first_activity->employee->name. ' '.$first_activity->employee->last_name : 'Desconocido';

                # SE ALMACENA LA INFORMACION
                $activities_info[] = array(
                    'id'        => $activity->id,
                    'act_num'   => $activity->act_num,
                    'agent'     => $agent_name,
                    'date'      => date('d/m/Y', $activity->date),
                    'completed' => $activity->completed
                );
            }
        }

        # SE ALMACENA LA INFORMACION PARA LA VISTA
        $data['activities'] = $activities_info;
        $data['search']     = str_replace('%', ' ', $search);
        $data['pagination'] = $pagination->render();

        # CARGAR LA VISTA
        $this->template->title   = 'Actividades Diarias';
        $this->template->content = View::forge('admin/helpdesk/activity/index', $data, false);
    }


    /**
    * BUSCAR
    *
    * REDIRECCIONA A LA URL DE BUSCAR REGISTROS
    *
    * @access  public
    * @return  Void
    */
    public function action_buscar()
    {
        # SI SE UTILIZO EL METODO POST
        if(Input::method() == 'POST')
        {
            # SE OBTIENEN LOS VALORES
            $data = array(
                'search' => ($_POST['search'] != '') ? $_POST['search'] : '',
            );

            # SE CREA LA VALIDACION DE LOS CAMPOS
            $val = Validation::forge('search');
            $val->add_callable('Rules');
            $val->add_field('search', 'search', 'max_length[100]');

            # SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
            if($val->run($data))
            {
                # SE REMPLAZAN ALGUNOS CARACTERES
                $search = str_replace(' ', '+', $val->validated('search'));
                $search = str_replace('*', '', $search);

                # SE ALMACENA LA CADENA DE BUSQUEDA
                $search = ($val->validated('search') != '') ? $search : '';

                # SE REDIRECCIONA A BUSCAR
                Response::redirect('admin/helpdesk/activity/index/'.$search);
            }
            else
            {
                # SE REDIRECCIONA AL USUARIO
                Response::redirect('admin/helpdesk/activity');
            }
        }
        else
        {
            # SE REDIRECCIONA AL USUARIO
            Response::redirect('admin/helpdesk/activity');
        }
    }


    /**
    * INFO
    *
    * MUESTRA UN REGISTRO A LA BASE DE DATOS
    *
    * @access  public
    * @return  Void
    */
    public function action_info($act_num = null)
    {
        # SE INICIALIZAN LAS VARIABLES
        $data            = array();
        $activities_info = array();

        # SI NO EXISTE EL ACT_NUM
        if(!$act_num)
        {
            # SE ESTABLECE EL MENSAJE DE ERROR
            Session::set_flash('error', 'No se proporcionó un identificador válido.');

            # SE REDIRECCIONA AL USUARIO
            Response::redirect('admin/helpdesk/activity');
        }

        # SE BUSCA INFORMACION A TRAVES DEL MODELO
        $activity_num = Model_Activitys_Num::query()
        ->where('act_num', $act_num)
        ->get_one();

        # SI SE OBTIENE INFORMACION
        if(!empty($activity_num))
        {
            # SE ALMACENA LA INFORMACION PARA LA VISTA
            $data['completed']   = $activity_num->completed;
            $data['global_date'] = date('d/m/Y', $activity_num->date);

            # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
            $activities = Model_Activity::query()
            ->where('act_num', $act_num)
            ->order_by('created_at', 'asc')
            ->get();

            # SI SE OBTIENE INFORMACION
            if(!empty($activities))
            {
                # SE RECORRE ELEMENTO POR ELEMENTO
                foreach($activities as $activity)
                {
                    # SE ALMACENA LA INFORMACION
                    $activities_info[] = array(
                        'id'          => $activity->id,
                        'customer'    => $activity->customer,
                        'company'     => $activity->company,
                        'user_id'     => $activity->user_id,
                        'employee'    => $activity->employee_id,
                        'hour'        => $activity->hour,
                        'invoice'      => ($activity->invoice == 0) ? 'No' : 'Sí',
                        'foreing'     => ($activity->foreing == 0) ? 'No' : 'Sí',
                        'time'        => $activity->time->name,
                        'contact'     => $activity->contact->name,
                        'category'    => $activity->category->name,
                        'status'      => $activity->status->name,
                        'type'        => $activity->type->name,
                        'total'       => $activity->total,
                        'comments'    => $activity->comments,
                        'global_date' => $activity->global_date
                    );
                }
            }
        }
        else
        {
            # SE ESTABLECE EL MENSAJE DE ERROR
            Session::set_flash('error', 'No se encontraron actividades para este identificador.');

            # SE REDIRECCIONA AL USUARIO
            Response::redirect('admin/helpdesk/activity');
        }

        # SE ALMACENA LA INFORMACION PARA LA VISTA
        $data['act_num']    = $act_num;
        $data['activities'] = $activities_info;

        # SE CARGA LA VISTA
        $this->template->title   = 'Ver Actividades';
        $this->template->content = View::forge('admin/helpdesk/activity/info', $data);
    }
}
