<?php
class Topic_conversation_log extends MY_Model{
    const DB_TABLE = 'topic_conversation_logs';
    const DB_TABLE_PK = 'id';

    public $topic_id;
    public $log_type;
    public $log_details;
    public $datetime_posted;


    public function timeline_message($session_id,$log_details){
        $log_details = unserialize(urldecode($log_details));
        $topic = $log_details['topic'];
        $topic_convo = $log_details['topic_convo'];

        $creator = $topic->created_by;
        $sender = $topic_convo->sender;
        $recipient = $topic_convo->recipient;
        $topic_type = $topic->type;
        $convo_type = $topic_convo->type;
        $session_id = $this->session->userdata('employee_id');
        if($topic->subject()->subject_type == "TASK"){ $hint_middle_seg = ' a task '; } else { $hint_middle_seg = ' an activity ';}

        switch($topic_type){
            case "PUBLIC":
                if($sender == $session_id) { $hint_start_seg = '<a href="#">You</a>'; } else { $hint_start_seg = '<a href="#">'.$topic_convo->sender()->full_name().'</a>'; }
                switch($session_id){
                    case $creator:
                        if($convo_type == "CAPTION"){
                            $icon = '<i class="fa fa-sticky-note-o bg-aqua"></i>';
                            $hint = $hint_start_seg.'</a> shared a post about '.$hint_middle_seg.' on ';
                        }
                        if($convo_type == "COMMENT"){
                            $icon = '<i class="fa fa-comments bg-yellow"></i>';
                            $hint = $hint_start_seg.'</a> commented on your post about '.$hint_middle_seg.' on ';
                        }
                        break;
                    default:
                        if($sender != $session_id){
                            if($convo_type == "CAPTION"){
                                $icon = '<i class="fa fa-sticky-note-o bg-aqua"></i>';
                                $hint = $hint_start_seg.'</a> shared a post about '.$hint_middle_seg.' on ';
                            }
                            if($convo_type == "COMMENT"){
                                $icon = '<i class="fa fa-comments bg-yellow"></i>';
                                $hint = $hint_start_seg.'</a> commented on a post about '.$hint_middle_seg.' on ';
                            }

                        } else {
                            $icon = '<i class="fa fa-comments bg-yellow"></i>';
                            $hint = $hint_start_seg . '</a> commented on a post about ' . $hint_middle_seg . ' on ';
                            break;
                        }
                }
                break;
            case "DIRECT":
                if($creator == $session_id) { $hint_start_seg = '<a href="#">You</a>'; } else { $hint_start_seg = '<a href="#">'.$topic_convo->sender()->full_name().'</a>'; }
                switch($session_id){
                    case $sender:
                        if($creator == $session_id) {
                            $icon = '<i class="fa fa-envelope bg-aqua"></i>';
                            $hint = $hint_start_seg.' sent a chat to '.$topic_convo->recipient()->full_name().'  about '.$hint_middle_seg.' on ';
                        } else {
                            $icon = '<i class="fa fa-retweet bg-aqua"></i>';
                            $hint = '<a href="#">You</a> replied to a chat started by ' . $topic->creator()->full_name().' about '.$hint_middle_seg.' on ';
                        }
                        break;
                    case $recipient:
                        if($creator != $session_id) {
                            $icon = '<i class="fa fa-envelope bg-aqua"></i>';
                            $hint = $hint_start_seg.' sent a chat to you  about '.$hint_middle_seg.' on ';
                        } else {
                            $icon = '<i class="fa fa-retweet bg-aqua"></i>';
                            $hint = $hint_start_seg.' replied to a chat started by ' . $topic->creator()->full_name().' about '.$hint_middle_seg.' on ';
                        }
                        break;
                }
                break;
        }
        return $icon.'_'.$hint;

    }
}

?>