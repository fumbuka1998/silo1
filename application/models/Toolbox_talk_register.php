<?php
class Toolbox_talk_register extends MY_Model {
    const DB_TABLE = 'toolbox_talk_registers';
    const DB_TABLE_PK = 'id';
    public $site_id;
    public $activity_id;
    public $supervisor_id;
    public $date;
    public $created_by;

    public function site(){
        $this->load->model('project');
        $site = new Project();
        $site->load($this->site_id);
        return $site;
    }

    public function activity(){
        $this->load->model('activity');
        $activity = new Activity();
        $activity->load($this->activity_id);
        return $activity;
    }

    public function delete_talk_register_participants(){
        $this->db->where('toolbox_talk_register_id', $this->{$this::DB_TABLE_PK});
        $this->db->delete('toolbox_talk_register_participants');
    }

    public function delete_talk_register_topics(){
        $this->db->where('toolbox_talk_register_id', $this->{$this::DB_TABLE_PK});
        $this->db->delete('toolbox_talk_register_topics');
    }

    public function talk_register_participants(){
        $this->load->model('toolbox_talk_register_participant');
        return $this->toolbox_talk_register_participant->get(0,0,['toolbox_talk_register_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function talk_register_topics(){
        $this->load->model('toolbox_talk_register_topic');
        return $this->toolbox_talk_register_topic->get(0,0,['toolbox_talk_register_id' => $this->{$this::DB_TABLE_PK}]);
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

    public function toolbox_talk_registers_list($limit, $start, $keyword, $order){

        $order_string = dataTable_order_string(['date'],$order,'date');

        $where = '';
        if($keyword != ''){
            $where = ' date LIKE "%'.$keyword.'%" ';
        }

        $toolbox_talk_registers = $this->get($limit, $start, $where,$order_string);
        $this->load->model(['project','site_topic']);
        $rows = array();
        foreach ($toolbox_talk_registers as $talk_register) {
            $data['projects_options'] = $this->project->on_going_projects_dropdown();
            $data['topics_options'] = $this->site_topic->dropdown_options();
            $data['talk_register'] = $talk_register;
            $rows[] = array(
                set_date($talk_register->date),
                $talk_register->site()->project_name,
                $talk_register->activity()->activity_name,
                $talk_register->supervisor()->full_name(),
                $this->load->view('hse/toolbox_talk_registers/toolbox_talk_registers_list_actions',$data,true)
            );
        }
        $data['data']=$rows;
        $data['recordsFiltered']=$this->count_rows($where);
        $data['recordsTotal']=$this->count_rows();
        return json_encode($data);

    }

}