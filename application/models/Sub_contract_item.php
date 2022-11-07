<?php

class Sub_contract_item extends MY_Model{

    const DB_TABLE = 'sub_contracts_items';
    const DB_TABLE_PK = 'id';

    public $sub_contract_id;
    public $start_date;
    public $end_date;
    public $contract_sum;
    public $vat_inclusive;
    public $vat_percentage;
    public $description;
    public $task_id;


    public function sub_contract()
    {
        $this->load->model('Sub_contract');
        $sub_contract = new Sub_contract();
        $sub_contract->load($this->sub_contract_id);
        return $sub_contract;
    }

    public function task()
    {
        $this->load->model('task');
        $task = new Task();
        $task->load($this->task_id);
        return $task;
    }

    public function actual_cost($cost_center_id, $level = null, $from = null, $to = null){
        $sql = 'SELECT COALESCE(SUM(contract_sum),0) AS total_sub_contract_cost FROM sub_contracts_items
                LEFT JOIN sub_contracts ON sub_contracts_items.sub_contract_id = sub_contracts.id
                WHERE ';

        if(!is_null($level) && $level == 'project') {
            $sql  .= ' sub_contracts.project_id = "' . $cost_center_id . '" AND task_id IS NULL';
        } else if(!is_null($level) && $level == 'task'){
            $sql .= ' sub_contracts_items.task_id = "' . $cost_center_id . '"';
        } else if(!is_null($level) && $level == 'activity'){
            $sql .= ' sub_contracts_items.task_id IN (SELECT task_id FROM tasks WHERE activity_id = "'.$cost_center_id.'")';
        } else {
            $sql .= ' sub_contracts.project_id = "' . $cost_center_id . '" ';
        }

        if(!is_null($from)){
            $sql .= ' AND start_date >= "'.$from.'" ';
        }

        if(!is_null($to)){
            $sql .= ' AND end_date <= "'.$to.'" ';
        }

        $query = $this->db->query($sql);
        return doubleval($query->row()->total_sub_contract_cost);
    }

    public function sub_contracts_list_table($sub_contract_id,$limit,$start,$keyword,$order){
        $this->load->model('sub_contract');
        $sub_contract = new Sub_contract();
        $sub_contract->load($sub_contract_id);

        $order_string = dataTable_order_string(['task_id','start_date','end_date','description','contract_sum'],$order,'task_id');
        $order_string = " ORDER BY ".$order_string. " LIMIT ".$limit. " OFFSET ".$start;

        $where = 'sub_contract_id= "'.$sub_contract_id.'"';
        $records_total = $this->count_rows($where);
        if($keyword != ''){
            $where .= 'task_id LIKE "%'.$keyword.'%" OR start_date LIKE "%'.$keyword.'%" OR end_date LIKE "%'.$keyword.'%"OR description LIKE "%'.$keyword.'%"OR contract_sum LIKE "%'.$keyword.'%" ';
        }
        $sql = 'SELECT sub_contracts_items.id,task_id,start_date,end_date,description,contract_sum FROM sub_contracts_items
                WHERE '.$where.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;
        $rows = [];

        foreach($results as $row){
            $sub_contract_item = new self();
            $sub_contract_item->load($row->id);
            $data['sub_contract_item_id'] = $sub_contract_item->{$sub_contract_item::DB_TABLE_PK};
            $rows[] = [
                $row->task_id ? wordwrap($sub_contract_item->task()->task_name,75,'<br/>') : 'Project Shared',
                custom_standard_date($row->start_date),
                custom_standard_date($row->end_date),
                wordwrap($row->description,90,'<br/>'),
                '<span class="pull-right">'.number_format($row->contract_sum).' '.$sub_contract_item->is_vat_iclusive().'</span>',
                $this->load->view('projects/sub_contracts/sub_contract_items/sub_contract_item_list_action',$data,true)

            ];
        }

        $json = [
            "sub_contract_total" => $sub_contract->sub_contract_amount(),
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        echo json_encode($json);
    }

    public function is_vat_iclusive(){
        return $this->vat_inclusive == 1 ? "VAT+" : "";
    }

}

