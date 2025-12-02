
<?php

/**
* CONTROLADOR ADMIN_SURVEY
*
* @package  app
* @extends  Controller_Admin
*/
class Controller_Admin_Crm_Survey extends Controller_Admin
{
    /**
	* RESULTS
	*
	* MUESTRA UNA LISTADO DE REGISTROS
	*
	* @access  public
	* @return  Void
	*/
    public function action_results()
    {
        # SE INICIALIZAN LAS VARIABLES
        $data           = array();
        $surveys_info   = array();
        $per_page       = 100;
        
        # OBTIENE TODOS LOS REGISTROS DE SURVEY DE LA BASE DE DATOS
        $surveys = Model_Survey::query()
        ->where('id', '>=', 0);

        # SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
        $config = array(
            'name'           => 'admin',
            'pagination_url' => Uri::current(),
            'total_items'    => $surveys->count(),
            'per_page'       => $per_page,
            'uri_segment'    => 'pagina',
        );

        # SE CREA LA INSTANCIA DE LA PAGINACION
        $pagination = Pagination::forge('surveys', $config);

        # SE EJECUTA EL QUERY
        $surveys = $surveys->order_by('id', 'desc')
        ->rows_limit($pagination->per_page)
        ->rows_offset($pagination->offset)
        ->get();

        # SI SE OBTIENE INFORMACION
        if(!empty($surveys))
        {
            # SE RECORRE ELEMENTO POR ELEMENTO
            foreach($surveys as $survey)
            {
                # SE ALMACENA LA INFORMACION
                $surveys_info[] = array(
                    'id'            => $survey->id,
                    'session_id'    => $survey->session_id,
                    'ip'            => $survey->ip,
                    'survey_code'   => $survey->survey_code,
                    'name'          => $survey->name,
                    'email'         => $survey->email,
                    'ratingventa'   => $survey->ratingventa,
                    'ratingsurtido' => $survey->ratingsurtido,
                    'ratingentrega' => $survey->ratingentrega,
                    'recomienda'    => $survey->recomienda,
                    'comment'       => $survey->comment,
                    'created_at'    => date('d/m/Y - H:i', $survey->created_at)
                );
            }
        }

        # SE ALMACENA LA INFORMACION PARA LA VISTA
        $data['surveys']    = $surveys_info;
        $data['pagination'] = $pagination->render();

        # RENDERIZA LA VISTA DE RESULTADOS Y PASA LOS DATOS DE LOS SURVERYS
        $this->template->title       = 'Resultados del Formulario de Satisfacción';
        $this->template->description = 'Distribuidora Sajor - ';
        $this->template->content     = View::forge('admin/crm/survey/results', $data, false);
    }

}
