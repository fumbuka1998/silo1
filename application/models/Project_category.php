<?php

class Project_category extends MY_Model{
    
    const DB_TABLE = 'project_categories';
    const DB_TABLE_PK = 'category_id';

    public $category_name;
    public $description;

    public function projects($with_closed = false){
        $this->load->model(['project']);
        $where = 'category_id ='.$this->{$this::DB_TABLE_PK};
        if($with_closed){
            $where .= ' AND project_id NOT IN( SELECT project_id FROM project_closures)';
        }
        return $this->project->get(0,0,$where,'project_name');
    }

    public function category_options()
    {
        $options[''] = '&nbsp;';
        $categories = $this->get(0,0,'','category_name');
        foreach($categories as $category){
            $options[$category->{$category::DB_TABLE_PK}] = $category->category_name;
        }
        return $options;
    }

    public function project_categories_list($limit, $start, $keyword, $order){
        //Order String
        $order_string = dataTable_order_string(['category_name','description'],$order,'category_name');


        $where = '';
        if($keyword != ''){
            $where .= 'category_name LIKE "%'.$keyword.'%"  OR description LIKE "%'.$keyword.'%" ';
        }

        $categories = $this->get($limit,$start,$where,$order_string);
        $rows = [];
        foreach($categories as $category){
            $data['category'] = $category;
            $rows[] = [
                $category->category_name,
                $category->description,
                $this->load->view('projects/settings/project_categories_list_actions',$data,true)
            ];
        }
        $records_filtered = $this->count_rows($where);
        $records_total = $this->count_rows();
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

}

