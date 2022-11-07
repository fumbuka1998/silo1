<?php

class Topic_carbon_copy extends MY_Model{
    const DB_TABLE = 'topic_carbon_copies';
    const DB_TABLE_PK = 'id';

    public $topic_id;
    public $email;

}

?>