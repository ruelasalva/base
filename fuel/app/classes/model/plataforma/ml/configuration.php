<?php

/**
 * MODELO DE CONFIGURACIÓN DE MERCADO LIBRE
 * Controla credenciales, tokens y estado de sincronización.
 */
class Model_Plataforma_Ml_Configuration extends \Orm\Model
{
    protected static $_table_name = 'plataforma_ml_configurations';

    protected static $_properties = array(
        'id',
        'name',
        'client_id',
        'client_secret',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'user_id_ml',
        'redirect_uri',
        'mode',
        'is_active',
        'expires_in_last',
        'account_email',
        'last_sync_catalog',
        'last_sync_orders',
        'last_sync_promotions',
        'last_sync_webhooks',
        'created_at',
        'updated_at',
    );

    protected static $_observers = array(
        'Orm\\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
        ),
        'Orm\\Observer_UpdatedAt' => array(
            'events' => array('before_update'),
            'mysql_timestamp' => false,
        ),
    );

    protected static $_has_many = array(
        'stores' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Plataforma_Ml_Store',
            'key_to' => 'configuration_id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
        'products' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Plataforma_Ml_Product',
            'key_to' => 'configuration_id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );

    /**
     * Verifica si el token está vencido o no configurado.
     */
    public function token_is_expired()
    {
        if (empty($this->access_token) || empty($this->token_expires_at)) {
            return true;
        }

        // Margen de seguridad de 60 segundos
        return (time() >= ((int) $this->token_expires_at - 60));
    }
}
