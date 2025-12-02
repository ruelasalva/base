<?php
/**
 * CONTROLADOR ADMIN - NOTAS DE CRÉDITO
 */
class Controller_Admin_Compras_Notasdecredito extends Controller_Admin
{
    /**
 * INDEX
 * LISTADO DE NOTAS DE CRÉDITO (ADMIN)
 */
public function action_index($search = '')
{
    \Log::debug("[ADMIN-NOTASDECREDITO][INDEX] INICIO");

    // CONSULTA BASE
    $query = Model_Providers_Creditnote::query()
        ->related('provider')
        ->where('deleted', 0);

    // FILTRO DE BÚSQUEDA
    if (!empty($search)) {
        $search = str_replace('+', ' ', rawurldecode($search));
        $search = str_replace(' ', '%', $search);
        $query->where_open()
              ->where('uuid', 'like', "%{$search}%")
              ->or_where('folio', 'like', "%{$search}%")
              ->or_where('status', 'like', "%{$search}%")
              ->or_where('provider.name', 'like', "%{$search}%")
              ->where_close();
    }

    // PAGINACIÓN
    $config = [
        'pagination_url' => \Uri::current(),
        'total_items'    => $query->count(),
        'per_page'       => 25,
        'uri_segment'    => 'pagina',
        'show_first'     => true,
        'show_last'      => true,
    ];
    $pagination = \Pagination::forge('admin_notasdecredito', $config);

    // OBTENER REGISTROS PAGINADOS
    $notas = $query
        ->order_by('created_at', 'desc')
        ->rows_limit($pagination->per_page)
        ->rows_offset($pagination->offset)
        ->get();

    // PROCESAR DATOS PARA LA VISTA
    $notas_info = [];
    foreach ($notas as $nota) {
        $status_badge = Helper_Purchases::render_status('creditnote', $nota->status);

        $notas_info[] = [
            'id'         => $nota->id,
            'provider'   => $nota->provider ? $nota->provider->name : '-',
            'uuid'       => $nota->uuid,
            'total'      => '$' . number_format($nota->total, 2, '.', ','),
            'status'     => $status_badge,
            'created_at' => !empty($nota->created_at)
                ? date('d/m/Y', $nota->created_at)
                : '---',
        ];
    }

    // DATOS PARA LA VISTA
    $data = [
        'notas'      => $notas_info,
        'search'     => str_replace('%', ' ', $search),
        'pagination' => $pagination->render(),
    ];

    $this->template->title   = "Notas de Crédito (Admin)";
    $this->template->content = \View::forge('admin/compras/notasdecredito/index', $data, false);
}


    /**
     * INFO
     * DETALLE DE NOTA DE CRÉDITO
     */
    public function action_info($id = null)
    {
        \Log::debug("[ADMIN-NOTASDECREDITO][INFO] id={$id}");
        is_null($id) and Response::redirect('admin/compras/notasdecredito');

        $nota = Model_Providers_Creditnote::find($id, array('related' => array('provider')));
        if (!$nota) {
            Session::set_flash('error','Nota de crédito no encontrada.');
            Response::redirect('admin/compras/notasdecredito');
        }

        $relaciones = Model_Providers_Creditnote_Bill::query()
            ->where('creditnote_id', $nota->id)
            ->related('bill')
            ->get();

        $data['nota']       = $nota;
        $data['relaciones'] = $relaciones;

        $this->template->title = "Detalle Nota de Crédito";
        $this->template->content = View::forge('admin/compras/notasdecredito/info', $data);
    }

    /**
     * CAMBIAR ESTATUS
     * ACEPTAR, CANCELAR, APLICAR, DESAPLICAR
     */
    public function action_status($id = null, $accion = null)
    {
        \Log::debug("[ADMIN-NOTASDECREDITO][STATUS] id={$id}, accion={$accion}");

        $nota = Model_Providers_Creditnote::find($id);
        if (!$nota) {
            Session::set_flash('error','Nota no encontrada.');
            Response::redirect('admin/compras/notasdecredito');
        }

        switch ($accion) {
            case 'aceptar':
                $nota->status = 1; # ACEPTADA
                $msg = 'Nota de crédito aceptada.';
                break;
            case 'rechazar':
                $nota->status = 2; # RECHAZADA
                $msg = 'Nota de crédito rechazada.';
                break;
            case 'aplicar':
                $nota->status = 3; # APLICADA
                $msg = 'Nota de crédito aplicada.';
                break;
            case 'desaplicar':
                $nota->status = 1; # VUELVE A ACEPTADA
                $msg = 'Nota de crédito desaplicada.';
                break;
            case 'cancelar':
                $nota->status = 4; # CANCELADA
                $msg = 'Nota de crédito cancelada.';
                break;
            default:
                $msg = 'Acción inválida.';
        }

        $nota->save();
        \Log::debug("[ADMIN-NOTASDECREDITO][STATUS] Nota {$nota->id} => {$nota->status}");
        Session::set_flash('success',$msg);
        Response::redirect('admin/compras/notasdecredito/info/'.$nota->id);
    }

    public function action_agregar()
{
    \Log::debug("[ADMIN-NOTASDECREDITO][AGREGAR] INICIO");

    # LISTA DE PROVEEDORES
    $providers = Model_Provider::query()
        //->where('deleted', 0)
        ->order_by('name','asc')
        ->get();

    $provider_opts = [];
    foreach($providers as $prov) {
        $provider_opts[$prov->id] = $prov->name;
    }

    $data['providers'] = $provider_opts;

    $this->template->title = "Subir Nota de Crédito (Admin)";
    $this->template->content = View::forge('admin/compras/notasdecredito/agregar', $data);
}

public function action_guardar()
{
    \Log::debug("[ADMIN-NOTASDECREDITO][GUARDAR] INICIO");

    if (Input::method() != 'POST') {
        Response::redirect('admin/compras/notasdecredito/agregar');
    }

    $provider_id  = Input::post('provider_id');
    $uuid         = Input::post('uuid');
    $serie        = Input::post('serie');
    $folio        = Input::post('folio');
    $total        = Input::post('total');
    $observations = Input::post('observations');
    $destino      = Input::post('destino');

    $xml_file = Input::file('xml_file');
    $pdf_file = Input::file('pdf_file');

    if (empty($xml_file['name'])) {
        Session::set_flash('error','Debes subir el archivo XML.');
        Response::redirect('admin/compras/notasdecredito/agregar');
    }

    $xml_name = time().'_'.$xml_file['name'];
    $xml_path = DOCROOT.'uploads/xml/'.$xml_name;
    move_uploaded_file($xml_file['tmp_name'], $xml_path);

    $pdf_name = null;
    if (!empty($pdf_file['name'])) {
        $pdf_name = time().'_'.$pdf_file['name'];
        $pdf_path = DOCROOT.'uploads/pdf/'.$pdf_name;
        move_uploaded_file($pdf_file['tmp_name'], $pdf_path);
    }

    $xml_data = Helper_Invoicexml::read($xml_path);
    if (!$xml_data || empty($xml_data['uuid'])) {
        Session::set_flash('error','El XML no es válido.');
        Response::redirect('admin/compras/notasdecredito/agregar');
    }

    # CREAR NOTA
    $nota = Model_Providers_Creditnote::forge(array(
        'provider_id'  => $provider_id,
        'uuid'         => $uuid,
        'serie'        => $serie,
        'folio'        => $folio,
        'xml_file'     => $xml_name,
        'pdf_file'     => $pdf_name,
        'total'        => $total,
        'status'       => 0,
        'observations' => $observations,
    ));
    $nota->save();

    \Log::debug("[ADMIN-NOTASDECREDITO][GUARDAR] NOTA {$nota->id} REGISTRADA PARA PROVIDER {$provider_id}");

    Session::set_flash('success','Nota de crédito registrada correctamente.');
    Response::redirect('admin/compras/notasdecredito');
}


}
