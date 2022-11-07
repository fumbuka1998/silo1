<?php
/**
 * Created by PhpStorm.
 * User: userx
 * Date: 2/4/20
 * Time: 9:44 AM
 */

class Topic extends MY_Model{
    const DB_TABLE = 'topics';
    const DB_TABLE_PK = 'id';

    public $subject;
    public $project_id;
    public $type;
    public $status;
    public $attachment_name;
    public $created_by;
    public $updated_at;


    public function project(){
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

    public function subject(){
        $this->load->model('topic_subject');
        $topic_subjects = $this->topic_subject->get(0,0,['topic_id'=>$this->{$this::DB_TABLE_PK}]);
        return array_shift($topic_subjects);
    }

    public function chat_main_recipient($id = false){
        $conversations = $this->conversations("REPLY");
        $first_conversation = array_shift($conversations);
        if(!$id) { return $first_conversation->recipient()->full_name(); } else { return $first_conversation->recipient()->employee_id; };
    }

    public function chat_rooms_list($limit,$start,$keyword,$order){
        $this->load->model([
            'project'
        ]);
        $project_id = $this->input->post('project_id');
        $project_id = $project_id != '' ? $project_id : null;

        $data['project'] = false;
        $where_clause = ' topics.type != "PUBLIC"';
        if(!is_null($project_id)) {
            $project = new Project();
            $project->load($project_id);
            $data['project'] = $project;
            $where_clause .= ' AND project_id = ' . $project_id . '';
            $data['project_objects_dropdown_options'] = $project->project_objects_dropdown_options();

        }
        $records_total = $this->count_rows($where_clause);
        $order_string = dataTable_order_string(['id','status','created_at','first_name'],$order,'id DESC');

        if($keyword != ''){
            $where_clause .= ''.($where_clause != '' ? ' AND' : '').' (status LIKE "%'.$keyword.'%" OR created_at LIKE "%'.$keyword.'%" OR first_name LIKE "%'.$keyword.'%" OR middle_name LIKE "%'.$keyword.'%" OR last_name LIKE "%'.$keyword.'%") ';
        }

        $this->db->select('topics.id AS topic_id,topics.*,employees.*');
        $this->db->join('employees','topics.created_by = employees.employee_id','left');
        if($where_clause != '') {
            $this->db->where($where_clause);
        }
        $this->db->order_by($order_string);
        $results = $this->db->get($this::DB_TABLE,$limit,$start)->result();
        $rows = [];
        $data['employee_options'] = employee_options();
        foreach($results as $row){
            $topic = new self();
            $topic->load($row->topic_id);
            $data['topic'] = $topic;
            $data['topic_convo_logs'] = $topic->conversation_logs($project_id);
            $data['continuation'] = true;

            $rows[] = [
                $row->created_at,
                $topic->creator()->full_name(),
                $row->status,
                $row->updated_at,
                $this->load->view('projects/wallposts/chats/chat_list_actions',$data,true)
            ];
        }
        $records_filtered = count($results);
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        echo json_encode($json);
    }

    public function creator(){
        $this->load->model('employee');
        $creator = new Employee();
        $creator->load($this->created_by);
        return $creator;
    }

    public function conversations($type,$sorting_flag = 'ASC'){
        $this->load->model('topic_conversation');
        $where = [
            'type'=>(!is_null($type) ? $type : ' IS NULL'),
            'topic_id'=>$this->{$this::DB_TABLE_PK}
        ];
        $convos = $this->topic_conversation->get(0,0,$where,'id '.$sorting_flag.'');
        return $convos;
    }

    public function conversation_logs($project_id = null){
        $sql = 'SELECT topic_conversation_logs.* FROM topic_conversation_logs
                LEFT JOIN topics ON topic_conversation_logs.topic_id = topics.id
                WHERE topic_id = '.$this->{$this::DB_TABLE_PK}.'';
        if(!is_null($project_id)){
            $sql .= ' AND project_id = '.$project_id.'';
        }
        $sql .= ' ORDER BY topic_conversation_logs.id ASC';
        return $this->db->query($sql)->result();
    }

    public function thumbnail(){
            $extension = strtolower(pathinfo($this->attachment_name,PATHINFO_EXTENSION));
            $directory_path = 'images/topics_thumbnails/';
            $supported_image = array('gif', 'jpg','jpeg', 'png');

            if($extension == 'xls' || $extension == 'xlsx' ){
                $link_display = '<i class="fa fa-file-excel-o"></i>';
            } else if($extension == 'doc' || $extension == 'docx' ){
                $link_display = '<i class="fa fa-file-word-o"></i>';
            } else if($extension == 'pdf'){
                $link_display = '<i class="fa fa-file-pdf-o"></i>';
            } else if(in_array($extension, $supported_image) ){
                $link_display = '<i class="fa fa-image"></i>';
            } else {
                $link_display = '<i class="fa fa-file"></i>';
            }

            if(in_array($extension, $supported_image) ){
                return base_url($directory_path.$this->attachment_name);
            } else {
                return anchor(base_url($directory_path.$this->attachment_name), $link_display, ' target="_blank" class="btn btn-primary btn-xs" title="Open" ');
            }
    }

    public function has_thumbnail(){
        $directory_path = 'images/topics_thumbnails/';
        return (!is_null($this->attachment_name) && file_exists($directory_path.$this->attachment_name)) ? base_url($directory_path.$this->attachment_name) : false;
    }

    public function ccs(){
        $this->load->model('topic_carbon_copy');
        return $this->topic_carbon_copy->get(0,0,['topic_id'=>$this->{$this::DB_TABLE_PK}]);
    }
}
