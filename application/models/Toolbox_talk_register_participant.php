<?php
class Toolbox_talk_register_participant extends MY_Model {
    const DB_TABLE = 'toolbox_talk_register_participants';
    const DB_TABLE_PK = 'id';
    public $toolbox_talk_register_id;
    public $name;

    public function toolbox_talk_register(){
        $this->load->model('toolbox_talk_register');
        $toobox = new Toolbox_talk_register();
        $toobox->load($this->toolbox_talk_register_id);
        return $toobox;
    }
}