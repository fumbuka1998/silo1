<?php
class Parameter_type extends MY_Model {
    const DB_TABLE = 'parameter_types';
    const DB_TABLE_PK = 'id';
    public $category_parameter_id;
    public $name;
    public $description;

    public function category_parameter(){
        $this->load->model('category_parameter');
        $category_parameter = new Category_parameter();
        $category_parameter->load($this->category_parameter_id);
        return $category_parameter;
    }
}