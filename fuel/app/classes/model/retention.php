<?php

class Model_Retention extends \Orm\Model
{
    protected static $_properties = array(
        "id" => array(
            "label" => "Id",
            "data_type" => "int",
        ),
        "code" => array(
            "label" => "Código",
            "data_type" => "varchar",
        ),
        "description" => array(
            "label" => "Descripción",
            "data_type" => "varchar",
        ),
        "type" => array(
            "label" => "Tipo",
            "data_type" => "varchar",
        ),
        "category" => array(
            "label" => "Categoría",
            "data_type" => "varchar",
            "null" => true,
        ),
        "valid_from" => array(
            "label" => "Vigencia desde",
            "data_type" => "date",
            "null" => true,
        ),
        "base_type" => array(
            "label" => "Tipo de base",
            "data_type" => "varchar",
            "null" => true,
        ),
        "rate" => array(
            "label" => "Tasa",
            "data_type" => "decimal",
            "null" => true,
        ),
        "account" => array(
            "label" => "Cuenta contable",
            "data_type" => "varchar",
            "null" => true,
        ),
        "factor_type" => array(
            "label" => "Tipo de factor",
            "data_type" => "varchar",
            "null" => true,
        ),
        "created_at" => array(
            "label" => "Creado en",
            "data_type" => "int",
            "null" => true,
        ),
        "updated_at" => array(
            "label" => "Actualizado en",
            "data_type" => "int",
            "null" => true,
        ),
    );

	protected static $_table_name = 'retentions';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
	);

}
