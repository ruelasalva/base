<?php

/**
 * ===========================================================
 *  CONTROLADOR: PLANTILLAS DE DESCRIPCIÓN MERCADO LIBRE
 *  Ubicación: admin/plataforma/ml/plantillas
 * ===========================================================
 */
class Controller_Admin_Plataforma_Ml_Plantillas extends Controller_Admin
{
    /**
     * =======================================================
     * INDEX — Listado de plantillas
     * =======================================================
     */
    public function action_index()
    {
        $config_id = (int) Input::get('config_id', 0);

        if (!$config_id) {
            Session::set_flash('error', 'No se envió configuration_id');
            return Response::redirect('admin/plataforma/ml');
        }

        $config = Model_Plataforma_Ml_Configuration::find($config_id);

        if (!$config) {
            Session::set_flash('error', 'Configuración ML no encontrada.');
            return Response::redirect('admin/plataforma/ml');
        }

        $plantillas = Model_Plataforma_Ml_Description_Template::query()
            ->where('configuration_id', $config_id)
            ->where('deleted', 0)
            ->order_by('id', 'desc')
            ->get();

        $view = View::forge('admin/plataformas/ml/plantillas/index');
        $view->set('plantillas', $plantillas, false);
        $view->set('config', $config, false);

        $this->template->title   = "Plantillas ML — ".$config->name;
        $this->template->content = $view;
    }


    /**
     * =======================================================
     * AGREGAR
     * =======================================================
     */
    public function action_agregar()
    {
        $config_id = (int) Input::get('config_id', 0);

        if (!$config_id) {
            Session::set_flash('error', 'No se envió configuration_id.');
            return Response::redirect('admin/plataforma/ml');
        }

        // ==== TEMPLATE ====
        $view = View::forge('admin/plataformas/ml/plantillas/agregar');
        $view->set('config_id', $config_id, false);

        $this->template->title   = "Nueva Plantilla ML";
        $this->template->content = $view;
    }


    /**
     * =======================================================
     * EDITAR
     * =======================================================
     */
    public function action_editar($id = null)
    {
        $plantilla = Model_Plataforma_Ml_Description_Template::find($id);

        if (!$plantilla || $plantilla->deleted == 1) {
            Session::set_flash('error', 'Plantilla no encontrada.');
            return Response::redirect('admin/plataforma/ml');
        }

        $view = View::forge('admin/plataformas/ml/plantillas/editar');
        $view->set('plantilla', $plantilla, false);

        $this->template->title   = "Editar Plantilla ML #{$plantilla->id}";
        $this->template->content = $view;
    }


    /**
     * =======================================================
     * GUARDAR (Crear / Editar)
     * =======================================================
     */
    public function action_guardar()
    {
        if (Input::method() != 'POST') {
            Session::set_flash('error', 'Método no permitido.');
            return Response::redirect_back();
        }

        $id               = Input::post('id', null);
        $config_id        = (int) Input::post('configuration_id');
        $name             = trim(Input::post('name'));
        $description_html = Input::post('description_html');
        $is_active        = (int) Input::post('is_active', 1);

        if (!$config_id) {
            Session::set_flash('error', 'Error: Falta configuration_id.');
            return Response::redirect_back();
        }

        if ($name == '') {
            Session::set_flash('error', 'El nombre no puede estar vacío.');
            return Response::redirect_back();
        }

        // ==========================================
        // EDITAR
        // ==========================================
        if ($id) {

            $plantilla = Model_Plataforma_Ml_Description_Template::find($id);

            if (!$plantilla) {
                Session::set_flash('error', 'Plantilla no encontrada.');
                return Response::redirect_back();
            }

            $plantilla->name             = $name;
            $plantilla->description_html = $description_html;
            $plantilla->is_active        = $is_active;
            $plantilla->updated_at       = time();
            $plantilla->save();

            Session::set_flash('success', 'Plantilla actualizada correctamente.');
            return Response::redirect('admin/plataformas/ml/plantillas?config_id='.$config_id);
        }

        // ==========================================
        // CREAR
        // ==========================================
        $plantilla = Model_Plataforma_Ml_Description_Template::forge([
            'configuration_id' => $config_id,
            'name'             => $name,
            'description_html' => $description_html,
            'is_active'        => $is_active,
            'deleted'          => 0,
            'created_at'       => time(),
            'updated_at'       => time(),
        ]);

        $plantilla->save();

        Session::set_flash('success', 'Plantilla creada correctamente.');
        return Response::redirect('admin/plataformas/ml/plantillas?config_id='.$config_id);
    }


    /**
     * =======================================================
     * ELIMINAR (Borrado lógico)
     * =======================================================
     */
    public function action_eliminar($id = null)
    {
        $plantilla = Model_Plataforma_Ml_Description_Template::find($id);

        if (!$plantilla) {
            Session::set_flash('error', 'Plantilla no encontrada.');
            return Response::redirect_back();
        }

        $plantilla->deleted    = 1;
        $plantilla->updated_at = time();
        $plantilla->save();

        Session::set_flash('success', 'Plantilla eliminada correctamente.');
        return Response::redirect('admin/plataformas/ml/plantillas?config_id='.$plantilla->configuration_id);
    }
}
