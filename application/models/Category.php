<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 11/27/2019
 * Time: 10:14 AM
 */

class Category extends MY_Model {
    const DB_TABLE = 'categories';
    const DB_TABLE_PK = 'id';
    public $name;
    public $description;
    public $created_by;

    public function created_by(){
        $this->load->model('employee');
        $created_by = new Employee();
        $created_by->load($this->created_by);
        return $created_by;
    }


    public function category_parameters()
    {
        $this->load->model('category_parameter');
        return $this->category_parameter->get(0, 0, ['category_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function category_parameter(){
        $this->load->model('category_parameter');
        $category_parameter = $this->category_parameter->get(1,0,['category_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($category_parameter) ? array_shift($category_parameter) : '';
    }

    public function dropdown_options($nbsp = false){
        $categories = $this->get();
        $options = [];
        if ($nbsp) {
            $options[''] = '&nbsp;';
        }
        foreach ($categories as $category){
            $options[$category->{$category::DB_TABLE_PK}] = $category->name;
        }
        return $options;

    }

    public function categories_list($limit, $start, $keyword, $order){
        $order_string = dataTable_order_string(['id'],$order,'id');

        $where = '';
        if($keyword != ''){
            $where = ' name LIKE "%'.$keyword.'%" ';
        }

        $categories = $this->get($limit, $start, $where,$order_string);
        $rows = array();
        foreach ($categories as $category) {
            $data['category'] = $category;
            $rows[] = array(
                anchor(base_url('hse/settings_details/'.$category->{$category::DB_TABLE_PK}), $category->name),
                $category->description,
                $category->created_by()->full_name(),
                $this->load->view('hse/settings/categories_list_actions',$data,true)
            );
        }
        $data['data']=$rows;
        $data['recordsFiltered']=$this->count_rows($where);
        $data['recordsTotal']=$this->count_rows();
        return json_encode($data);

    }
}