<?php

class Revision extends MY_Model
{

    const DB_TABLE = 'revision';
    const DB_TABLE_PK = 'id';

    public $revision_date;
    public $project_id;
    public $description;
    public $created_by;

    public function revision_number(){
        return 'REV/'.add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

    public function revised_tasks($total = false){
        $this->load->model('revised_task');
        $where['revision_id'] = $this->{$this::DB_TABLE_PK};
        if($total){
            return $this->revised_task->count_rows($where);
        } else {
            return !empty($this->revised_task->get(0, 0, $where)) ? $this->revised_task->get(0, 0, $where) : false;
        }
    }

    public function revision_cost(){
        $this->load->model('revised_task');
        return $this->revised_task->revision_cost($this->{$this::DB_TABLE_PK});
    }

    public function delete_revised_tasks(){
        $this->db->delete('revised_tasks',['revision_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function revision_list($project_id,$limit, $start, $keyword, $order){
        $revision_where = ' project_id='.$project_id;
        $extension_where = ' project_id='.$project_id;

        if($keyword != ''){
            $revision_where .= ' AND (revision.id LIKE "%'.$keyword.'%" OR revision_date LIKE "%'.$keyword.'%" OR description LIKE "%'.$keyword.'%")';
            $extension_where .= ' AND (project_contract_reviews.id LIKE "%'.$keyword.'%" OR review_date LIKE "%'.$keyword.'%" OR reason LIKE "%'.$keyword.'%")';
        }
        $order_string = dataTable_order_string(['id','date'],$order,'date');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $sql = 'SELECT * FROM (

                    SELECT id FROM revision
                    WHERE  '.$revision_where.'
                    
                    UNION 
                    
                    SELECT id FROM project_contract_reviews
                    WHERE  '.$extension_where.'

                ) AS contract_reviews';

        $query = $this->db->query($sql);
        $records_total = $query->num_rows();


        $sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM (

                    SELECT id, "tasks_revision" AS revision_type, revision_date AS date, description FROM revision
                    WHERE  '.$revision_where.'
                    
                    UNION 
                    
                    SELECT id, "project_extension" AS revision_type, review_date AS date, reason AS description FROM project_contract_reviews
                    WHERE  '.$extension_where.'

                ) AS contract_reviews '.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $this->load->model(['project', 'project_contract_review']);
        $project = new Project();
        $project->load($project_id);
        $data['project'] = $project;
        $rows = [];
        foreach ($results as $row){
            if($row->revision_type == "tasks_revision") {
                $revision = new self();
                $revision->load($row->id);
                $data['revision'] = $revision;
                $rows[] = [
                    custom_standard_date($row->date),
                    "Task(s) Revision",
                    '<span style="text-align: right">' . $row->description . '</span>',
                    $revision->revised_tasks(true),
                    '<span style="text-align: right">' . number_format($revision->revision_cost(), 2) . '</span>',
                    $this->load->view('projects/contract_reviews/list_actions', $data, true)
                ];

            } else {
                $project_contract_review = new Project_contract_review();
                $project_contract_review->load($row->id);
                $data['project_contract_review'] = $project_contract_review;
                if($project_contract_review->duration_type == 'days'){
                    $contract_review = ($project_contract_review->plus_or_minus_duration == 'plus' ? '+' : '-').' '.$project_contract_review->duration_variation.'&nbsp;Day(s)';
                }else if($project_contract_review->duration_type == 'months'){
                    $contract_review = ($project_contract_review->plus_or_minus_duration == 'plus' ? '+' : '-').' '.$project_contract_review->duration_variation.'&nbsp;Month(s)';
                }else{
                    $contract_review = ($project_contract_review->plus_or_minus_duration == 'plus' ? '+' : '-').' '.$project_contract_review->duration_variation.'&nbsp;Year(s)';
                }
                $rows[] = [
                    custom_standard_date($row->date),
                    "Project Extension",
                    $row->description,
                    $contract_review,
                    '<span style="text-align: right">' .($project_contract_review->plus_or_minus_contract_sum == 'plus' ? '+' : '-').' '.number_format($project_contract_review->contract_sum_variation) . '</span>',
                    ''
                ];
            }
        }

        $data['data']=$rows;
        $data['recordsFiltered']= $records_filtered;
        $data['recordsTotal'] = $records_total;
        return json_encode($data);
    }

    public function created_by(){
        $this->load->model('employee');
        $created_by = new Employee();
        $created_by->load($this->created_by);
        return $created_by;
    }

    public function revised_task(){
        $this->load->model('revised_task');
        $where['revision_id'] = $this->{$this::DB_TABLE_PK};
        stringfy_dropdown_options($this->revised_task->get(0, 0, $where));
    }
}