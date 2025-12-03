<?php
/**
 * Helper_ProviderLog
 * 
 * Sistema de trazabilidad y auditoría para el módulo de proveedores.
 * Registra todas las acciones críticas con contexto completo.
 * 
 * @package    Base
 * @category   Helpers
 * @author     Sistema Proveedores
 * @version    1.0
 */
class Helper_ProviderLog
{
    /**
     * Registra una acción en el log de proveedores
     * 
     * @param string $entity Entidad afectada (providers, providers_bills, providers_receipts)
     * @param int $entity_id ID del registro afectado
     * @param string $action Acción realizada (register, login, upload, validate, approve, suspend, etc.)
     * @param string $description Descripción legible de la acción
     * @param mixed $old_data Datos anteriores (array o null)
     * @param mixed $new_data Datos nuevos (array o null)
     * @param int $provider_id ID del proveedor (opcional, se detecta automáticamente si es posible)
     * @return int ID del log insertado
     */
    public static function record($entity, $entity_id, $action, $description, $old_data = null, $new_data = null, $provider_id = null)
    {
        try {
            // Obtener información de contexto
            $user_id = \Auth::check() ? \Auth::get_user_id()[1] : null;
            $ip_address = \Input::real_ip();
            $user_agent = \Input::user_agent();
            
            // Si no se proporciona provider_id, intentar detectarlo
            if ($provider_id === null) {
                $provider_id = self::detect_provider_id($entity, $entity_id);
            }
            
            // Preparar datos para JSON
            $old_json = $old_data ? json_encode($old_data, JSON_UNESCAPED_UNICODE) : null;
            $new_json = $new_data ? json_encode($new_data, JSON_UNESCAPED_UNICODE) : null;
            
            // Insertar log
            list($log_id, $rows) = \DB::insert('providers_action_logs')
                ->set([
                    'tenant_id' => 1, // TODO: Obtener dinámicamente
                    'provider_id' => $provider_id,
                    'user_id' => $user_id,
                    'entity' => $entity,
                    'entity_id' => $entity_id,
                    'action' => $action,
                    'description' => $description,
                    'old_data' => $old_json,
                    'new_data' => $new_json,
                    'ip_address' => $ip_address,
                    'user_agent' => $user_agent,
                    'created_at' => \DB::expr('NOW()')
                ])
                ->execute();
            
            return $log_id;
            
        } catch (\Exception $e) {
            \Log::error('Error registrando log de proveedor: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Intenta detectar el provider_id basado en la entidad y entity_id
     * 
     * @param string $entity
     * @param int $entity_id
     * @return int|null
     */
    private static function detect_provider_id($entity, $entity_id)
    {
        try {
            switch ($entity) {
                case 'providers':
                    return $entity_id;
                
                case 'providers_bills':
                    $bill = \DB::select('provider_id')
                        ->from('providers_bills')
                        ->where('id', $entity_id)
                        ->execute()
                        ->current();
                    return $bill ? $bill['provider_id'] : null;
                
                case 'providers_receipts':
                    $receipt = \DB::select('provider_id')
                        ->from('providers_receipts')
                        ->where('id', $entity_id)
                        ->execute()
                        ->current();
                    return $receipt ? $receipt['provider_id'] : null;
                
                case 'providers_orders':
                    $order = \DB::select('provider_id')
                        ->from('providers_orders')
                        ->where('id', $entity_id)
                        ->execute()
                        ->current();
                    return $order ? $order['provider_id'] : null;
                
                default:
                    return null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Registros de acciones comunes (métodos helper)
     */
    
    public static function log_registration($provider_id, $provider_data)
    {
        return self::record(
            'providers',
            $provider_id,
            'register',
            sprintf('Nuevo proveedor registrado: %s (RFC: %s)', 
                $provider_data['company_name'], 
                $provider_data['tax_id'] ?? 'N/A'
            ),
            null,
            $provider_data,
            $provider_id
        );
    }
    
    public static function log_login_success($provider_id, $email)
    {
        return self::record(
            'providers',
            $provider_id,
            'login_success',
            sprintf('Login exitoso: %s', $email),
            null,
            ['email' => $email, 'timestamp' => date('Y-m-d H:i:s')],
            $provider_id
        );
    }
    
    public static function log_login_failed($email, $reason = 'Contraseña incorrecta')
    {
        return self::record(
            'providers',
            null,
            'login_failed',
            sprintf('Login fallido: %s - %s', $email, $reason),
            null,
            ['email' => $email, 'reason' => $reason],
            null
        );
    }
    
    public static function log_email_confirmation($provider_id, $email)
    {
        return self::record(
            'providers',
            $provider_id,
            'email_confirmed',
            sprintf('Email confirmado: %s', $email),
            ['email_confirmed' => false],
            ['email_confirmed' => true],
            $provider_id
        );
    }
    
    public static function log_bill_upload($bill_id, $uuid, $provider_id, $total)
    {
        return self::record(
            'providers_bills',
            $bill_id,
            'upload',
            sprintf('Factura subida - UUID: %s, Total: $%.2f', $uuid, $total),
            null,
            ['uuid' => $uuid, 'total' => $total],
            $provider_id
        );
    }
    
    public static function log_bill_validation_sat($bill_id, $uuid, $sat_status, $provider_id)
    {
        return self::record(
            'providers_bills',
            $bill_id,
            'validate_sat',
            sprintf('Validación SAT - UUID: %s, Status: %s', $uuid, $sat_status),
            null,
            ['uuid' => $uuid, 'sat_status' => $sat_status],
            $provider_id
        );
    }
    
    public static function log_bill_approval($bill_id, $uuid, $admin_id, $provider_id)
    {
        return self::record(
            'providers_bills',
            $bill_id,
            'approve',
            sprintf('Factura aprobada por admin #%d - UUID: %s', $admin_id, $uuid),
            ['status' => 'pending'],
            ['status' => 'approved', 'validated_by' => $admin_id],
            $provider_id
        );
    }
    
    public static function log_bill_rejection($bill_id, $uuid, $admin_id, $reason, $provider_id)
    {
        return self::record(
            'providers_bills',
            $bill_id,
            'reject',
            sprintf('Factura rechazada por admin #%d - UUID: %s. Razón: %s', $admin_id, $uuid, $reason),
            ['status' => 'pending'],
            ['status' => 'rejected', 'validated_by' => $admin_id, 'message' => $reason],
            $provider_id
        );
    }
    
    public static function log_receipt_generation($receipt_id, $receipt_number, $bill_id, $provider_id)
    {
        return self::record(
            'providers_receipts',
            $receipt_id,
            'generate',
            sprintf('Contrarecibo generado: %s para factura #%d', $receipt_number, $bill_id),
            null,
            ['receipt_number' => $receipt_number, 'bill_id' => $bill_id],
            $provider_id
        );
    }
    
    public static function log_provider_suspension($provider_id, $reason, $admin_id)
    {
        return self::record(
            'providers',
            $provider_id,
            'suspend',
            sprintf('Cuenta suspendida por admin #%d. Razón: %s', $admin_id, $reason),
            ['is_suspended' => 0],
            ['is_suspended' => 1, 'suspended_reason' => $reason, 'suspended_at' => date('Y-m-d H:i:s')],
            $provider_id
        );
    }
    
    public static function log_provider_activation($provider_id, $admin_id)
    {
        return self::record(
            'providers',
            $provider_id,
            'activate',
            sprintf('Cuenta activada por admin #%d', $admin_id),
            ['is_suspended' => 1],
            ['is_suspended' => 0, 'activated_by' => $admin_id, 'activated_at' => date('Y-m-d H:i:s')],
            $provider_id
        );
    }
    
    public static function log_password_reset_request($provider_id, $email)
    {
        return self::record(
            'providers',
            $provider_id,
            'password_reset_request',
            sprintf('Solicitud de reseteo de contraseña: %s', $email),
            null,
            ['email' => $email, 'timestamp' => date('Y-m-d H:i:s')],
            $provider_id
        );
    }
    
    /**
     * Obtiene el historial de logs para un proveedor
     * 
     * @param int $provider_id
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function get_provider_history($provider_id, $limit = 50, $offset = 0)
    {
        return \DB::select()
            ->from('providers_action_logs')
            ->where('provider_id', $provider_id)
            ->order_by('created_at', 'DESC')
            ->limit($limit)
            ->offset($offset)
            ->execute()
            ->as_array();
    }
    
    /**
     * Obtiene logs de una entidad específica
     * 
     * @param string $entity
     * @param int $entity_id
     * @param int $limit
     * @return array
     */
    public static function get_entity_logs($entity, $entity_id, $limit = 20)
    {
        return \DB::select()
            ->from('providers_action_logs')
            ->where('entity', $entity)
            ->where('entity_id', $entity_id)
            ->order_by('created_at', 'DESC')
            ->limit($limit)
            ->execute()
            ->as_array();
    }
    
    /**
     * Obtiene estadísticas de logs para dashboard
     * 
     * @param int $tenant_id
     * @return array
     */
    public static function get_dashboard_stats($tenant_id = 1)
    {
        // Logs de las últimas 24 horas por acción
        $last_24h = date('Y-m-d H:i:s', strtotime('-24 hours'));
        
        $stats = \DB::select('action', \DB::expr('COUNT(*) as count'))
            ->from('providers_action_logs')
            ->where('tenant_id', $tenant_id)
            ->where('created_at', '>=', $last_24h)
            ->group_by('action')
            ->execute()
            ->as_array();
        
        $result = [];
        foreach ($stats as $stat) {
            $result[$stat['action']] = (int)$stat['count'];
        }
        
        return $result;
    }
    
    /**
     * Busca logs con filtros avanzados
     * 
     * @param array $filters ['provider_id' => X, 'action' => 'login', 'date_from' => 'YYYY-MM-DD', 'date_to' => 'YYYY-MM-DD']
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function search_logs(array $filters, $limit = 100, $offset = 0)
    {
        $query = \DB::select()
            ->from('providers_action_logs');
        
        if (isset($filters['provider_id'])) {
            $query->where('provider_id', $filters['provider_id']);
        }
        
        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }
        
        if (isset($filters['entity'])) {
            $query->where('entity', $filters['entity']);
        }
        
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from'] . ' 00:00:00');
        }
        
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to'] . ' 23:59:59');
        }
        
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        if (isset($filters['ip_address'])) {
            $query->where('ip_address', 'LIKE', '%' . $filters['ip_address'] . '%');
        }
        
        $total = $query->count();
        
        $results = $query
            ->order_by('created_at', 'DESC')
            ->limit($limit)
            ->offset($offset)
            ->execute()
            ->as_array();
        
        return [
            'total' => $total,
            'results' => $results,
            'limit' => $limit,
            'offset' => $offset
        ];
    }
}
