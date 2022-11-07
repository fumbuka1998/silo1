<?php

class Project_contract_review extends MY_Model
{

    const DB_TABLE = 'project_contract_reviews';
    const DB_TABLE_PK = 'id';

    public $project_id;
    public $review_date;
    public $plus_or_minus_duration;
    public $plus_or_minus_contract_sum;
    public $duration_type;
    public $duration_variation;
    public $contract_sum_variation;
    public $reason;
    public $created_by;

    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

    /**public function project_contract_review_list($limit, $start, $keyword, $order){

        $project_id = $this->input->post('project_id');

        if($project_id !=''){

            $where = ' project_id ="'.$project_id.'" ';

        }else{ $where = '';}

        $records_total = $this->count_rows($where);

        if($keyword != ''){
            $where .= $where != '' ? ' AND ' : '';
            $where .= ' (review_date LIKE "%'.$keyword.'%" OR duration_variation LIKE "%'.$keyword.'%" OR contract_sum_variation LIKE "%'.$keyword.'%" OR reason LIKE "%'.$keyword.'%")';
        }

        //order string
        $order_string = dataTable_order_string(['review_date','duration_variation','contract_sum_variation','reason'],$order,'review_date');

        $project_contract_reviews = $this->get($limit,$start,$where,$order_string);

        $rows = [];

        $this->load->model(['project', 'Project_contract_review']);
        $project = new Project();
        $project->load($project_id);
        $data = ['project' => $project];
        foreach ($project_contract_reviews as $project_contract_review){
            if($project_contract_review->duration_type == 'days'){
                $contract_review = ($project_contract_review->plus_or_minus_duration == 'plus' ? '+' : '-').' '.$project_contract_review->duration_variation.'&nbsp;Day(s)';
            }else if($project_contract_review->duration_type == 'months'){
                $contract_review = ($project_contract_review->plus_or_minus_duration == 'plus' ? '+' : '-').' '.$project_contract_review->duration_variation.'&nbsp;Month(s)';
            }else{
                $contract_review = ($project_contract_review->plus_or_minus_duration == 'plus' ? '+' : '-').' '.$project_contract_review->duration_variation.'&nbsp;Year(s)';
            }

            $data['project_contract_review'] = $project_contract_review;
            $rows[] = [
                custom_standard_date($project_contract_review->review_date),
                $contract_review,
                number_format($project_contract_review->contract_sum_variation).' '.($project_contract_review->plus_or_minus_contract_sum == 'plus' ? '+' : '-'),
                $project_contract_review->reason,
                $this->load->view('projects/contract_reviews/project_contract_review_action',$data,true)
            ];
        }

        $data['data']=$rows;
        $data['recordsFiltered']=$this->count_rows($where);
        $data['recordsTotal']=$records_total;
        return json_encode($data);

    }**/
}

