<?php
class Inspection_category_parameter extends MY_Model {
    const DB_TABLE = 'inspection_category_parameters';
    const DB_TABLE_PK = 'id';
    public $inspection_category_id;
    public $category_parameter_id;
    public $remarks;

    public function inspection_category(){
        $this->load->model('inspection_category');
        $inspection_category = new Inspection_category();
        $inspection_category->load($this->inspection_category_id);
        return $inspection_category;
    }

    public function category_parameter(){
        $this->load->model('category_parameter');
        $category_parameter = new Category_parameter();
        $category_parameter->load($this->category_parameter_id);
        return $category_parameter;
    }

    public function inspection_category_parameter_type(){
        $this->load->model('inspection_category_parameter_type');
        return $this->inspection_category_parameter_type->get(0,0,['inspection_category_parameter_id' => $this->{$this::DB_TABLE_PK}]);
    }
}