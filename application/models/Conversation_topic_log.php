<?php
/**
 * Created by PhpStorm.
 * User: userx
 * Date: 2/4/20
 * Time: 9:57 AM
 */

class Conversation_topic_log extends MY_Model{
    const DB_TABLE = 'conversation_topic_logs';
    const DB_TABLE_PK = 'id';

    public $log_type;
    public $log_details;
    public $datetime_post;
}

?>