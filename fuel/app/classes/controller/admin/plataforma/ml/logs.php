<?php

class Controller_Admin_Plataforma_Ml_Logs extends Controller_Admin
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
            $config_id = $config->id;
        } else {
            $config = Model_Plataforma_Ml_Configuration::find($config_id);
            if (!$config) {
                Session::set_flash('error', 'Configuración no encontrada.');
                return Response::redirect('admin/plataforma/ml');
            }
        }

        // FILTRO POR FECHA (opcional)
        $fecha = Input::get('fecha', '');

        $logs_query = Model_Plataforma_Ml_Log::query()
            ->where('configuration_id', $config_id)
            ->order_by('id', 'desc');

        if (!empty($fecha)) {
            $inicio = strtotime($fecha . ' 00:00:00');
            $fin    = strtotime($fecha . ' 23:59:59');
            $logs_query->where('created_at', '>=', $inicio);
            $logs_query->where('created_at', '<=', $fin);
        }

        $pagination = Pagination::forge('ml_logs', array(
            'pagination_url' => Uri::create('admin/plataforma/ml/logs', array(), array(
                'config_id' => $config_id,
                'fecha'     => $fecha,
            )),
            'total_items' => $logs_query->count(),
            'per_page'    => 30,
            'uri_segment' => 'pagina',
        ));

        $logs = $logs_query
            ->rows_offset($pagination->offset)
            ->rows_limit($pagination->per_page)
            ->get();

        $this->template->title = "Logs Mercado Libre";

        $this->template->content = View::forge(
            'admin/plataformas/ml/logs/index',
            [
                'configs'    => $configs,
                'config'     => $config,
                'logs'       => $logs,
                'fecha'      => $fecha,
                'pagination' => $pagination,
            ],
            false
        );
    }
}
