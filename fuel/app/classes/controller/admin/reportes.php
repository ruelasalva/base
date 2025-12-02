<?php
/**
 * MÓDULO DE REPORTES ADMINISTRATIVOS
 * Versión inicial (Fase 1)
 * - Muestra departamentos (employees_departments)
 * - Lista reportes asignados (reports_queries)
 * - Permite ejecutar y exportar resultados
 */

class Controller_Admin_Reportes extends Controller_Admin
{
    public function action_index()
    {
        // CARGAR TODOS LOS DEPARTAMENTOS ACTIVOS
        $departments = Model_Employees_Department::query()
            ->where('deleted', 0)
            ->order_by('name', 'asc')
            ->get();

        // CARGAR REPORTES ACTIVOS AGRUPADOS POR DEPARTAMENTO
        $reports = Model_Reports_Query::query()
            ->where('deleted', 0)
            ->where('is_active', 1)
            ->order_by('department_id', 'asc')
            ->order_by('query_name', 'asc')
            ->get();

        // AGRUPAR LOS REPORTES POR DEPARTAMENTO
        $reportes_por_departamento = [];
        foreach ($reports as $r) {
            $dept_id = (int) $r->department_id;
            if (!isset($reportes_por_departamento[$dept_id])) {
                $reportes_por_departamento[$dept_id] = [];
            }
            $reportes_por_departamento[$dept_id][] = $r;
        }

        $data = [
            'departments' => $departments,
            'reportes_por_departamento' => $reportes_por_departamento,
        ];

        $this->template->title = 'Reportes | Administrador';
        $this->template->content = View::forge('admin/reportes/index', $data);
    }

    /**
     * AJAX - EJECUTAR REPORTE Y MOSTRAR EN PANTALLA
     */
    public function post_ejecutar()
    {
        if (!Input::is_ajax()) return;
        $query_id = (int) Input::post('query_id', 0);

        $query = Model_Reports_Query::find($query_id);
        if (!$query) {
            return Response::forge(json_encode(['error' => 'Reporte no encontrado']));
        }

        $sql = trim($query->query_sql);

        // VALIDAR SOLO SELECT
        if (!preg_match('/^\s*select/i', $sql)) {
            return Response::forge(json_encode(['error' => 'Solo se permiten consultas SELECT']));
        }

        try {
            $result = DB::query($sql)->execute()->as_array();
        } catch (Database_Exception $e) {
            Log::error('[REPORTES] Error al ejecutar consulta: '.$e->getMessage());
            return Response::forge(json_encode(['error' => 'Error al ejecutar la consulta']));
        }

        return Response::forge(json_encode(['rows' => $result]));
    }

    /**
     * EXPORTAR REPORTE A CSV
     */
    public function post_exportar_csv()
    {
        $query_id = (int) Input::post('query_id', 0);
        $query = Model_Reports_Query::find($query_id);

        if (!$query) {
            Session::set_flash('error', 'Reporte no encontrado');
            return Response::redirect('admin/reportes');
        }

        $sql = trim($query->query_sql);
        if (!preg_match('/^\s*select/i', $sql)) {
            Session::set_flash('error', 'Solo se permiten consultas SELECT');
            return Response::redirect('admin/reportes');
        }

        $filename = 'reporte_' . preg_replace('/\s+/', '_', strtolower($query->query_name)) . '_' . date('Ymd_His') . '.csv';

        try {
            $rows = DB::query($sql)->execute()->as_array();
        } catch (Database_Exception $e) {
            Log::error('[REPORTES][CSV] '.$e->getMessage());
            Session::set_flash('error', 'Error al generar CSV');
            return Response::redirect('admin/reportes');
        }

        // GENERAR CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        $output = fopen('php://output', 'w');

        if (!empty($rows)) {
            fputcsv($output, array_keys($rows[0]));
            foreach ($rows as $r) {
                fputcsv($output, $r);
            }
        } else {
            fputcsv($output, ['Sin resultados']);
        }

        fclose($output);
        exit;
    }

    /**
 * EXPORTAR REPORTE A PDF
 * Genera un PDF con el resultado del reporte (query guardada).
 */
public function post_exportar_pdf()
{

    ini_set('memory_limit', '1024M'); // aumenta límite para grandes reportes
    set_time_limit(300); // 5 minutos de ejecución
    
    // 1. Obtener ID del reporte
    $query_id = (int) Input::post('query_id', 0);
    if (!$query_id) {
        \Session::set_flash('error', 'ID de reporte no válido.');
        \Response::redirect('admin/reportes');
    }

    // 2. Cargar la consulta guardada
    $reporte = \Model_Reports_Query::find($query_id);
    if (!$reporte) {
        \Session::set_flash('error', 'Reporte no encontrado.');
        \Response::redirect('admin/reportes');
    }

    // 3. Ejecutar la consulta
    $sql = trim($reporte->query_sql);
    if (stripos($sql, 'select') !== 0) {
        \Session::set_flash('error', 'Solo se permiten consultas SELECT.');
        \Response::redirect('admin/reportes');
    }

    try {
        $rows = \DB::query($sql)->execute()->as_array();
    } catch (\Database_Exception $e) {
        \Log::error('[REPORTES][PDF][ERROR] ' . $e->getMessage());
        \Session::set_flash('error', 'Error al ejecutar la consulta: ' . $e->getMessage());
        \Response::redirect('admin/reportes');
    }

    if (empty($rows)) {
        \Session::set_flash('warning', 'El reporte no devolvió resultados.');
        \Response::redirect('admin/reportes');
    }

    // 4. Preparar datos para la vista
    $data['reporte'] = $reporte;
    $data['rows'] = $rows;
    $data['is_pdf_export'] = true; // indica a la vista que está en modo PDF

    // 5. Crear HTML del reporte (vista dedicada)
    $html = \View::forge('admin/reportes/pdf', $data)->render();

    // 6. Instanciar Dompdf
    $dompdf = new \Dompdf\Dompdf();
    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');
    $dompdf->setOptions($options);

    // 7. Cargar HTML
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape'); // horizontal para tablas amplias

    try {
        $dompdf->render();
    } catch (\Exception $e) {
        \Log::error('Dompdf render error: ' . $e->getMessage());
        \Session::set_flash('error', 'Error al generar PDF.');
        \Response::redirect('admin/reportes');
    }

    // 8. Descargar PDF
    $filename = 'Reporte_' . preg_replace('/\s+/', '_', $reporte->query_name) . '.pdf';
    $dompdf->stream($filename, ["Attachment" => 1]);
    exit();
}



    /**
 * AGREGAR NUEVO REPORTE
 */
public function action_agregar()
{
    $val = Validation::forge();
    $val->add('query_name', 'Nombre del reporte')->add_rule('required');
    $val->add('query_sql', 'Consulta SQL')->add_rule('required');
    $val->add('department_id', 'Departamento')->add_rule('required');

    if (Input::method() == 'POST') {
        if ($val->run()) {
            $q = Model_Reports_Query::forge(array(
                'query_name'   => Input::post('query_name'),
                'query_sql'    => Input::post('query_sql'),
                'description'  => Input::post('description'),
                'department_id'=> Input::post('department_id'),
                'user_id'      => Auth::get('id'),
                'is_active'    => 1,
                'deleted'      => 0,
            ));
            if ($q->save()) {
                Session::set_flash('success', 'Reporte creado correctamente');
                return Response::redirect('admin/reportes');
            } else {
                Session::set_flash('error', 'No se pudo guardar el reporte');
            }
        } else {
            Session::set_flash('error', $val->show_errors());
        }
    }

    $departments = Model_Employees_Department::query()->where('deleted',0)->get();
    $data = array('departments' => $departments);
    $this->template->title = 'Agregar Reporte';
    $this->template->content = View::forge('admin/reportes/agregar', $data);
}





}
