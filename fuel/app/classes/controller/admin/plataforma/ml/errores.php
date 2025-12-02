<?php

class Controller_Admin_Plataforma_Ml_Errores extends Controller_Admin
{
    public function action_index()
    {
        $config_id = (int) Input::get('config_id', 0);

        $configs = Model_Plataforma_Ml_Configuration::query()
            ->order_by('name', 'asc')
            ->get();

        if (!$configs) {
            Session::set_flash('error', 'No hay cuentas ML configuradas.');
            return Response::redirect('admin/plataforma/ml');
        }

        if ($config_id <= 0) {
            $config = reset($configs);
        } else {
            $config = Model_Plataforma_Ml_Configuration::find($config_id);
        }

        $errors_query = Model_Plataforma_Ml_Error::query()
            ->where('configuration_id', $config->id)
            ->order_by('id', 'desc');

        $pagination = Pagination::forge('ml_errors', array(
            'pagination_url' => Uri::create('admin/plataforma/ml/errores', [], [
                'config_id' => $config->id,
            ]),
            'total_items' => $errors_query->count(),
            'per_page'    => 50,
            'uri_segment' => 'pagina',
        ));

        $errors = $errors_query
            ->rows_offset($pagination->offset)
            ->rows_limit($pagination->per_page)
            ->get();

        $this->template->title = 'Errores Mercado Libre';

        $this->template->content = View::forge(
            'admin/plataformas/ml/errores/index',
            [
                'configs'    => $configs,
                'config'     => $config,
                'errors'     => $errors,
                'pagination' => $pagination,
            ],
            false
        );
    }
}
