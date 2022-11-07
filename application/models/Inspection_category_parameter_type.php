<?php
class Inspection_category_parameter_type extends MY_Model {
    const DB_TABLE = 'inspection_category_parameter_types';
    const DB_TABLE_PK = 'id';
    public $inspection_category_parameter_id;
    public $parameter_type_id;
    public $is_checked;

    public function inspection_category_parameter(){
        $this->load->model('inspection_category_parameter');
        $inspection_category_parameter = new Inspection_category_parameter();
        $inspection_category_parameter->load($this->inspection_category_parameter_id);
        return $inspection_category_parameter;
    }

    public function parameter_type(){
        $this->load->model('parameter_type');
        $parameter_type = new Parameter_type();
        $parameter_type->load($this->parameter_type_id);
        return $parameter_type;
    }
}