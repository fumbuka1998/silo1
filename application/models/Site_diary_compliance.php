<?php
class Site_diary_compliance extends MY_Model {
    const DB_TABLE = 'site_diary_compliances';
    const DB_TABLE_PK = 'id';
    public $site_id;
    public $date;
    public $supervisor_id;
    public $remarks;
    public $created_by;

    public function site(){
        $this->load->model('project');
        $site = new Project();
        $site->load($this->site_id);
        return $site;
    }

    public function supervisor(){
        $this->load->model('employee');
        $supervisor = new Employee();
        $supervisor->load($this->supervisor_id);
        return $supervisor;
    }

    public function created_by(){
        $this->load->model('employee');
        $created_by = new Employee();
        $created_by->load($this->created_by);
        return $created_by;
    }

    public function delete_diary_copliance_statuses(){
        $this->db->where('site_diary_id',$this->{$this::DB_TABLE_PK});
        $this->db->delete('site_diary_compliance_statuses');
    }

    public function site_diary_complience_statuses(){
        $this->load->model('site_diary_compliance_status');
        return $this->site_diary_compliance_status->get(0,0,['site_diary_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function site_diary_compliances_list($limit, $start, $keyword, $order){

        $order_string = dataTable_order_string(['date'],$order,'date');

        $where = '';
        if($keyword != ''){
            $where = ' date LIKE "%'.$keyword.'%" ';
        }

        $site_diary_compliances = $this->get($limit, $start, $where,$order_string);
        $this->load->model('project');
        $rows = array();
        foreach ($site_diary_compliances as $site_diary_compliance) {
            $data['projects_options'] = $this->project->on_going_projects_dropdown();
            $data['site_diary_compliance'] = $site_diary_compliance;
            $rows[] = array(
                set_date($site_diary_compliance->date),
                $site_diary_compliance->site()->project_name,
                $site_diary_compliance->supervisor()->full_name(),
                $site_diary_compliance->remarks,
                $this->load->view('hse/site_diary_compliances/site_diary_compliances_list_actions',$data,true)
            );
        }
        $data['data']=$rows;
        $data['recordsFiltered']=$this->count_rows($where);
        $data['recordsTotal']=$this->count_rows();
        return json_encode($data);

    }

}