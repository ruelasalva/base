<?php
/**
* CONTROLADOR CSV
*
* @package  app
* @extends  Controller_Base
*/
class Controller_Admin_Helpdesk_CSV extends Controller_Admin
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
    * ARCHIVO INDEX
    *
    * GENERA UN CSV CON LA INFORMACION DE LOS TICKETS
    *
    * @access  public
    * @return  Void
    */

    private function remove_accents($string) {
    return strtr(utf8_decode($string), utf8_decode('áéíóúÁÉÍÓÚñÑ'), 'aeiouAEIOUnN');
    }

    public function action_tickets()
    {
        # SE INICIALIZAN LAS VARIABLES
        $tickets_info = array();
        $row          = array();
        $start_date   = Input::get('r1');
        $end_date     = Input::get('r2');

        # SI NO HAY RANGO DE FECHAS
        if($start_date == 0 && $end_date == 0)
        {
            # SE INICIALIZAN LOS RANGOS DE FECHAS
            $start_date = $this->date2unixtime(date('01'.'/m/Y', time()));
            $end_date   = $this->date2unixtime(date('/m/Y', time()), 'end');
        }

        # SE INICIALIZAN LAS VARIABLES
        $tickets_info[] = array(
            'ID', 'Usuario Asignado', 'Solicitante', 'Departamento que Solicita', 'Tipo de Ticket', 'Tipo de Incidencia', 'Descripción Detallada', 'Prioridad', 'Estatus', 'Fecha Creación', 'Hora Creación', 'Fecha Inicio','Hora de Inicio','Fecha Final','Hora Final', 'Duración'
        );

        # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
        $tickets = Model_Ticket::query()
            ->where('created_at', 'between', array($start_date, $end_date))
            ->order_by('id', 'desc')
            ->get();

        # SI SE OBTIENE INFORMACION
        if(!empty($tickets))
        {
            # SE RECORRE ELEMENTO POR ELEMENTO
            foreach($tickets as $ticket)
            {
                # CALCULAR LA DIFERENCIA DE TIEMPO SIEMPRE Y CUANDO EL TICKET ESTE FINALIZADO
                if ($ticket->status_id == 4){
                    if (!is_null($ticket->start_date) && !is_null($ticket->finish_date)) {
                        # SI HORA INICIO Y HORA FINAL TIENEN INFORMACION CALCULA LA DIFERENCIA ENTRE ELLOS
                        $time_to_solve = $this->calculate_time_difference($ticket->start_date, $ticket->finish_date);
                    } else {
                        # SI HORA INICIO NO TIENEN DATOS CALCULA ENTRE LA HORA DE CREACION Y LA DE ACTUALIZACION
                        $time_to_solve = ($ticket->updated_at && $ticket->created_at) ? $this->calculate_time_difference($ticket->created_at, $ticket->updated_at) : 'N/A';
                    }
                }else{
                    #SI EL TICKET NO ESTA FINALIZADO QUE ES EL 4 SE DEBE PONER EN TIEMPO 0
                    $time_to_solve = 0;
                }

                # SE ALMACENA LA INFORMACION
                $row = array(
                    $ticket->id,
                    $this->remove_accents($ticket->asiguser->name  . ' ' .$ticket->asiguser->last_name),
                    $this->remove_accents($ticket->employee->name  . ' ' . $ticket->employee->last_name),
                    $this->remove_accents($ticket->employee->department->name),
                    $this->remove_accents($ticket->typeticket->name),
                    $this->remove_accents($ticket->incidentticket->name),
                    $this->remove_accents($ticket->description),
                    $this->remove_accents($ticket->priorityticket->name),
                    $this->remove_accents($ticket->statusticket->name)
                );

                # AGREGAR FECHA DE CREACIÓN SI NO ES NULA
                if($ticket->created_at !== null)
                {
                    $row[] = date('Y-m-d', $ticket->created_at);
                    $row[] = date('H:i:s', $ticket->created_at);
                }
                else
                {
                    $row[] = '';
                    $row[] = '';
                }

                # AGREGAR FECHA DE INICIO SI NO ES NULA
                if($ticket->start_date !== null)
                {
                    $row[] = date('Y-m-d', $ticket->start_date);
                    $row[] = date('H:i:s', $ticket->start_date);
                }
                else
                {
                    $row[] = '';
                    $row[] = '';
                }

                # AGREGAR FECHA DE FINALIZACIÓN SI NO ES NULA
                if($ticket->finish_date !== null)
                {
                    $row[] = date('Y-m-d', $ticket->finish_date);
                    $row[] = date('H:i:s', $ticket->finish_date);
                }
                else
                {
                    $row[] = '';
                    $row[] = '';
                }

                # AGREGAR DURACION
                $row[] = $time_to_solve;

                $tickets_info[] = $row;
            }
        }

        # SE GENERA EL CSV
        $this->generate_csv_tickets($tickets_info);
    }



    /**
    * GENERATE CSV
    *
    * CONVIERTE ARRAYS BODY Y HEADERS EN UN CSV
    *
    * @access  private
    * @return  Void
    */
    private function generate_csv_tickets($body)
    {
        # SE ESTABLECE EL NOMBRE DEL ARCHIVO CSV
        $filename = 'tickets.csv';

        # SE INICIALIZAN LAS VARIABLES
        $delimiter    = ',';
        $encapsulator = '"';

        # SE ESTABLECEN LAS CABECERAS
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'. $filename. '"');

        # ABRE EL FLUJO DE SALIDA PARA ESCRITURA
        $output = fopen('php://output', 'w');

        # PROCESAR LOS TÍTULOS DE COLUMNAS PARA ELIMINAR ACENTOS
        $titles = array_map(function($item) {
            return $this->remove_accents($item);
        }, array_shift($body));

        # ESCRIBIR LOS TÍTULOS PROCESADOS SIN EL BOM
        fputcsv($output, $titles, $delimiter, $encapsulator);

        # SE RECORRE ELEMENTO POR ELEMENTO PARA EL RESTO DE LAS FILAS
        foreach ($body as $row) {
            # Asegura que cada elemento del array esté en UTF-8 y elimina acentos si es necesario
            $converted_row = array_map(function($item) {
                return mb_convert_encoding($this->remove_accents($item), 'UTF-8', 'UTF-8');
            }, $row);

            # SE ESCRIBE LOS DATOS EN EL ARCHIVO CSV
            fputcsv($output, $converted_row, $delimiter, $encapsulator);
        }

        # SE CIERRA EL FLUJO DE SALIDA
        fclose($output);

        die();
    }


    /**
    * ARCHIVO INDEX
    *
    * GENERA UN CSV CON LA INFORMACION DE LAS TAREAS
    *
    * @access  public
    * @return  Void
    */
    public function action_tasks()
    {
        # SE INICIALIZAN LAS VARIABLES
        $tasks_info[] = array(
            'ID', 'Empleado', 'Departamento que Solicita', 'Descripción detallada', 'Comentarios', 'Estatus', 'Fecha de Creación', 'Fecha compromiso', 'Fecha finalizado'
        );

        # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
        $tasks = Model_Task::query()
        ->order_by('id', 'desc')
        ->get();

        # SI SE OBTIENE INFORMACION
        if(!empty($tasks))
        {
            # SE RECORRE ELEMENTO POR ELEMENTO
            foreach($tasks as $task)
            {
                # SE ALMACENA LA INFORMACION
                $row = array(
                    $task->id,
                    $this->remove_accents($task->employee->name  . ' ' . $task->employee->last_name),
                    $this->remove_accents($task->employee->department->name),
                    $this->remove_accents($task->description),
                    $this->remove_accents($task->comments),
                    $this->remove_accents($task->statusticket->name)
                );

                # AGREGAR FECHA DE CREACIÓN SI NO ES NULA
                if($task->created_at !== null)
                {
                    $row[] = date('Y-m-d', $task->created_at);
                }
                else
                {
                    $row[] = '';
                }

                # AGREGAR FECHA DE COMPROMISO SI NO ES NULA
                if($task->commitment_at !== null)
                {
                    $row[] = date('Y-m-d', $task->commitment_at);
                }
                else
                {
                    $row[] = '';
                }

                # AGREGAR FECHA DE FINALIZACION SI NO ES NULA
                if($task->finish_at !== null)
                {
                    $row[] = date('Y-m-d', $task->finish_at);
                }
                else
                {
                    $row[] = '';
                }

                $tasks_info[] = $row;
            }
        }

        # SE GENERA EL CSV
        $this->generate_csv_tasks($tasks_info);
    }

     /**
    * GENERATE CSV
    *
    * CONVIERTE ARRAYS BODY Y HEADERS EN UN CSV
    *
    * @access  private
    * @return  Void
    */
    private function generate_csv_tasks($body)
    {
    # SE ESTABLECE EL NOMBRE DEL ARCHIVO CSV
    $filename = 'tasks.csv';

    # SE INICIALIZAN LAS VARIABLES
    $delimiter    = ',';
    $encapsulator = '"';

    # SE ESTABLECEN LAS CABECERAS
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="'. $filename . '"');

    # ABRE EL FLUJO DE SALIDA PARA ESCRITURA
    $output = fopen('php://output', 'w');

    # PROCESAR LOS TÍTULOS DE COLUMNAS PARA ELIMINAR ACENTOS
    $titles = array_map(function($item) {
        return $this->remove_accents($item);
    }, array_shift($body));

    # ESCRIBIR LOS TÍTULOS PROCESADOS
    fputcsv($output, $titles, $delimiter, $encapsulator);

    # SE RECORRE ELEMENTO POR ELEMENTO
    foreach ($body as $row) {
        # Asegura que cada elemento del array esté en UTF-8
        $converted_row = array_map(function($item) {
             return mb_convert_encoding($this->remove_accents($item), 'UTF-8', 'UTF-8');
        }, $row);

        # SE ESCRIBE LOS DATOS EN EL ARCHIVO CSV
        fputcsv($output, $converted_row, $delimiter, $encapsulator);
     }

    # SE CIERRA EL FLUJO DE SALIDA
    fclose($output);

    die();
    }

    private function calculate_time_difference($start, $end)
    {
        # CALCULAR LA DIFERENCIA EN SEGUNDOS
        $diff = $end - $start;

        # CALCULAR LOS MINUTOS
        $minutes = floor($diff / 60);

        # DEVOLVER EL FORMATO DE TIEMPO
        return sprintf('%d M', $minutes);
    }

}
