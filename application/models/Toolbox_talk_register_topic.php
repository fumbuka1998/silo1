<?php
class Toolbox_talk_register_topic extends MY_Model {
    const DB_TABLE = 'toolbox_talk_register_topics';
    const DB_TABLE_PK = 'id';
    public $toolbox_talk_register_id;
    public $topic_id;

    public function toolbox_talk_register(){
        $this->load->model('toolbox_talk_register');
        $toobox = new Toolbox_talk_register();
        $toobox->load($this->toolbox_talk_register_id);
        return $toobox;
    }

    public function topic(){
        $this->load->model('site_topic');
        $topic = new Site_topic();
        $topic->load($this->topic_id);
        return $topic;
    }

}