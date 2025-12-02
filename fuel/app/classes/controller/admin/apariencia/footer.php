<?php

/**
 * ADMIN/APARIENCIA/FOOTER
 *
 * CONTROLADOR PRINCIPAL DEL FOOTER
 * MANEJA DATOS GENERALES (logos, dirección, contacto, horarios, redes sociales, texto de atención)
 */
class Controller_Admin_Apariencia_Footer extends Controller_Admin
{
    /**
     * INDEX
     * LISTA LOS FOOTERS CONFIGURADOS
     */
    public function action_index()
    {
        $data['footers'] = Model_Appearance_Footer::query()
            ->order_by('id', 'desc')
            ->get();

        $this->template->title = "Apariencia - Footer";
        $this->template->content = View::forge('admin/apariencia/footer/index', $data);
    }

    /**
 * ACTIVAR FOOTER
 */
public function action_activar($id = null)
{
    if (!$id || !$footer = Model_Appearance_Footer::find($id)) {
        Session::set_flash('error', 'Footer no encontrado.');
        Response::redirect('admin/apariencia/footer');
    }

    // Desactivar todos
    \DB::update('appearance_footer')
        ->set(['status' => 0, 'updated_at' => time()])
        ->execute();

    // Activar este
    $footer->status = 1;
    $footer->updated_at = time();

    if ($footer->save()) {
        Session::set_flash('success', 'Footer activado correctamente.');
    } else {
        Session::set_flash('error', 'No se pudo activar el footer.');
    }

    Response::redirect('admin/apariencia/footer');
}


/**
 * AGREGAR FOOTER
 */
public function action_agregar()
{
    if (Input::method() == 'POST') {

        // Configuración de subida de logos
        $upload_config = [
            'path'          => DOCROOT.'assets/uploads/footer/',
            'randomize'     => false,
            'ext_whitelist' => ['jpg','jpeg','png'],
        ];
        Upload::process($upload_config);

        $logo_main = null;
        $logo_secondary = null;

        if (Upload::is_valid()) {
            Upload::save();
            foreach (Upload::get_files() as $file) {
                if ($file['field'] == 'logo_main') {
                    $logo_main = 'uploads/footer/'.$file['saved_as'];
                }
                if ($file['field'] == 'logo_secondary') {
                    $logo_secondary = 'uploads/footer/'.$file['saved_as'];
                }
            }
        }

        $footer = Model_Appearance_Footer::forge([
            'logo_main'             => $logo_main,
            'logo_secondary'        => $logo_secondary,
            'customer_service'      => Input::post('customer_service'),
            'address'               => Input::post('address'),
            'phone'                 => Input::post('phone'),
            'email'                 => Input::post('email'),
            'office_hours_week'     => Input::post('office_hours_week'),
            'office_hours_weekend'  => Input::post('office_hours_weekend'),
            'facebook'              => Input::post('facebook'),
            'instagram'             => Input::post('instagram'),
            'linkedin'              => Input::post('linkedin'),
            'youtube'               => Input::post('youtube'),
            'twitter'               => Input::post('twitter'),
            'tiktok'                => Input::post('tiktok'),
            'whatsapp'              => Input::post('whatsapp'),
            'telegram'              => Input::post('telegram'),
            'pinterest'             => Input::post('pinterest'),
            'snapchat'              => Input::post('snapchat'),
            'status'                => Input::post('status', 0), // por defecto inactivo
            'created_at'            => time(),
            'updated_at'            => time(),
        ]);

        if ($footer->save()) {
            Session::set_flash('success', 'Footer agregado correctamente.');
            Response::redirect('admin/apariencia/footer');
        } else {
            Session::set_flash('error', 'No se pudo agregar el footer.');
        }
    }

    $this->template->title   = "Agregar Footer";
    $this->template->content = View::forge('admin/apariencia/footer/agregar');
}



    /**
     * EDITAR FOOTER
     */
    public function action_editar($id = null)
{
    $footer = Model_Appearance_Footer::find($id);

    if (!$footer) {
        Session::set_flash('error', 'Footer no encontrado.');
        Response::redirect('admin/apariencia/footer');
    }

    if (Input::method() == 'POST') {

    // Manejo de logos
    $upload_config = [
        'path' => DOCROOT.'assets/uploads/footer/',
        'randomize' => false,
        'ext_whitelist' => ['jpg','jpeg','png'],
    ];
    Upload::process($upload_config);

    if (Upload::is_valid()) {
        Upload::save();
        foreach (Upload::get_files() as $file) {
            if ($file['field'] == 'logo_main') {
                $footer->logo_main = 'uploads/footer/'.$file['saved_as'];
            }
            if ($file['field'] == 'logo_secondary') {
                $footer->logo_secondary = 'uploads/footer/'.$file['saved_as'];
            }
        }
    }

    $footer->customer_service     = Input::post('customer_service');
    $footer->address              = Input::post('address');
    $footer->phone                = Input::post('phone');
    $footer->email                = Input::post('email');
    $footer->office_hours_week    = Input::post('office_hours_week');
    $footer->office_hours_weekend = Input::post('office_hours_weekend');
    $footer->facebook             = Input::post('facebook');
    $footer->instagram            = Input::post('instagram');
    $footer->linkedin             = Input::post('linkedin');
    $footer->youtube              = Input::post('youtube');
    $footer->twitter              = Input::post('twitter');
    $footer->tiktok               = Input::post('tiktok');
    $footer->whatsapp             = Input::post('whatsapp');
    $footer->telegram             = Input::post('telegram');
    $footer->pinterest            = Input::post('pinterest');
    $footer->snapchat             = Input::post('snapchat');
    $footer->status               = Input::post('status', 1);
    $footer->updated_at           = time();

    if ($footer->save()) {
        Session::set_flash('success', 'Footer actualizado correctamente.');
        Response::redirect('admin/apariencia/footer');
    } else {
        Session::set_flash('error', 'No se pudo actualizar el footer.');
    }
}


    $data['footer'] = $footer;

    $this->template->title = "Editar Footer";
    $this->template->content = View::forge('admin/apariencia/footer/editar', $data);
}


    /**
     * INFO FOOTER
     */
    public function action_info($id = null)
{
    is_null($id) and Response::redirect('admin/apariencia/footer');

    if ( ! $footer = Model_Appearance_Footer::find($id)) {
        Session::set_flash('error', 'No se encontró el registro de Footer.');
        Response::redirect('admin/apariencia/footer');
    }

    // LINKS (mapa de sitio y legales)
    $links = Model_Appearance_Footer_Link::query()
        ->where('status', 1)
        ->order_by('type', 'asc')
        ->order_by('sort_order', 'asc')
        ->get();

    // BADGES (distintivos)
    $badges = Model_Appearance_Footer_Badge::query()
        ->where('status', 1)
        ->order_by('sort_order', 'asc')
        ->get();

    $data['footer'] = $footer;
    $data['links']  = $links;
    $data['badges'] = $badges;

    $this->template->title   = "Footer";
    $this->template->content = View::forge('admin/apariencia/footer/info', $data);
}

}
