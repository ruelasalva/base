<?php
/**
 * CONTROLADOR ADMIN - Catálogo: Tipos de Documento
 * 
 * Gestiona el catálogo document_types (Remisión, Cotización, Requisición, etc.)
 * Compatible con Model_Document_Type.
 * 
 * Estructura:
 * - index(): listado general con filtros
 * - agregar(): alta manual
 * - editar(): actualización
 * - eliminar(): borrado lógico
 */
class Controller_Admin_Catalogo_Generales_Tipodedocumento extends Controller_Admin
{
    /**
     * INDEX
     * Lista todos los tipos de documento con filtros básicos.
     */
    public function action_index()
    {
        $search = Input::get('search', '');
        $scope  = Input::get('scope', '');
        $per_page = 50;

        $query = Model_Document_Type::query()
            ->where('deleted', 0)
            ->order_by('name', 'asc');

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($scope !== '') {
            $query->where('scope', $scope);
        }

        $config = [
            'pagination_url' => Uri::current(),
            'total_items'    => $query->count(),
            'per_page'       => $per_page,
            'uri_segment'    => 'pagina',
            'show_first'     => true,
            'show_last'      => true,
        ];

        $pagination = Pagination::forge('tipos', $config);

        $tipos = $query
            ->rows_limit($pagination->per_page)
            ->rows_offset($pagination->offset)
            ->get();

        $data = [
            'tipos'       => $tipos,
            'search'      => $search,
            'scope'       => $scope,
            'pagination'  => $pagination->render(),
        ];

        $this->template->title   = 'Catálogo - Tipos de Documento';
        $this->template->content = View::forge('admin/catalogo/generales/tipodedocumento/index', $data, false);
    }

    /**
     * AGREGAR NUEVO TIPO DE DOCUMENTO
     */
    public function action_agregar()
    {
        if (Input::method() === 'POST') {
            try {
                $val = Validation::forge();
                $val->add('name', 'Nombre')->add_rule('required');
                $val->add('scope', 'Ámbito')->add_rule('required');

                if (!$val->run()) {
                    Session::set_flash('error', 'Faltan campos obligatorios.');
                    Response::redirect('admin/catalogo/generales/tipodedocumento/agregar');
                }

                $tipo = Model_Document_Type::forge([
                    'name'       => Input::post('name'),
                    'scope'      => Input::post('scope'),
                    'active'     => Input::post('active', 1),
                    'deleted'    => 0,
                    'created_at' => time(),
                    'updated_at' => time(),
                ]);

                $tipo->save();
                Session::set_flash('success', 'Tipo de documento agregado correctamente.');
                Response::redirect('admin/catalogo/generales/tipodedocumento');
            } catch (Exception $e) {
                \Log::error('[TIPOS_DOC][ADD][ERROR] ' . $e->getMessage());
                Session::set_flash('error', $e->getMessage());
            }
        }

        $this->template->title   = 'Agregar Tipo de Documento';
        $this->template->content = View::forge('admin/catalogo/generales/tipodedocumento/agregar', [], false);
    }

    /**
     * EDITAR TIPO DE DOCUMENTO
     */
    public function action_editar($id = null)
    {
        $tipo = Model_Document_Type::find($id);
        if (!$tipo) {
            Session::set_flash('error', 'Tipo de documento no encontrado.');
            Response::redirect('admin/catalogo/generales/tipodedocumento');
        }

        if (Input::method() === 'POST') {
            try {
                $tipo->name    = Input::post('name');
                $tipo->scope   = Input::post('scope');
                $tipo->active  = Input::post('active', 1);
                $tipo->updated_at = time();
                $tipo->save();

                Session::set_flash('success', 'Tipo de documento actualizado correctamente.');
                Response::redirect('admin/catalogo/generales/tipodedocumento');
            } catch (Exception $e) {
                \Log::error('[TIPOS_DOC][EDIT][ERROR] ' . $e->getMessage());
                Session::set_flash('error', $e->getMessage());
            }
        }

        $data = ['tipo' => $tipo];

        $this->template->title   = 'Editar Tipo de Documento';
        $this->template->content = View::forge('admin/catalogo/generales/tipodedocumento/editar', $data, false);
    }

    /**
     * ELIMINAR (borrado lógico)
     */
    public function action_eliminar($id = null)
    {
        $tipo = Model_Document_Type::find($id);
        if (!$tipo) {
            Session::set_flash('error', 'Tipo de documento no encontrado.');
        } else {
            $tipo->deleted = 1;
            $tipo->save();
            Session::set_flash('success', 'Tipo de documento eliminado correctamente.');
        }
        Response::redirect('admin/catalogo/generales/tipodedocumento');
    }

    /**
     * INFO
     * Muestra los detalles de un tipo de documento
     */
    public function action_info($id = null)
    {
        $tipo = Model_Document_Type::find($id);
        if (!$tipo) {
            Session::set_flash('error', 'Tipo de documento no encontrado.');
            Response::redirect('admin/catalogo/generales/tipodedocumento');
        }

        $data = ['tipo' => $tipo];
        $this->template->title   = 'Detalle - Tipo de Documento';
        $this->template->content = View::forge('admin/catalogo/generales/tipodedocumento/info', $data, false);
    }

}
