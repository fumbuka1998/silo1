<?php

class Asset_group extends MY_Model{
    
    const DB_TABLE = 'asset_groups';
    const DB_TABLE_PK = 'id';

    public $group_name;
    public $description;
    public $parent_id;
    public $group_nature_id;
    public $level;
    public $created_by;
    public $created_at;

    public function parent()
    {
        $parent = new self();
        $parent->load($this->parent_id);
        return $parent;
    }

    public function children_groups(){
        
        return $this->get(0,0,['parent_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function asset_group_list($limit, $start, $keyword, $order){
        $records_total = $this->count_rows();

        $where = '';
        if($keyword != ''){
            $where .= 'group_name LIKE "%'.$keyword.'%" OR description LIKE "%'.$keyword.'%" ';
        }

        //order string
        $order_string = dataTable_order_string(['group_name','description'],$order,'group_name');

        $groups = $this->get($limit,$start,$where,$order_string);
        $rows = [];
        $data['parent_group_options']  = $this->asset_group_options();
        foreach ($groups as $group){
            $data['group'] = $group;
            $rows[] = [
                $group->group_name,
                $group->description,
                $this->load->view('settings/asset_group/asset_group_actions',$data,true)
            ];
        }

        $records_filtered = $this->count_rows($where);

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }


   public function asset_groups_list(){

        $groups = $this->get();
        return $groups;

     }

    public function asset_group_options(){

        $groups = $this->get();
        $options[''] = '&nbsp;';
        foreach ($groups as $group){
            $options[$group->{$this::DB_TABLE_PK}] = $group->group_name;
        }
        
        return $options;
     }


    public function accessible_parents(){

        $sql = 'SELECT MAX(level) AS tree_level FROM asset_groups';
        $query = $this->db->query($sql);
        //$loop_count = $query->row()->tree_level - $this->tree_level;
        $loop_count = $query->row()->tree_level;

        $sql = 'SELECT id FROM asset_groups WHERE parent_id = "'.$this->{$this::DB_TABLE_PK}.'"';
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
            $sql = 'SELECT id FROM asset_groups WHERE parent_id IN ('.$parent_level_ids.')';
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


        return $this->get(0,0,' id NOT IN('.$children_ids.') ');
    }

     public function asset_depreciation_rate_items(){

        $this->load->model('Asset_depreciation_rate_item');
        $Asset_depreciation_rate_item = new Asset_depreciation_rate_item();
        $Asset_depreciation_rate_item->load($this::DB_TABLE_PK);
        return $Asset_depreciation_rate_item;
     }

     public function asset_dropdown_options(){
        $this->load->model('asset_register/Asset');
        $assets=$this->Asset->get(0,0,['asset_group_id'=>$this->{$this::DB_TABLE_PK}]);
        $options ='';
        foreach ($assets as $asset){
            $options .='<option  value="'.$asset->id.'">'.$asset->asset_name.'</option>';
        }
        return $options;
    }

    public function assets_dropdown_options(){

        $this->load->model('asset_register/Asset');
        $assets=$this->Asset->get(0,0,['asset_group_id'=>$this->{$this::DB_TABLE_PK}]);
        foreach ($assets as $asset){
            $options[$asset->{$this::DB_TABLE_PK}] = $asset->asset_name;
        }

        return $options;
    }

       //equipments

     public function equipment_dropdown_options(){
        $this->load->model('asset_register/Hired_equipment');
        $hired_equipments=$this->Hired_equipment->get(0,0,['asset_group_id'=>$this->{$this::DB_TABLE_PK}]);
        $options ='';
        foreach ($hired_equipments as $hired_equipment){
            $options .='<option  value="'.$hired_equipment->id.'">'.$hired_equipment->equipment_code.'</option>';
        }

        return $options;
    }

    public function equipments_dropdown_options(){

        $this->load->model('asset_register/Hired_equipment');
        $hired_equipments=$this->Hired_equipment->get(0,0,['asset_group_id'=>$this->{$this::DB_TABLE_PK}]);
        foreach ($hired_equipments as $hired_equipment){
            $options[$hired_equipment->{$this::DB_TABLE_PK}] = $hired_equipment->equipment_code;
        }

        return $options;
    }



}

