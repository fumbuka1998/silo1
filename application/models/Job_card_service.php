<?php
class Job_card_service extends MY_Model {
    const DB_TABLE = 'job_card_services';
    const DB_TABLE_PK = 'id';
    public $job_card_labour_id;
    public $activity_id;

    public function job_card(){
        $this->load->model('job_card');
        $job_card = new Job_card();
        $job_card->load($this->job_card_id);
        return $job_card;
    }

    public function activity(){
        $this->load->model('activity');
        $activity = new Activity();
        $activity->load($this->activity_id);
        return $activity;
    }
}
