<?php

class Miscellaneous_cost extends MY_Model{
    
    const DB_TABLE = 'miscellaneous_costs';
    const DB_TABLE_PK = 'cost_id';

    public $cost_date;
    public $project_id;
    public $task_id;
    public $budget_id;
    public $cost_item_name;
    public $quantity;
    public $rate;
    public $description;
    public $employee_id;

    public function project_costs_list($project_id){
        $limit = $this->input->post('length');
        $keyword = $this->input->post('search')['value'];
        $start = $this->input->post('start');

        $order_column = $this->input->post('order')[0]['column'];
        $order_dir = $this->input->post('order')[0]['dir'];
        switch ($order_column) {
            case 0;
                $order_column = 'cost_date';
                break;
            case 1;
                $order_column = 'cost_item_name';
                break;
            case 2;
                $order_column = 'quantity';
                break;
            case 3;
                $order_column = 'rate';
                break;
            case 5;
                $order_column = 'description';
                break;
            case 6;
                $order_column = 'employee_name';
                break;
            default:
                $order_column = 'cost_date';
        }

        $order_string = $order_column.' '.$order_dir;

        $sql = 'SELECT cost_id,cost_date,cost_item_name,quantity,rate,project_miscellaneous_costs.description,(quantity*rate) AS amount,
                employees.employee_id, CONCAT(first_name," ",middle_name," ",last_name) as employee_name
                FROM project_miscellaneous_costs
                LEFT JOIN miscellaneous_budgets ON project_miscellaneous_costs.budget_id = miscellaneous_budgets.budget_id
                LEFT JOIN employees ON project_miscellaneous_costs.employee_id = employees.employee_id
                WHERE miscellaneous_budgets.project_id = "'.$project_id.'"
            ';

        $query = $this->db->query($sql);
        $records_total = $query->num_rows();

        if($keyword != ''){
            $sql .= ' AND (cost_date LIKE "%'.$keyword.'%" OR cost_item_name LIKE "%'.$keyword.'%" OR quantity = "'.$keyword.'" OR rate = "'.$keyword.'"
              OR project_miscellaneous_costs.description LIKE "%'.$keyword.'%" OR employees.first_name LIKE "%'.$keyword.'%" OR employees.middle_name LIKE "%'.$keyword.'%" OR employees.last_name LIKE "%'.$keyword.'%" )';
        }

        $query = $this->db->query($sql);
        $records_filtered = $query->num_rows();

        $sql .= " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $query = $this->db->query($sql);

        $results = $query->result();
        $rows = [];

        foreach($results as $row){
            $cost_item = new Miscellaneous_cost();
            $cost_item->load($row->cost_id);
            $data['cost_item'] = $cost_item;
            if(!isset($data['project'])){
                $data['project'] = $cost_item->project();
                $data['miscellaneous_categories'] = $data['project']->miscellaneous_categories();
            }
            $rows[] = [
                custom_standard_date($row->cost_date),
                $row->cost_item_name,
                $row->quantity,
                '<span class="pull-right">'.number_format($row->rate).'</span>',
                '<span class="pull-right">'.number_format($row->amount).'</span>',
                $row->description,
                check_permission('Human Resources') ? anchor(base_url('human_resources/employee_profile/'.$row->employee_id),$row->employee_name) : $row->employee_name,
                $this->load->view('projects/costs/miscellaneous_costs_list_actions',$data,true)
            ];
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function actual_cost(){
        return 0;
    }

    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

    public function task()
    {
        $this->load->model('task');
        $task = new Task();
        $task->load($this->task_id);
        return $task;
    }

}

