<?php
class Incident extends MY_Model {
    const DB_TABLE = 'incidents';
    const DB_TABLE_PK = 'id';
    public $incident_date;
    public $reference;
    public $type;
    public $causative_agent;
    public $location;
    public $is_reported;
    public $site_id;
    public $description;
    public $created_by;

    public function created_by(){
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->created_by);
        return $employee;
    }

    public function site(){
        $this->load->model('project');
        $site = new Project();
        $site->load($this->site_id);
        return $site;
    }

    public function incidents_list($limit, $start, $keyword, $order){

        $order_string = dataTable_order_string(['incident_dste','type'],$order,'incident_date');

        $where = '';
        if($keyword != ''){
            $where = ' incident_date LIKE "%'.$keyword.'%" OR type LIKE "%'.$keyword.'%" ';
        }

        $incidents = $this->get($limit, $start, $where,$order_string);
        $this->load->model('project');
        $rows = array();
        foreach ($incidents as $incident) {
            $data['projects_options'] = $this->project->on_going_projects_dropdown();
            $data['incident'] = $incident;
            $rows[] = array(
                //anchor(base_url('hse/incident_profile/' . $incident->{$incident::DB_TABLE_PK}), custom_standard_date($incident->incident_date)),
                custom_standard_date($incident->incident_date),
                $incident->site()->project_name,
                $incident->type,
                $incident->causative_agent,
                $incident->reference,
               // $incident->description,
               // $incident->created_by()->full_name(),
                $this->load->view('hse/incidents/incidents_list_actions',$data,true)
            );
        }
        $data['data']=$rows;
        $data['recordsFiltered']=$this->count_rows($where);
        $data['recordsTotal']=$this->count_rows();
        return json_encode($data);

    }

    public function incident_job_card(){
        $this->load->model('incident_job_card');
        $incident_job_card = $this->incident_job_card->get(1,0,['incident_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($incident_job_card) ? array_shift($incident_job_card) : '';
    }

}
