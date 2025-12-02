<?php

/**
 * ADMIN/APARIENCIA/FOOTER/LINKS
 *
 * CONTROLADOR DE ENLACES DEL FOOTER
 */
class Controller_Admin_Apariencia_Footer_Links extends Controller_Admin
{
    /**
     * INDEX
     * LISTA LOS ENLACES DEL FOOTER
     * $footer_id = ID del footer al que pertenecen los enlaces
     *  MUY IMPORTANTE: se pasa el objeto $footer a la vista para los enlaces de navegación
     */
    public function action_index($footer_id = null)
    {
        if (!$footer_id) {
            Session::set_flash('error', 'No se especificó Footer.');
            Response::redirect('admin/apariencia/footer');
        }

        $footer = Model_Appearance_Footer::find($footer_id);

        if (!$footer) {
            Session::set_flash('error', 'Footer no encontrado.');
            Response::redirect('admin/apariencia/footer');
        }

        // Separar sitemap y legales
        $sitemaps = Model_Appearance_Footer_Link::query()
            ->where('footer_id', $footer_id)
            ->where('type', 'sitemap')
            ->order_by('sort_order', 'asc')
            ->get();

        $legals = Model_Appearance_Footer_Link::query()
            ->where('footer_id', $footer_id)
            ->where('type', 'legal')
            ->order_by('sort_order', 'asc')
            ->get();

        $data['footer']   = $footer;
        $data['sitemaps'] = $sitemaps;
        $data['legals']   = $legals;

        $this->template->title   = "Links";
        $this->template->content = View::forge('admin/apariencia/footer/links/index', $data);
    }

    /**
     * AGREGAR LINK
     */
    public function action_agregar($footer_id = null)
    {
        if (!$footer_id) {
            Session::set_flash('error', 'No se especificó Footer.');
            Response::redirect('admin/apariencia/footer');
        }

        $footer = Model_Appearance_Footer::find($footer_id);

        if (!$footer) {
            Session::set_flash('error', 'Footer no encontrado.');
            Response::redirect('admin/apariencia/footer');
        }

        if (Input::method() == 'POST') {
        $slug = Input::post('slug'); // por si es sitemap
        if (Input::post('type') === 'legal' && Input::post('legal_id')) {
            $doc = Model_Legal_Document::find(Input::post('legal_id'));
            if ($doc) {
                $slug = $doc->shortcode; // usar shortcode como slug
            }
        }

            $link = Model_Appearance_Footer_Link::forge([
                'footer_id'  => $footer_id,
                'title'      => Input::post('title'),
                'url'        => Input::post('url'),
                'slug'       => $slug,
                'type'       => Input::post('type'),
                'legal_id'   => Input::post('legal_id') ?: null,   // legal_id puede ser nulo si no es un documento legal
                'sort_order' => Input::post('sort_order'),
                'status'     => Input::post('status'),
                'created_at' => time(),
                'updated_at' => time(),
            ]);

            if ($link and $link->save()) {
                Session::set_flash('success', 'Link agregado correctamente.');
                Response::redirect('admin/apariencia/footer/links/index/'.$footer_id);
            } else {
                Session::set_flash('error', 'No se pudo agregar el link.');
            }
        }

        // 🔹 Traer documentos legales activos desde Legal
        $legal_docs = Model_Legal_Document::query()
            ->where('active', 0)
            ->order_by('title', 'asc')
            ->get();

        $data['footer']     = $footer;
        $data['legal_docs'] = $legal_docs; 

        $this->template->title   = "Agregar Link";
        $this->template->content = View::forge('admin/apariencia/footer/links/agregar', $data);
    }


    /**
 * EDITAR LINK
 */
public function action_editar($id = null)
{
    $link = Model_Appearance_Footer_Link::find($id);

    if (!$link) {
        Session::set_flash('error', 'Link no encontrado.');
        Response::redirect('admin/apariencia/footer');
    }

    $footer = Model_Appearance_Footer::find($link->footer_id);

    if (Input::method() == 'POST') {
    $slug = Input::post('slug');
    if (Input::post('type') === 'legal' && Input::post('legal_id')) {
        $doc = Model_Legal_Document::find(Input::post('legal_id'));
        if ($doc) {
            $slug = $doc->shortcode;
        }
    }
        $link->title      = Input::post('title');
        $link->url        = Input::post('url');
        $link->slug       = $slug;
        $link->type       = Input::post('type');
        $link->legal_id   = Input::post('legal_id') ?: null;  // NUEVO
        $link->sort_order = Input::post('sort_order');
        $link->status     = Input::post('status');

        if ($link->save()) {
            Session::set_flash('success', 'Link actualizado.');
            Response::redirect('admin/apariencia/footer/links/index/'.$link->footer_id);
        } else {
            Session::set_flash('error', 'No se pudo actualizar el link.');
        }
    }

    // 🔹 Traer documentos legales activos
    $legal_docs = Model_Legal_Document::query()
        ->where('active', 0)
        ->order_by('title', 'asc')
        ->get();

    $data['link']       = $link;
    $data['footer']     = $footer;
    $data['legal_docs'] = $legal_docs;

    $this->template->title   = "Editar Link";
    $this->template->content = View::forge('admin/apariencia/footer/links/editar', $data);
}



    /**
     * INFO LINK
     */
    public function action_info($id = null)
    {
        $link = Model_Appearance_Footer_Link::find($id);

        if (!$link) {
            Session::set_flash('error', 'Enlace no encontrado.');
            Response::redirect('admin/apariencia/footer');
        }

        $data['link'] = $link;

        $this->template->title = "Detalle del enlace";
        $this->template->content = View::forge('admin/apariencia/footer/links/info', $data);
    }

    /**
     * ELIMINAR LINK
     */
    public function action_eliminar($id = null)
    {
        $link = Model_Appearance_Footer_Link::find($id);

        if ($link and $link->delete()) {
            Session::set_flash('success', 'Enlace eliminado.');
            Response::redirect('admin/apariencia/footer/links/index/'.$link->footer_id);
        } else {
            Session::set_flash('error', 'No se pudo eliminar el enlace.');
            Response::redirect('admin/apariencia/footer');
        }
    }
}
