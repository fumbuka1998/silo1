<?php

class Payment_voucher_item_cost_center extends MY_Model{
    
    const DB_TABLE = 'payment_voucher_item_cost_centers';
    const DB_TABLE_PK = 'cost_center_id';

    public $payment_voucher_item_id;
    public $project_id;
    public $task_id;
    public $department_id;

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

    public function department()
    {
        $this->load->model('department');
        $department = new Department();
        $department->load($this->department_id);
        return $department;
    }

    public function cost_figure($cost_center_id,$cost_center_type,$from = null, $to = null){
        $sql = 'SELECT COALESCE(SUM(amount),0) AS cost_figure FROM payment_voucher_items
                LEFT JOIN payment_voucher_item_cost_centers ON payment_voucher_items.payment_voucher_item_id = payment_voucher_item_cost_centers.payment_voucher_item_id
                LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                WHERE 
                ';
        if($cost_center_type == 'department'){
            $sql .= ' department_id = "'.$cost_center_id.'" ';
        } else if($cost_center_type == 'project'){
            $sql .= ' project_id = "'.$cost_center_id.'" AND task_id IS NULL ';
        } else if($cost_center_type == 'activity'){
            $sql .= ' task_id IN (SELECT task_id FROM tasks WHERE activity_id = "'.$cost_center_id.'")';
        } else if($cost_center_type == 'task'){
            $sql .= ' task_id  = "'.$cost_center_id.'" ';
        } else {
            $sql .= ' project_id = "'.$cost_center_id.'" ';
        }

        if($from != null){
            $sql .= ' AND payment_date >= "'.$from.'" ';
        }

        if($to != null){
            $sql .= ' AND payment_date <= "'.$to.'" ';
        }
        $query = $this->db->query($sql);
        return $query->row()->cost_figure;
        
    }

}

