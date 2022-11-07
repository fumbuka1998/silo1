<?php
class Deployment_person extends MY_Model {
    const DB_TABLE = 'deployment_persons';
    const DB_TABLE_PK = 'id';
    public $deployment_id;
    public $name;

    public function deployment(){
        $this->load->model('deployment');
        $deployment = new Deployment();
        $deployment->load($this->deployment_id);
        return $deployment;
    }
}