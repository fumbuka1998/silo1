<?php

class Project_extension extends MY_Model
{

    const DB_TABLE = 'project_extensions';
    const DB_TABLE_PK = 'id';

    public $extension_date;
    public $project_id;
    public $duration;
    public $duration_type;
    public $extension_cost;
    public $reason;
    public $created_by;

    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

    public function project_extension_list($limit, $start, $keyword, $order){

        $project_id = $this->input->post('project_id');

        if($project_id !=''){

            $where = ' project_id ="'.$project_id.'" ';

        }else{ $where = '';}

        $records_total = $this->count_rows($where);

        if($keyword != ''){
            $where .= $where != '' ? ' AND ' : '';
            $where .= ' (extension_date LIKE "%'.$keyword.'%" OR duration LIKE "%'.$keyword.'%" OR extension_cost LIKE "%'.$keyword.'%" OR reason LIKE "%'.$keyword.'%")';
        }

        //order string
        $order_string = dataTable_order_string(['extension_date','','extension_cost','reason'],$order,'extension_date');

        $project_extensions = $this->get($limit,$start,$where,$order_string);

        $rows = [];

        $this->load->model(['project','project_extension']);
        $project = new Project();
        $project->load($project_id);
        $data = ['project' => $project];
        foreach ($project_extensions as $project_extension){
            $data['project_extension'] = $project_extension;
            $rows[] = [
                custom_standard_date($project_extension->extension_date),
                $project_extension->duration.' '.$project_extension->duration_type,
                number_format($project_extension->extension_cost),
                $project_extension->reason,
                $this->load->view('projects/extensions/project_extension_action',$data,true)
            ];
        }

        $data['data']=$rows;
        $data['recordsFiltered']=$this->count_rows($where);
        $data['recordsTotal']=$records_total;
        return json_encode($data);

    }
}

