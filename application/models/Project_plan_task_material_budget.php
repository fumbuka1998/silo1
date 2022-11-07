<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/3/2018
 * Time: 11:31 AM
 */

class Project_plan_task_material_budget extends MY_Model{
    const DB_TABLE = 'project_plan_task_material_budgets';
    const DB_TABLE_PK = 'id';

    public $project_plan_task_id;
    public $material_item_id;
    public $quantity;
    public $rate;
    public $created_by;

    public function material_item()
    {
        $this->load->model('material_item');
        $material_item = new Material_item();
        $material_item->load($this->material_item_id);
        return $material_item;
    }

    public function project_plan_task()
    {
        $this->load->model('project_plan_task');
        $project_plan_task = new Project_plan_task();
        $project_plan_task->load($this->project_plan_task_id);
        return $project_plan_task;
    }

    public function plan_material_budget_list($limit,$start,$keyword,$order,$project_plan_id){
        $where='WHERE project_plan_tasks.project_plan_id = '.$project_plan_id.'';

        $sql = 'SELECT COUNT(project_plan_task_material_budgets.id) AS records_total FROM project_plan_task_material_budgets 
                LEFT JOIN project_plan_tasks ON project_plan_task_material_budgets.project_plan_task_id = project_plan_tasks.id
              '.$where;

        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;

        if ($keyword!=''){
            if($where != ''){
                $where .= ' AND ';
            }
            $where.=' (project_plan_task_material_budgets.id LIKE "%'.$keyword.'%" OR tasks.task_name LIKE "%'.$keyword.'%" OR material_items.item_name LIKE "%'.$keyword.'%"  OR project_plan_task_material_budgets.quantity LIKE "%'.$keyword.'%"  OR project_plan_task_material_budgets.rate LIKE "%'.$keyword.'%" ) ';
        }
        $order_string = dataTable_order_string(['project_plan_task_material_budgets.id','tasks.task_name','material_items.item_name','project_plan_task_material_budgets.quantity','project_plan_task_material_budgets.rate'],$order,'project_plan_task_material_budgets.id ASC');

        $sql = 'SELECT SQL_CALC_FOUND_ROWS project_plan_task_material_budgets.id AS no, tasks.task_name AS plan_task_name, material_items.item_name AS material_item_name, project_plan_task_material_budgets.quantity AS quantity, project_plan_task_material_budgets.rate AS rate FROM project_plan_task_material_budgets
                LEFT JOIN project_plan_tasks ON project_plan_task_material_budgets.project_plan_task_id = project_plan_tasks.id
                LEFT JOIN tasks ON project_plan_tasks.task_id = tasks.task_id
                LEFT JOIN material_items ON project_plan_task_material_budgets.material_item_id = material_items.item_id
                '.$where.' ORDER BY '.$order_string.' LIMIT '.$limit.' OFFSET '.$start;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];
        foreach($results as $row){
            $plan_material_budget = new self();
            $plan_material_budget->load($row->no);
            $data['plan_material_budget'] = $plan_material_budget;
            $rate = $row->rate;
            $quantity = $row->quantity;

            $rows[] = [
                wordwrap($row->plan_task_name,75,'<br/>'),
                wordwrap($row->material_item_name,35,'<br/>'),
                $plan_material_budget->material_item()->unit()->symbol,
                $quantity,
                '<span class="pull-right">'.number_format($rate).'</span>',
                '<span class="pull-right">'.number_format($rate * $quantity).'</span>',
                $this->load->view('projects/plans/project_plan_task_materials/list_actions',$data,true)
            ];
        }

        $json = [
            "total_budget_amount" => $this->total_plan_material_budget($project_plan_id),
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);

    }

    public function total_plan_material_budget($project_plan_id, $project_plan_task_id = null, $level = null)
    {
        $sql = 'SELECT COALESCE(SUM(project_plan_task_material_budgets.rate * project_plan_task_material_budgets.quantity),0) AS total_budget_amount FROM project_plan_task_material_budgets
                LEFT JOIN project_plan_tasks ON project_plan_task_material_budgets.project_plan_task_id = project_plan_tasks.id
                WHERE project_plan_tasks.project_plan_id ='.$project_plan_id;
            if($level == 'task'){
              $sql .= ' AND project_plan_task_id ="'.$project_plan_task_id.'" ';
            }

        $query = $this->db->query($sql);
        return doubleval($query->row()->total_budget_amount);
    }
}