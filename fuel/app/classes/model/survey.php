<?php
class Model_Survey extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'session_id',
        'ip',
        'survey_code',
        'name',
        'email',
        'rating',
        'ratingventa',
        'ratingsurtido',
        'ratingentrega',
        'recomienda',
        'comment',
        'created_at',
        'updated_at',
    );

    protected static $_table_name = 'surveys';
}
