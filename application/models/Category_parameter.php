<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 11/27/2019
 * Time: 11:35 AM
 */

class Category_parameter extends MY_Model {
    const DB_TABLE = 'category_parameters';
    const DB_TABLE_PK = 'id';
    public $name;
    public $description;
    public $category_id;
    public $created_by;

    public function created_by(){
        $this->load->model('employee');
        $created_by = new Employee();
        $created_by->load($this->created_by);
        return $created_by;
    }

    public function category(){
        $this->load->model('category');
        $category = new Category();
        $category->load($this->category_id);
        return $category;
    }

    public function dropdown_options($nbsp = false){
        $categorie_parameters = $this->get();
        $options = [];
        if ($nbsp) {
            $options[''] = '&nbsp;';
        }
        foreach ($categorie_parameters as $parameter){
            $options[$parameter->{$parameter::DB_TABLE_PK}] = $parameter->name;
        }
        return $options;

    }

    public function category_parameters_list($category_id,$limit, $start, $keyword, $order){

        $order_string = dataTable_order_string(['name'],$order,'name');

        $where = 'category_id = '.$category_id;
        $records_total = $this->count_rows($where);
        if($keyword != ''){
            $where = ' name LIKE "%'.$keyword.'%" ';
        }

        $category_parameters = $this->get($limit, $start, $where,$order_string);
        $rows = array();
        foreach ($category_parameters as $parameter) {
            $data['parameter'] = $parameter;
            $rows[] = array(
                $parameter->name,
                $parameter->description,
                $parameter->created_by()->full_name(),
                $this->load->view('hse/settings/profile/parameters/parameters_list_actions',$data,true)
            );
        }
        $data['data']=$rows;
        $data['recordsFiltered']=$this->count_rows($where);
        $data['recordsTotal']= $records_total;
        return json_encode($data);

    }

    public function fetch_data($query,$category_id)
    {
        $this->db->select("*");
        $this->db->from("category_parameters");
        if($query != '')
        {
            $this->db->like('name', $query);
        }
        $this->db->where('category_id', $category_id);
        $this->db->order_by('id', 'DESC');
        return $this->db->get();
    }

    // TODO: Category id should be arraged a varibale not hardcoded 3;
    public function deployment_dropdown_options($nbsp = false,$category_id = 3){
        $categorie_parameters = $this->get(0,0,['category_id' => $category_id]);
        $options = [];
        if ($nbsp) {
            $options[''] = '&nbsp;';
        }
        foreach ($categorie_parameters as $parameter){
            $options[$parameter->{$parameter::DB_TABLE_PK}] = $parameter->name;
        }
        return $options;

    }

    public function deployment_category_parameters($category_id = 3){
        $this->db->select("*");
        $this->db->from("category_parameters");
        $this->db->where('category_id', $category_id);
        $this->db->order_by('id', 'ASC');
        return $this->db->get();
    }

    public function parameter_types(){
        $this->load->model('parameter_type');
        return $this->parameter_type->get(0,0,['category_parameter_id' => $this->{$this::DB_TABLE_PK}]);
    }

}