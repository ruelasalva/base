<?php

/**
 * ADMIN/APARIENCIA/FOOTER/BADGES
 *
 * CONTROLADOR DE DISTINTIVOS DEL FOOTER
 */
class Controller_Admin_Apariencia_Footer_Badges extends Controller_Admin
{

    /**
     * INDEX
     * LISTA LOS DISTINTIVOS DEL FOOTER
     * $footer_id = ID del footer al que pertenecen los distintivos
     *  MUY IMPORTANTE: se pasa el objeto $footer a la vista para los enlaces de navegación
     */
    public function action_index($footer_id = null)
    {
        $data['footer'] = Model_Appearance_Footer::find($footer_id);

        if (!$data['footer']) {
            Session::set_flash('error', 'Footer no encontrado.');
            Response::redirect('admin/apariencia/footer');
        }

        $data['badges'] = Model_Appearance_Footer_Badge::query()
            ->where('footer_id', $footer_id)
            ->order_by('sort_order', 'asc')
            ->get();

        $this->template->title = "Distintivos del Footer";
        $this->template->content = View::forge('admin/apariencia/footer/badges/index', $data);
    }


    /**
     * AGREGAR DISTINTIVO
     */
    public function action_agregar($footer_id = null)
{
    if (Input::method() == 'POST') {

        # Validación
        $val = Validation::forge();
        $val->add('title', 'Título')->add_rule('required');
        $val->add('sort_order', 'Orden')->add_rule('valid_string', ['numeric']);
        $val->add('status', 'Estado');

        if ($val->run()) {

            # Configuración de subida
            $upload_config = [
                'path'          => DOCROOT.'assets/uploads/footer/badges/',
                'randomize'     => true,
                'ext_whitelist' => ['jpg','jpeg','png'],
            ];
            \Upload::process($upload_config);

            $image_path = null;
            if (\Upload::is_valid()) {
                \Upload::save();
                foreach (\Upload::get_files() as $file) {
                    if ($file['field'] == 'image') {
                        # renombrar con prefijo único (badge)
                        $new_name = 'badge-'.time().'-'.$file['name'];
                        $fullpath = $upload_config['path'].$new_name;
                        rename($file['saved_to'].$file['saved_as'], $fullpath);
                        $image_path = 'uploads/footer/badges/'.$new_name;
                    }
                }
            }

            # Crear registro
            $badge = Model_Appearance_Footer_Badge::forge([
                'footer_id'  => $footer_id,
                'title'      => Input::post('title'),
                'image'      => $image_path,
                'sort_order' => Input::post('sort_order', 0),
                'status'     => Input::post('status', 1),
                'created_at' => time(),
                'updated_at' => time(),
            ]);

            if ($badge and $badge->save()) {
                \Session::set_flash('success', 'Distintivo agregado.');
                \Response::redirect('admin/apariencia/footer/badges/index/'.$footer_id);
            } else {
                \Session::set_flash('error', 'No se pudo guardar el distintivo.');
            }
        } else {
            \Session::set_flash('error', $val->show_errors());
        }
    }

    $data['footer'] = Model_Appearance_Footer::find($footer_id);

    $this->template->title = "Agregar distintivo";
    $this->template->content = \View::forge('admin/apariencia/footer/badges/agregar', $data);
}




    /**
     * EDITAR DISTINTIVO
     */
    /**
 * EDITAR BADGE
 */
public function action_editar($id = null)
{
    $badge = Model_Appearance_Footer_Badge::find($id);

    if (!$badge) {
        \Session::set_flash('error', 'Distintivo no encontrado.');
        \Response::redirect('admin/apariencia/footer');
    }

    $footer = Model_Appearance_Footer::find($badge->footer_id);

    if (\Input::method() == 'POST') {
        # Validación
        $val = \Validation::forge();
        $val->add('title', 'Título')->add_rule('required');
        $val->add('sort_order', 'Orden')->add_rule('valid_string', ['numeric']);
        $val->add('status', 'Estado');

        if ($val->run()) {

            # Subida de imagen
            $upload_config = [
                'path'          => DOCROOT.'assets/uploads/footer/badges/',
                'randomize'     => true,
                'ext_whitelist' => ['jpg','jpeg','png'],
            ];
            \Upload::process($upload_config);

            $image_path = $badge->image; // mantener la actual si no se sube nada

            if (\Upload::is_valid()) {
                \Upload::save();
                foreach (\Upload::get_files() as $file) {
                    if ($file['field'] == 'image') {
                        $new_name = 'badge-'.time().'-'.$file['name'];
                        $fullpath = $upload_config['path'].$new_name;
                        rename($file['saved_to'].$file['saved_as'], $fullpath);
                        $image_path = 'uploads/footer/badges/'.$new_name;
                    }
                }
            }

            # Actualizar datos
            $badge->title      = \Input::post('title');
            $badge->image      = $image_path;
            $badge->sort_order = \Input::post('sort_order', 0);
            $badge->status     = \Input::post('status', 1);
            $badge->updated_at = time();

            if ($badge->save()) {
                \Session::set_flash('success', 'Distintivo actualizado.');
                \Response::redirect('admin/apariencia/footer/badges/index/'.$badge->footer_id);
            } else {
                \Session::set_flash('error', 'No se pudo actualizar el distintivo.');
            }
        } else {
            \Session::set_flash('error', $val->show_errors());
        }
    }

    $data['badge']  = $badge;
    $data['footer'] = $footer;

    $this->template->title   = "Editar distintivo";
    $this->template->content = \View::forge('admin/apariencia/footer/badges/editar', $data);
}



    /**
     * INFO DISTINTIVO
     */
    public function action_info($id = null)
    {
        $badge = Model_Appearance_Footer_Badge::find($id);

        if (!$badge) {
            Session::set_flash('error', 'Distintivo no encontrado.');
            Response::redirect('admin/apariencia/footer');
        }

        $data['badge'] = $badge;

        $this->template->title = "Detalle del distintivo";
        $this->template->content = View::forge('admin/apariencia/footer/badges/info', $data);
    }

    /**
     * ELIMINAR DISTINTIVO
     */
    public function action_eliminar($id = null)
    {
        $badge = Model_Appearance_Footer_Badge::find($id);

        if ($badge and $badge->delete()) {
            Session::set_flash('success', 'Distintivo eliminado.');
            Response::redirect('admin/apariencia/footer/badges/index/'.$badge->footer_id);
        } else {
            Session::set_flash('error', 'No se pudo eliminar el distintivo.');
            Response::redirect('admin/apariencia/footer');
        }
    }
}
