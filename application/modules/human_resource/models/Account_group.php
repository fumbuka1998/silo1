<?php

class Account_group extends MY_Model{
    
    const DB_TABLE = 'account_groups';
    const DB_TABLE_PK = 'account_group_id';

    public $group_name;
    public $description;
    public $parent_id;
    public $group_nature_id;
    public $level;

    public function parent_group(){
        $parent = new self;
        $parent->load($this->parent_id);
        return $parent;
    }

    public function group_nature()
    {
        $group_nature = new self;
        $group_nature->load($this->group_nature_id);
        return $group_nature;
    }

    public function account_group_options($group_natures = 'all'){
        $sql = 'SELECT account_groups.account_group_id,account_groups.group_name FROM account_groups 
              
                ';
        if($group_natures != 'all' && !empty($group_natures)){
            $nature_names = '';
            foreach ($group_natures as $group_nature){
                $nature_names .= '"'.$group_nature.'",';
            }
            $nature_names = rtrim($nature_names,', ');
            $sql .= ' LEFT JOIN account_groups nature ON account_groups.account_group_id = nature.group_nature_id
                WHERE nature.group_name IN('.$nature_names.')';
        }

        $query = $this->db->query($sql);
        $results = $query->result();
        $options[''] = '&nbsp;';
        foreach ($results as $account_group){
            $options[$account_group->account_group_id] = $account_group->group_name;
        }
        return $options;
    }

    public function account_groups_list($limit, $start, $keyword, $order){

        //order string
        $order_string = dataTable_order_string(['group_name','','description'],$order,'group_name');
        
        $where = '';
        if($keyword != ''){
            $where .= 'group_name LIKE "%'.$keyword.'%" OR description LIKE "%'.$keyword.'%" ';
        }

        $account_groups = $this->get($limit,$start,$where,$order_string);
        $rows = [];

        $this->load->model('account_group');
        $data['account_group_options'] = $this->account_group->account_group_options();
        foreach($account_groups as $account_group){
            $data['account_group'] = $account_group;
            $rows[] = [
                $account_group->group_name,
                $account_group->parent_group()->group_name,
                $account_group->description,
                $this->load->view('finance/settings/account_groups_list_actions',$data,true)
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

    public function account_group_details($account_group_id)
    {
        $account_group = new Account_group();
        $account_group->load($account_group_id);
        return $account_group;
    }

    public function account_group_selection($group_name)
    {
        $sql = 'SELECT * FROM account_groups WHERE group_name LIKE "%'.$group_name.'%"';
        $query = $this->db->query($sql);
        $results = $query->result();
        return $results ? $results[0]->account_group_id : '';

    }

}

