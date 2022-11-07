<?php
class Timeline extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('project','activity','task','topic','topic_carbon_copy','topic_subject','topic_conversation','topic_conversation_log','employee'));
    }

    public function index(){
        $data['timeline'] = 'ePM | Timeline';
        $data['convo_logs'] = $this->topic_conversation_log->get(0,0,[],' id DESC');
        $this->load->view('timeline/index',$data);
    }

    public function chat_rooms_list(){
        $this->load->model('topic');
        $datatable = dataTable_post_params();
        echo $this->topic->chat_rooms_list($datatable['limit'],$datatable['start'],$datatable['keyword'],$datatable['order']);
    }

    public function submit_post_o_chat(){
        $topic = new Topic();
        $continuation = $this->input->post('topic_id');
        $continuation = $continuation != '' ? $continuation : null;
        $direct_chat_ccs = $this->input->post('direct_chat_ccs');
        $direct_chat_ccs = is_array($direct_chat_ccs) ? array_filter($direct_chat_ccs) : [];
        $subject_id = $this->input->post('subject_id');
        $exp_subject = explode('_', $subject_id);
        $topic_subject = new Topic_subject();
        $topic_subject->subject_type = strtoupper($exp_subject[0]);
        $data['project_id'] = $project_id = $this->input->post('project_id');
        $project = new Project();
        $project->load($project_id);
        $data['project'] = $project;
        date_default_timezone_set('Africa/Dar_es_Salaam');
        $date = date('Y-m-d H:i:s');
        if(is_null($continuation)) {
            switch ($exp_subject[0]) {
                case 'activity':
                    $subject = new Activity();
                    $subject->load($exp_subject[1]);
                    $subject_details = $subject->activity_name;
                    break;
                case 'task':
                    $subject = new Task();
                    $subject->load($exp_subject[1]);
                    $subject_details = $subject->task_name;
                    break;
            }
            $topic->subject = $subject_details;
            $topic->project_id = $project_id;
            $post_type = $this->input->post('type');
            $topic->status = ($post_type != '' && $post_type == 'PUBLIC') ? $post_type : "OPEN";
            $topic->type = $this->input->post('type');
            $topic->created_by = $this->session->userdata('employee_id');
            $topic->updated_at = $date;

            if (!empty($_FILES['file'])) {
                $attachments_directory = "./images/topics_thumbnails/";
                if (!file_exists($attachments_directory)) {
                    mkdir($attachments_directory,0777,true);
                }
                $config = [
                    'upload_path' => $attachments_directory,
                    'allowed_types' => 'gif|jpg|png|jpeg|pdf|docx|doc|dot|xls|xlsx'
                ];

                $this->load->library('upload', $config);
                if ($this->upload->do_upload('file')) {
                    $topic->attachment_name = $this->upload->data()['file_name'];
                }
            }

            if($topic->save()) {

                if (!empty($direct_chat_ccs)) {
                    foreach ($direct_chat_ccs as $chat_cc) {
                        $employee = new Employee();
                        $employee->load($chat_cc);
                        $topic_cc = new Topic_carbon_copy();
                        $topic_cc->topic_id = $topic->{$topic::DB_TABLE_PK};
                        $topic_cc->email = $employee->first_name . ' ' . $employee->last_name . '<' . $employee->email . '>';
                        $topic_cc->save();
                    }
                }

                switch ($exp_subject[0]) {
                    case 'activity':
                        $topic_subject->activity_id = $exp_subject[1];
                        break;
                    case 'task':
                        $topic_subject->task_id = $exp_subject[1];
                        break;
                }
                $topic_subject->topic_id = $topic->{$topic::DB_TABLE_PK};
                $topic_subject->save();
            }

        } else {
            $topic->load($continuation);
            $topic->updated_at = $date;
            $topic->save();
        }


        $topic_convo = new Topic_conversation();
        $topic_convo->topic_id = $topic->{$topic::DB_TABLE_PK};
        $topic_convo->email = $topic->creator()->email;
        $topic_convo->phone = $topic->creator()->phone;
        $convo_type = $this->input->post('convo_type');
        $convo_type = $convo_type != '' ? $convo_type : null;
        $topic_convo->sender = $convo_type == "CAPTION" ? $topic->created_by : $this->session->userdata('employee_id');
        $topic_convo->is_read = 0;
        $topic_convo->recipient = $this->input->post('recipient');
        $topic_convo->type = $this->input->post('convo_type');
        $topic_convo->message = $this->input->post('topic_message');
        $topic_convo->created_at = $topic->updated_at;
        $topic_convo->save();

        $log = array(
            'topic'=>$topic,
            'topic_ccs'=>$direct_chat_ccs,
            'topic_subject'=>$topic_subject,
            'topic_convo'=>$topic_convo
        );
        $session_user_id = $this->session->userdata('employee_id');
        $topic_convo_log = new Topic_conversation_log();
        $topic_convo_log->topic_id = $topic->{$topic::DB_TABLE_PK};
        $topic_convo_log->log_type = $session_user_id == $topic->created_by ? "SENDERS" : "RECIPIENTS";
        $topic_convo_log->log_details = urlencode(serialize($log));
        $topic_convo_log->datetime_posted = $topic->updated_at;
        $topic_convo_log->save();

        $data['topic_convo_logs'] = $topic->conversation_logs($project_id);
        if($topic->type == "PUBLIC"){
            $ret_view['public_posts_view'] = $this->load->view('projects/wallposts/posts/public_posts_view', $data,true);
        } else {
            $ret_view['main_recipient'] = $topic->chat_main_recipient();
            $ret_view['chat_box_view'] = $this->load->view('projects/wallposts/chats/chat_box_view', $data,true);
        }
        echo json_encode($ret_view);

    }

    public function load_topic_chats(){
        $this->load->model(array('activity','task','topic','topic_carbon_copy','topic_subject','topic_conversation','topic_conversation_log','employee'));
        $topic_id = $this->input->post('topic_id');
        $where = ['topic_id'=>$topic_id];
        $data['topic_convo_logs'] = $this->topic_conversation_log->get(0,0,$where,'id ASC');
        $ret_view['chat_box_view'] = $this->load->view('projects/wallposts/chats/chat_box_view',$data,true);
        echo json_encode($ret_view);
    }

    public function load_present_posts($type = "PUBLIC"){
        $this->load->model(array('activity','task','topic','topic_carbon_copy','topic_subject','topic_conversation','topic_conversation_log','employee'));
        $data['project_id'] = $project_id = $this->input->post('project_id');
        $sql = 'SELECT topics.* FROM topics
                WHERE type = "'.$type.'" AND project_id = '.$project_id.'
                ORDER BY topics.id DESC';

        $results = $this->db->query($sql)->result();
        $project_topics = [];
        foreach($results as $result){
            $topic = new Topic();
            $topic->load($result->id);
            $project_topics[] = $topic;
        }
        $data['project_topics'] = $project_topics;
        $ret_view['public_posts_view'] = $this->load->view('projects/wallposts/posts/public_posts_view', $data,true);
        echo json_encode($ret_view);
    }
}
?>