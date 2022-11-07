<?php
class Job_card_labour extends MY_Model {
    const DB_TABLE = 'job_card_labours';
    const DB_TABLE_PK = 'id';
    public $job_card_id;
    public $employee_id;

    public function job_card(){
        $this->load->model('job_card');
        $job_card = new Job_card();
        $job_card->load($this->job_card_id);
        return $job_card;
    }

    public function delete_job_card_service(){
        $this->db->where('job_card_labour_id', $this->{$this::DB_TABLE_PK});
        $this->db->delete('job_card_services');
    }

    public function job_card_service(){
        $this->load->model('job_card_service');
        $job_card_service = $this->job_card_service->get(1,0,['job_card_labour_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($job_card_service) ? array_shift($job_card_service) : '';
    }

    public function job_card_services(){
        $this->load->model('job_card_service');
        return $this->job_card_service->get(0,0,['job_card_labour_id' => $this->{$this::DB_TABLE_PK}]);
    }


    public function labour(){
        $this->load->model('employee');
        $labour = new Employee();
        $labour->load($this->employee_id);
        return $labour;
    }

    public function job_card_labours_and_activities_list( $limit, $start, $keyword, $order){

        $order_string = dataTable_order_string(['job_card_id'],$order,'job_card_id');
        $job_card_id = $this->input->post('job_card_id');
        $type = $this->input->post('type');
        $where = 'job_card_id ='.$job_card_id;
        if($keyword != ''){
            $where = ' job_card_id LIKE "%'.$keyword.'%" ';
        }

        $job_card_labours = $this->get($limit, $start, $where,$order_string);
        $this->load->model(['employee','activity','job_card']);

        if($type == 'Inspection'){
            $job_card = new Job_card();
            $job_card->load($job_card_id);
            $job_card_type = $job_card->inspection_job_card()->inspection();
        } else {
            $job_card = new Job_card();
            $job_card->load($job_card_id);
            $job_card_type = $job_card->incident_job_card()->incident();
        }
        $this->load->model('registered_certificate');
        $rows = array();
        foreach ($job_card_labours as $job_card_labour) {
            $jcl = new self();
            $jcl->load($job_card_labour->id);
            $data['type'] = $type;
            $data['job_card_labour'] = $job_card_labour;
//            $activity = $jcl->job_card_service()->activity();
            $data['labours_options'] = $this->registered_certificate->certificated_labours();
            $data['activities_options'] = $this->activity->dropdown_options($job_card_type->site_id);
            $rows[] = array(
                $jcl->labour()->full_name(),
                '',//$activity->activity_name,
                $this->load->view('hse/job_cards/profile/labours_and_activities/labours_and_activities_list_actions',$data,true)
            );
        }
        $data['data']=$rows;
        $data['recordsFiltered']=$this->count_rows($where);
        $data['recordsTotal']=$this->count_rows();
        return json_encode($data);

    }
}