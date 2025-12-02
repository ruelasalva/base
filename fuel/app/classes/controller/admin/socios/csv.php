<?php
/**
* CONTROLADOR CSV
*
* @package  app
* @extends  Controller_Base
*/
class Controller_Admin_Socios_CSV extends Controller_Admin
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
     * EXPORTAR DATOS GENERALES Y FISCALES DE SOCIOS
     *
     * @access  public
     * @return  Void
     */
    public function action_exportar_generales()
    {
        // Se inicializa el arreglo que contendrá los datos
        $rows = [];

        // Se obtiene la información de los socios
        $partners = Model_Partner::query()
            ->related('user')
            ->related('type')
            ->related('customer')
            ->related('employee')
            ->where('deleted', 0) // si aplicas borrado lógico
            ->get();

        // Se recorren los socios
        foreach ($partners as $p)
        {
            $tax_data = Model_Partners_Tax_Datum::query()
                ->related('state')
                ->related('cfdi')
                ->related('sat_tax_regime')
                ->related('payment_method')
                ->where('partner_id', $p->id)
                ->get_one();

            $status = unserialize($p->user->profile_fields);

            // Se crea la fila
            $rows[] = [
                'ID'               => $p->id,
                'Código SAP'       => $p->code_sap,
                'Nombre'           => $p->name,
                'Email'            => $p->user->email ?? '',
                'Bloqueado'        => (isset($status['banned']) && $status['banned']) ? 'Sí' : 'No',
                'Cliente Web'      => $p->customer->name ?? '',
                'Vendedor Asignado'=> $p->employee->name ?? '',
                'Lista de Precios' => $p->type->name ?? '',

                'Razón Social'     => $tax_data->business_name ?? '',
                'RFC'              => $tax_data->rfc ?? '',
                'Calle'            => $tax_data->street ?? '',
                'Número'           => $tax_data->number ?? '',
                'Interior'         => $tax_data->internal_number ?? '',
                'Colonia'          => $tax_data->colony ?? '',
                'Código Postal'    => $tax_data->zipcode ?? '',
                'Ciudad'           => $tax_data->city ?? '',
                'Municipio'        => $tax_data->municipality ?? '',
                'Estado'           => $tax_data->state->name ?? '',
                'CFDI'             => $tax_data->cfdi->name ?? '',
                'Régimen Fiscal'   => $tax_data->sat_tax_regime->name ?? '',
                'Método de Pago'   => $tax_data->payment_method->name ?? ''
            ];
        }

        // ENCABEZADOS PARA EXPORTACIÓN
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="Datos_generales_socios.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // SE ABRE LA SALIDA PARA ESCRIBIR EL CSV
        $output = fopen('php://output', 'w');

        // UTF-8 BOM para Excel
        fputs($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // ENCABEZADOS FIJOS
        $headers = [
            'ID', 'Código SAP', 'Nombre', 'Email', 'Bloqueado',
            'Cliente Web', 'Vendedor Asignado', 'Lista de Precios',
            'Razón Social', 'RFC', 'Calle', 'Número Exterior', 'Número Interior',
            'Colonia', 'Código Postal', 'Ciudad','Municipio', 'Estado',
            'Uso de CFDI', 'Régimen Fiscal', 'Método de Pago'
        ];
        fputcsv($output, $headers);

        // FILAS
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }



    /**
     * EXPORTAR DATOS ENTREGAS
     *
     * @access  public
     * @return  Void
     */
    public function action_exportar_entregas()
    {
        // Se obtiene la información de las direcciones de entrega con relación al socio
        $entregas = Model_Partners_Delivery::query()
            ->related('partner')
            ->related('partner.user')
            ->related('state')
            ->where('deleted', 0) // Asegura que no se muestren registros eliminados lógicamente
            ->get();

        // Encabezados fijos del CSV
        $csv = [];
        $csv[] = [
            'Código SAP',
            'Razón Social',
            'Identificador',
            'Calle',
            'Número',
            'Interior',
            'Colonia',
            'Código Postal',
            'Ciudad',
            'Municipio',
            'Estado',
            'Horario de Recepción',
            'Notas',
            'Última Modificación'
        ];

        // Recorremos las entregas para formar las filas
        foreach ($entregas as $e)
        {
            $csv[] = [
                $e->partner->code_sap ?? '',
                $e->partner->name ?? '',
                $e->iddelivery,
                $e->street,
                $e->number,
                $e->internal_number,
                $e->colony,
                $e->zipcode,
                $e->city,
                $e->municipality,
                $e->state->name ?? '',
                $e->reception_hours,
                $e->delivery_notes,
                (!empty($e->updated_at)) ? date('d/m/Y H:i', $e->updated_at) : ''
            ];
        }

        // ENCABEZADOS PARA EXPORTACIÓN
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="socios_direcciones_entrega.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // ABRIMOS SALIDA PARA ESCRIBIR EL CSV
        $output = fopen('php://output', 'w');

        // UTF-8 BOM para compatibilidad con Excel
        fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($output, $headers); // Escribe encabezados

        // ESCRIBIMOS TODAS LAS FILAS
        foreach ($csv as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }


    /**
     * EXPORTAR DATOS CONTACTOS
     *
     * @access  public
     * @return  Void
     */
    public function action_exportar_contactos()
    {
        // Se obtiene la información de los contactos con relación al socio
        $contactos = Model_Partners_Contact::query()
            ->related('partner')
            ->related('partner.user')
            ->where('deleted', 0) // Excluye los contactos eliminados lógicamente
            ->get();

        // Encabezados fijos del CSV
        $csv = [];
        $csv[] = [
            'Código SAP',
            'Razón Social',
            'Identificador de Contacto',
            'Nombre',
            'Apellido',
            'Teléfono',
            'Correo Electrónico',
            'Última Modificación'
        ];

        // Recorremos los contactos para formar las filas
        foreach ($contactos as $c)
        {
            $csv[] = [
                $c->partner->code_sap ?? '',
                $c->partner->name ?? '',
                $c->idcontact,
                $c->name,
                $c->last_name,
                $c->phone,
                $c->email,
                (!empty($c->updated_at)) ? date('d/m/Y H:i', $c->updated_at) : ''
            ];
        }

        // ENCABEZADOS PARA EXPORTACIÓN
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="socios_contactos.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // ABRIMOS SALIDA PARA ESCRIBIR EL CSV
        $output = fopen('php://output', 'w');

        // UTF-8 BOM para compatibilidad con Excel
        fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // ESCRIBIMOS TODAS LAS FILAS
        foreach ($csv as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }


    /**
     * EXPORTAR DATOS POR SOCIO
     *
     * @access  public
     * @return  Void
     */
    public function action_exportar_info($partner_id = 0)
    {
        // Validar ID
        if ($partner_id == 0 || !is_numeric($partner_id)) {
            Session::set_flash('error', 'ID inválido.');
            Response::redirect('admin/socios/info');
        }

        // Obtener socio con relaciones básicas
        $partner = Model_Partner::query()
            ->related('user')
            ->related('type')
            ->related('customer')
            ->related('employee')
            ->where('id', $partner_id)
            ->get_one();

        if (!$partner || !$partner->user) {
            Session::set_flash('error', 'Socio no encontrado.');
            Response::redirect('admin/socios');
        }

        // Deserializar campos
        $status = unserialize($partner->user->profile_fields);

        // Obtener datos fiscales (1)
        $tax_data = Model_Partners_Tax_Datum::query()
            ->related('cfdi')
            ->related('sat_tax_regime')
            ->related('payment_method')
            ->related('state')
            ->where('partner_id', $partner_id)
            ->get_one();

        // Obtener entregas (múltiples)
        $deliveries = Model_Partners_Delivery::query()
            ->related('state')
            ->where('partner_id', $partner_id)
            ->where('deleted', 0)
            ->get();

        // Obtener contactos (múltiples)
        $contacts = Model_Partners_Contact::query()
            ->where('partner_id', $partner_id)
            ->where('deleted', 0)
            ->get();

        // Iniciar CSV
        $csv = [];

        // Bloque: Datos Generales
        $csv[] = ['INFORMACION DEL SOCIO'];
        $csv[] = ['Código SAP.:', $partner->code_sap];
        $csv[] = ['Nombre o Razón Social.:', $partner->name];
        $csv[] = ['Email.:', $partner->user->email ?? ''];
        $csv[] = ['Bloqueado.:', (isset($status['banned']) && $status['banned']) ? 'Sí' : 'No'];
        $csv[] = [''];

        // Bloque: Fiscales
        if ($tax_data) {
            $csv[] = ['DATOS FISCALES'];
            $csv[] = ['Razón Social.:', $tax_data->business_name];
            $csv[] = ['RFC.:', $tax_data->rfc];
            $csv[] = ['Calle.:', $tax_data->street];
            $csv[] = ['Número.:', $tax_data->number];
            $csv[] = ['Interior.:', $tax_data->internal_number];
            $csv[] = ['Colonia.:', $tax_data->colony];
            $csv[] = ['Código Postal.:', $tax_data->zipcode];
            $csv[] = ['Ciudad.:', $tax_data->city];
            $csv[] = ['Estado.:', $tax_data->state->name ?? ''];
            $csv[] = ['CFDI.:', $tax_data->cfdi->name ?? ''];
            $csv[] = ['Régimen Fiscal.:', $tax_data->sat_tax_regime->name ?? ''];
            $csv[] = ['Método de Pago.:', $tax_data->payment_method->name ?? ''];
            $csv[] = [''];
        }

        // Bloque: Direcciones de entrega
        if (!empty($deliveries)) {
            foreach ($deliveries as $i => $d) {
                $csv[] = ['DIRECCION DE ENTREGA #' . ($i + 1)];
                $csv[] = ['Identificador.:', $d->iddelivery];
                $csv[] = ['Calle.:', $d->street];
                $csv[] = ['Número.:', $d->number];
                $csv[] = ['Interior.:', $d->internal_number];
                $csv[] = ['Colonia.:', $d->colony];
                $csv[] = ['Código Postal.:', $d->zipcode];
                $csv[] = ['Ciudad.:', $d->city];
                $csv[] = ['Municipio.:', $d->municipality];
                $csv[] = ['Estado.:', $d->state->name ?? ''];
                $csv[] = ['Horario de recepción.:', $d->reception_hours];
                $csv[] = ['Notas.:', $d->delivery_notes];
                $csv[] = [''];
            }
        }

        // Bloque: Contactos
        if (!empty($contacts)) {
            foreach ($contacts as $i => $c) {
                $csv[] = ['CONTACTO #' . ($i + 1)];
                $csv[] = ['Identificador.:', $c->idcontact];
                $csv[] = ['Nombre........:', $c->name];
                $csv[] = ['Apellido......:', $c->last_name];
                $csv[] = ['Teléfono......:', $c->phone];
                $csv[] = ['Correo........:', $c->email];
                $csv[] = [''];
            }
        }

        // ENCABEZADOS PARA EXPORTACIÓN
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="socio_info_'.$partner->code_sap.'.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // ABRIMOS SALIDA PARA ESCRIBIR EL CSV
        $output = fopen('php://output', 'w');

        // UTF-8 BOM para Excel
        fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Escribir todas las filas
        foreach ($csv as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * EXPORTAR CON TODOS LOS DATOS
     *
     * @access  public
     * @return  Void
     */
    public function action_exportar_todo()
    {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="socios_completo.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // BOM para Excel
        fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // TITULOS DEL EXCEL
        $headers = [
            'ID', 'Código SAP', 'Nombre', 'Email', 'Bloqueado',
            'Cliente Web', 'Vendedor Asignado', 'Lista de Precios',
            'Razón Social', 'RFC', 'Calle', 'Número Exterior', 'Número Interior',
            'Colonia', 'Código Postal', 'Ciudad', 'Municipio', 'Estado',
            'Uso de CFDI', 'Régimen Fiscal', 'Método de Pago',
            'Identificador Entrega', 'Entrega Calle', 'Entrega Número',
            'Entrega Interior', 'Entrega Colonia', 'Entrega Código Postal',
            'Entrega Ciudad', 'Entrega Municipio', 'Entrega Estado',
            'Entrega Horario', 'Entrega Notas', 'Entrega Última Modificación',
            'ID Contacto', 'Nombre Contacto', 'Apellido Contacto',
            'Teléfono Contacto', 'Correo Contacto', 'Contacto Última Modificación'
        ];
        fputcsv($output, $headers);
        //SOCIO
        $partners = Model_Partner::query()
            ->related('user')
            ->related('type')
            ->related('customer')
            ->related('employee')
            ->where('deleted', 0)
            ->get();

        foreach ($partners as $p)
        {
            //DATOS FISCALES
            $tax_data = Model_Partners_Tax_Datum::query()
                ->related('state')
                ->related('cfdi')
                ->related('sat_tax_regime')
                ->related('payment_method')
                ->where('partner_id', $p->id)
                ->get_one();

            //DATOS DE ENTREGA
            $delivery = Model_Partners_Delivery::query()
                ->related('state')
                ->where('partner_id', $p->id)
                ->where('deleted', 0)
                ->get_one();

            //CONTACTOS
            $contact = Model_Partners_Contact::query()
                ->where('partner_id', $p->id)
                ->where('deleted', 0)
                ->get_one();

            $status = array();
            if (!empty($p->user->profile_fields)) {
                $status = @unserialize($p->user->profile_fields);
                if (!is_array($status)) {
                    $status = array();
                }
            }

            //GENERALES CON FISCALES
            fputcsv($output, [
                $p->id,
                $p->code_sap,
                $p->name,
                $p->user->email ?? '',
                (isset($status['banned']) && $status['banned']) ? 'Sí' : 'No',
                $p->customer->name ?? '',
                $p->employee->name ?? '',
                $p->type->name ?? '',
                $tax_data->business_name ?? '',
                $tax_data->rfc ?? '',
                $tax_data->street ?? '',
                $tax_data->number ?? '',
                $tax_data->internal_number ?? '',
                $tax_data->colony ?? '',
                $tax_data->zipcode ?? '',
                $tax_data->city ?? '',
                $tax_data->municipality ?? '',
                $tax_data->state->name ?? '',
                $tax_data->cfdi->name ?? '',
                $tax_data->sat_tax_regime->name ?? '',
                $tax_data->payment_method->name ?? '',

                //ENTREGAS
                $delivery->iddelivery ?? '',
                $delivery->street ?? '',
                $delivery->number ?? '',
                $delivery->internal_number ?? '',
                $delivery->colony ?? '',
                $delivery->zipcode ?? '',
                $delivery->city ?? '',
                $delivery->municipality ?? '',
                $delivery->state->name ?? '',
                $delivery->reception_hours ?? '',
                $delivery->delivery_notes ?? '',
                (!empty($delivery->updated_at)) ? date('d/m/Y H:i', $delivery->updated_at) : '',

                //CONTACTOS
                $contact->idcontact ?? '',
                $contact->name ?? '',
                $contact->last_name ?? '',
                $contact->phone ?? '',
                $contact->email ?? '',
                (!empty($contact->updated_at)) ? date('d/m/Y H:i', $contact->updated_at) : ''
            ]);
        }

        //SALIDA DEL ARCHIVO 
        fclose($output);
        exit;
    }


    
    /**
     * EXPORTAR CON TODOS LOS ACTULIZADOS
     *
     * @access  public
     * @return  Void
     */
    public function action_exportar_actualizados()
    {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="socios_actualizados.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM

        // Encabezados...
        $headers = [
            'ID', 'Código SAP', 'Nombre', 'Email', 'Bloqueado',
            'Cliente Web', 'Vendedor Asignado', 'Lista de Precios',
            'Razón Social', 'RFC', 'Calle', 'Número Exterior', 'Número Interior',
            'Colonia', 'Código Postal', 'Ciudad', 'Municipio', 'Estado',
            'Uso de CFDI', 'Régimen Fiscal', 'Método de Pago',
            'Identificador Entrega', 'Entrega Calle', 'Entrega Número',
            'Entrega Interior', 'Entrega Colonia', 'Entrega Código Postal',
            'Entrega Ciudad', 'Entrega Municipio', 'Entrega Estado',
            'Entrega Horario', 'Entrega Notas', 'Entrega Última Modificación',
            'ID Contacto', 'Nombre Contacto', 'Apellido Contacto',
            'Teléfono Contacto', 'Correo Contacto', 'Contacto Última Modificación'
        ];
        fputcsv($output, $headers);

        // 1. Obtener IDs de socios con domicilios
        $delivery_ids = \DB::select('partner_id')
        ->from('partners_delivery') 
        ->where('deleted', '=', 0)
        ->execute()
        ->as_array();

            // 2. Obtener IDs de socios con contactos
        $contact_ids = \DB::select('partner_id')
        ->from('partners_contacts') 
        ->where('deleted', '=', 0)
        ->execute()
        ->as_array();

        // 3. Unificar IDs únicos
        $ids_entrega = array_map(function($row){ return $row['partner_id']; }, $delivery_ids);
        $ids_contact = array_map(function($row){ return $row['partner_id']; }, $contact_ids);
        $socios_actualizados = array_unique(array_merge($ids_entrega, $ids_contact));

        // 4. Ahora solo buscamos esos socios
        if (empty($socios_actualizados)) {
            fclose($output);
            exit;
        }

        $partners = Model_Partner::query()
            ->related('user')
            ->related('type')
            ->related('customer')
            ->related('employee')
            ->where('deleted', 0)
            ->where('id', 'in', $socios_actualizados)
            ->get();

        foreach ($partners as $p)
        {
            // Todo igual a tu código
            $tax_data = Model_Partners_Tax_Datum::query()
                ->related('state')
                ->related('cfdi')
                ->related('sat_tax_regime')
                ->related('payment_method')
                ->where('partner_id', $p->id)
                ->get_one();

            $delivery = Model_Partners_Delivery::query()
                ->related('state')
                ->where('partner_id', $p->id)
                ->where('deleted', 0)
                ->get_one();

            $contact = Model_Partners_Contact::query()
                ->where('partner_id', $p->id)
                ->where('deleted', 0)
                ->get_one();

            $status = array();
            if (!empty($p->user->profile_fields)) {
                $status = @unserialize($p->user->profile_fields);
                if (!is_array($status)) $status = array();
            }

            fputcsv($output, [
                $p->id,
                $p->code_sap,
                $p->name,
                $p->user->email ?? '',
                (isset($status['banned']) && $status['banned']) ? 'Sí' : 'No',
                $p->customer->name ?? '',
                $p->employee->name ?? '',
                $p->type->name ?? '',
                $tax_data->business_name ?? '',
                $tax_data->rfc ?? '',
                $tax_data->street ?? '',
                $tax_data->number ?? '',
                $tax_data->internal_number ?? '',
                $tax_data->colony ?? '',
                $tax_data->zipcode ?? '',
                $tax_data->city ?? '',
                $tax_data->municipality ?? '',
                $tax_data->state->name ?? '',
                $tax_data->cfdi->name ?? '',
                $tax_data->sat_tax_regime->name ?? '',
                $tax_data->payment_method->name ?? '',
                $delivery->iddelivery ?? '',
                $delivery->street ?? '',
                $delivery->number ?? '',
                $delivery->internal_number ?? '',
                $delivery->colony ?? '',
                $delivery->zipcode ?? '',
                $delivery->city ?? '',
                $delivery->municipality ?? '',
                $delivery->state->name ?? '',
                $delivery->reception_hours ?? '',
                $delivery->delivery_notes ?? '',
                (!empty($delivery->updated_at)) ? date('d/m/Y H:i', $delivery->updated_at) : '',
                $contact->idcontact ?? '',
                $contact->name ?? '',
                $contact->last_name ?? '',
                $contact->phone ?? '',
                $contact->email ?? '',
                (!empty($contact->updated_at)) ? date('d/m/Y H:i', $contact->updated_at) : ''
            ]);
        }

        fclose($output);
        exit;
    }











}
