<?php

class Material_item_category extends MY_Model{
    
    const DB_TABLE = 'material_item_categories';
    const DB_TABLE_PK = 'category_id';

    public $category_name;
    public $description;
    public $parent_category_id;
    public $project_nature_id;
    public $tree_level;


    public function category_list(){
        $limit = $this->input->post('length');
        $keyword = $this->input->post('search')['value'];
        $start = $this->input->post('start');

        $order_column = $this->input->post('order')[0]['column'];
        $order_dir = $this->input->post('order')[0]['dir'];
        switch ($order_column) {
            case 0;
                $order_column = 'category_name';
                break;
            case 2;
                $order_column = 'description';
                break;
            case 3;
                $order_column = 'number_of_items';
                break;
            default:
                $order_column = 'category_name';
        }

        $order_string = $order_column.' '.$order_dir;

        $sql = 'SELECT material_item_categories.*,parent_table.category_name AS parent_name,
                (
                  SELECT COALESCE (COUNT(item_id),0)
                  FROM material_items
                  WHERE material_items.category_id = material_item_categories.category_id
                ) AS number_of_items
                FROM material_item_categories
                LEFT JOIN material_item_categories AS parent_table ON material_item_categories.parent_category_id = parent_table.category_id
            ';

        if($keyword != ''){
            $sql .= ' WHERE material_item_categories.category_name LIKE "%'.$keyword.'%" OR parent_table.category_name LIKE "%'.$keyword.'%"  OR material_item_categories.description LIKE "%'.$keyword.'%" ';
        }

        $query = $this->db->query($sql);
        $records_filtered = $query->num_rows();

        $sql .= " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $query = $this->db->query($sql);
        $records_total = $this->db->count_all('material_item_categories');

        $results = $query->result();
        $rows = [];

        $this->load->model('material_item_category');

        $data['material_category_options'] = [];

        foreach($results as $row){
            $category = new Material_item_category();
            $category->load($row->category_id);
            $data['category'] = $category;
            $data['number_of_categories'] = $row->number_of_items;
            $rows[] = [
                $row->category_name,
                $row->parent_name,
                $row->description,
                $row->number_of_items,
                $this->load->view('inventory/settings/material_item_categories_list_actions',$data,true)
            ];
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        echo json_encode($json);
    }

    public function dropdown_options($project_nature_id = null)
    {
        $options[''] = '&nbsp;';
        if($project_nature_id == 'unnatured'){
            $where = ' project_nature_id IS NULL ';
        } else if(!is_null($project_nature_id)){
            $where = [
                'project_nature_id' => $project_nature_id
            ];
        } else {
            $where = '';
        }
        $categories = $this->get(0,0,$where,'category_name');
        foreach($categories as $category){
            $options[$category->{$category::DB_TABLE_PK}] = $category->category_name;
        }
        return $options;
    }

    public function parent_category(){
        $category = new self;
        $category->load($this->parent_category_id);
        return $category;
    }

    public function material_items(){
        $where['category_id'] = $this->{$this::DB_TABLE_PK};
        $this->load->model('material_item');
        return $this->material_item->get(0,0,$where);
    }

    public function material_options(){
        $sql = 'SELECT item_name, item_id FROM material_items WHERE category_id = "'.$this->{$this::DB_TABLE_PK}.'"';
        $query = $this->db->query($sql);
        $material_items = $query->result();
        $options = [];
        foreach($material_items as $material_item){
            $options[$material_item->item_id] = $material_item->item_name;
        }
        return $options;
    }

    public function update_children(){
        $children = $this->children();
        foreach ($children as $child){
            $child->tree_level = $child->parent_category()->tree_level+1;
            $child->save();
            $child->update_children();
        }
    }

    public function accessible_parents(){
        //Get loop counts which is the maximum tree_level
        $sql = 'SELECT MAX(tree_level) AS tree_level FROM material_item_categories';
        $query = $this->db->query($sql);
        $loop_count = $query->row()->tree_level - $this->tree_level;

        //Get The children of the current_group
        $sql = 'SELECT category_id FROM material_item_categories WHERE parent_category_id = "'.$this->{$this::DB_TABLE_PK}.'"';
        $query = $this->db->query($sql);
        $results = $query->result();
        $parent_level_ids = '0, ';
        $children_ids = $this->{$this::DB_TABLE_PK}.', ';
        foreach ($results as $row){
            $parent_level_ids .= $row->category_id.', ';
            $children_ids .= $row->category_id.', ';
        }
        
        $parent_level_ids = rtrim($parent_level_ids,', ');

        for ($i = 1; $i < $loop_count; $i++ ){
            $sql = 'SELECT category_id FROM material_item_categories WHERE parent_category_id IN ('.$parent_level_ids.')';
            $query = $this->db->query($sql);
            $results = $query->result();
            $parent_level_ids = '0, ';
            foreach ($results as $row){
                $parent_level_ids .= $row->category_id.', ';
                $children_ids .= $row->category_id.', ';
            }
            $parent_level_ids = rtrim($parent_level_ids,', ');
        }

        $children_ids = rtrim($children_ids,', ');
        return $this->get(0,0,' category_id NOT IN('.$children_ids.') ');
    }

    public function children(){
        return $this->get(0,0,['parent_category_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function maximum_level_reached(){
        $sql = 'SELECT MAX(tree_level) AS maximum_level FROM material_item_categories';
        $query = $this->db->query($sql);
        return $query->row()->maximum_level;
    }

    public function add_children_to_category_repository($category_repository = []){
        $children = $this->children();
        $category_repository = array_merge($category_repository,$children);
        if(count($children) > 0){
            foreach ($children as $child){
                $child->add_children_to_category_repository($category_repository);
            }
        }
    }

}

