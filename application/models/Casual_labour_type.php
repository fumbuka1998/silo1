<?php

class Casual_labour_type extends MY_Model{
    
    const DB_TABLE = 'casual_labour_types';
    const DB_TABLE_PK = 'type_id';

    public $name;
    public $description;


    public function labour_types_options()
    {
        $options[''] = '&nbsp;';
        $types = $this->get(0,0,'','name');
        foreach($types as $type){
            $options[$type->{$type::DB_TABLE_PK}] = $type->name;
        }
        return $options;
    }

    public function budget_casual_labour_type_options($cost_center_level,$cost_center_id,$rate_mode){
        $sql = 'SELECT name,type_id
                FROM casual_labour_types
                WHERE type_id NOT IN (
                  SELECT casual_labour_type_id FROM casual_labour_budgets
                  WHERE  rate_mode = "' .$rate_mode.'"';
        if($cost_center_level == 'project'){
            $sql .= ' AND (project_id = "'.$cost_center_id.'" AND task_id IS NULL)';
        } else {
            $sql .= ' AND task_id = "'.$cost_center_id.'"';
        }
        $sql .= '
                )
       ';
        $query = $this->db->query($sql);
        $tool_types = $query->result();

        $options = '<option value="">&nbsp;</option>';
        foreach($tool_types as $type){
            $options .= '<option value="'.$type->type_id.'">'.$type->name.'</option>';
        }

        return $options;
    }


    public function casual_labour_types_list($limit, $start, $keyword, $order){
        $order_column = $order['column'];
        $order_dir = $order['dir'];
        switch ($order_column) {
            case 0;
                $order_column = 'name';
                break;
            case 1;
                $order_column = 'description';
                break;
            default:
                $order_column = 'category_name';
        }

        $order = $order_column.' '.$order_dir;

        $where = '';
        if($keyword != ''){
            $where .= 'name LIKE "%'.$keyword.'%"  OR description LIKE "%'.$keyword.'%" ';
        }

        $types = $this->get($limit,$start,$where,$order);
        $rows = [];
        foreach($types as $type){
            $data['type'] = $type;
            $rows[] = [
                $type->name,
                $type->description,
                $this->load->view('human_resources/settings/casual_labour_types_list_actions',$data,true)
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

