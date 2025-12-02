<?php

/**
 * CONTROLADOR PROVEEDOR_PERFIL_LEGAL
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Perfil_Legal extends Controller_Admin
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
		if(!Auth::member(100) && !Auth::member(50) && !Auth::member(25))
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			Session::set_flash('error', 'No tienes los permisos para acceder a esta sección.');

			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin');
		}
	}


	/**
	 * FISCAL
	 *
	 * PERMITE EDITAR EL PERFIL FISCAL DEL PROPIO USUARIO
	 *
	 * @access  public
	 * @return  Void
	 */
	 public function action_index()
    {
        $user_id  = Auth::get('id');
        $category = 'empleado'; // clave para filtrar documentos importantes para admin

        $data['documents'] = Model_Legal_Document::query()
            ->where('active', 0)
            ->where_open()
                ->where('category', $category)
                ->or_where('category', 'general')
            ->where_close()
            ->order_by('created_at', 'desc')
            ->get();

        $this->template->title   = 'Documentos Legales';
        $this->template->content = View::forge('admin/perfil/legal/index', $data);
    }

    /**
     * INFO
     * Ver detalle de un documento
     */
    public function action_info($id = null)
    {
        $doc = Model_Legal_Document::find($id);

        if (!$doc || $doc->active != 0) {
            Session::set_flash('error', 'Documento no encontrado.');
            Response::redirect('admin/perfil/legal');
        }

        $data['doc'] = $doc;

        $this->template->title   = 'Detalle de Documento';
        $this->template->content = View::forge('admin/perfil/legal/info', $data);
    }

    /**
     * IMPRIMIR
     * Descargar en PDF
     */
    /**
 * IMPRIMIR DOCUMENTO
 *
 * Muestra el documento en una vista limpia para impresión
 */
public function action_imprimir($id = null)
{
    $doc = Model_Legal_Document::find($id);

    if (!$doc || $doc->active != 0) {
        Session::set_flash('error', 'Documento no disponible para impresión.');
        Response::redirect('admin/perfil/legal');
    }

    // Renderiza vista exclusiva para impresión
    $data['doc'] = $doc;

    return Response::forge(View::forge('admin/perfil/legal/imprimir', $data));
}

    /**
 * VER PDF en navegador
 */
public function action_ver_pdf($id = null)
{
    $doc = Model_Legal_Document::find($id);

    if (!$doc || $doc->active != 0) {
        Session::set_flash('error', 'Documento no disponible en PDF.');
        Response::redirect('admin/perfil/legal');
    }

    # false = vista previa en navegador
    Helper_Legal::export_pdf_frontend($doc, 'documento_'.$doc->shortcode.'.pdf', false);
}

/**
 * DESCARGAR PDF
 */
public function action_descargar($id = null)
{
    $doc = Model_Legal_Document::find($id);

    if (!$doc || $doc->active != 0) {
        Session::set_flash('error', 'Documento no disponible para descarga.');
        Response::redirect('admin/perfil/legal');
    }

    # true = forzar descarga
    Helper_Legal::export_pdf_frontend($doc, 'documento_'.$doc->shortcode.'.pdf', true);
}



}
