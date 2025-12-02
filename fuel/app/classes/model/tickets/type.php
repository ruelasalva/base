<?php
class Model_Tickets_Type extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'name',
        'created_at',
        'updated_at',
    );

    protected static $_table_name = 'tickets_type';

}
