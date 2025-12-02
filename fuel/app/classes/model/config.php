<?php

class Model_Config extends \Orm\Model
{
	protected static $_properties = array(
		'id',                          // ID AUTOINCREMENTAL DEL REGISTRO
		'name',                        // NOMBRE DE LA EMPRESA
		'rfc',                         // RFC DE LA EMPRESA
		'cp',                          // CÓDIGO POSTAL DE LA EMPRESA
		'id_sat_tax_regimes',          // ID DEL RÉGIMEN FISCAL SAT
		'invoice_receive_days',        // DÍAS EN QUE SE RECIBEN FACTURAS
		'invoice_receive_limit_time',  // HORA LÍMITE PARA RECIBIR FACTURAS
		'payment_days',                // DÍAS DE PAGO A PROVEEDORES
		'payment_terms_days',          // DÍAS HÁBILES PARA EL PAGO DESPUÉS DE RECIBIR LA FACTURA
		'contact_email',               // CORREO ELECTRÓNICO DE CONTACTO
		'contact_phone',               // TELÉFONO DE CONTACTO DE LA EMPRESA
		'announcement_message',        // MENSAJE O AVISO GENERAL
		'blocked_reception',           // 1 = RECEPCIÓN BLOQUEADA, 0 = ACTIVA
		'holidays',                    // DÍAS FERIADOS (FECHAS SEPARADAS POR COMA O SERIALIZADO)
		'policy_file',                 // ARCHIVO PDF DE POLÍTICAS
		'created_at',                  // FECHA Y HORA DE CREACIÓN
		'updated_at'                   // FECHA Y HORA DE ÚLTIMA ACTUALIZACIÓN
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events'          => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events'          => array('before_save'),
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'config';

	protected static $_primary_key = array('id');

	protected static $_belongs_to = array(
		'sat_tax_regime' => array(
        'key_from'		 => 'id_sat_tax_regimes',
        'model_to' 		 => 'Model_Sat_Tax_Regime',
        'key_to'		 => 'id',
        'cascade_save' 	 => false,
        'cascade_delete' => false,
    )
	
	);

	protected static $_has_one = array(
		
	);
}
