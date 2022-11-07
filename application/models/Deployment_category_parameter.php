<?php
class Deployment_category_parameter extends MY_Model {
    const DB_TABLE = 'deployment_category_parameters';
    const DB_TABLE_PK = 'id';
    public $category_parameter_id;
    public $deployment_id;
    public $answer;
    public $description;

    public function category_parameter(){
        $this->load->model('category_parameter');
        $category_parameter = new Category_parameter();
        $category_parameter->load($this->category_parameter_id);
        return $category_parameter;
    }

    public function deployment(){
        $this->load->model('deployment');
        $deployment = new Deployment();
        $deployment->load($this->deployment_id);
        return $deployment;
    }
}