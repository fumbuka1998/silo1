<?php
/**
 * Created by PhpStorm.
 * User: userx
 * Date: 2/4/20
 * Time: 9:48 AM
 */

class Topic_conversation extends MY_Model{
    const DB_TABLE = 'topic_conversations';
    const DB_TABLE_PK = 'id';

    public $topic_id;
    public $email;
    public $phone;
    public $sender;
    public $recipient;
    public $type;
    public $message;
    public $is_read;
    public $created_at;


    public function sender(){
        $this->load->model('employee');
        $sender = new Employee();
        $sender->load($this->sender);
        return $sender;
    }

    public function recipient(){
        $this->load->model('employee');
        $recipient = new Employee();
        $recipient->load($this->recipient);
        return $recipient;
    }


}

