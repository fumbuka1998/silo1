<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 1/8/2020
 * Time: 4:04 PM
 */
class Inspection_category extends MY_Model {
    const DB_TABLE = 'inspection_categories';
    const DB_TABLE_PK = 'id';
    public $category_id;
    public $inspection_id;

    public function category(){
        $this->load->model('category');
        $category = new Category();
        $category->load($this->category_id);
        return $category;
    }

    public function category_parameters(){
        $sql = 'SELECT category_parameters.name as name,inspection_category_id FROM inspection_category_parameters
                 LEFT JOIN category_parameters ON inspection_category_parameters.category_parameter_id = category_parameters.id
                 WHERE inspection_category_parameters.inspection_category_id ='.$this->{$this::DB_TABLE_PK} ;
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function inspection(){
        $this->load->model('inspection');
        $inspection = new Inspection();
        $inspection->load($this->inspection_id);
        return $inspection;
    }

    public function inspection_category_parameters(){
        $this->load->model('inspection_category_parameter');
        return $this->inspection_category_parameter->get(0,0,['inspection_category_id' => $this->{$this::DB_TABLE_PK}]);
    }


}