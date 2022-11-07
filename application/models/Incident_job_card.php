<?php
class Incident_job_card extends MY_Model {
    const DB_TABLE = 'incident_job_cards';
    const DB_TABLE_PK = 'id';
    public $job_card_id;
    public $incident_id;

    public function job_card(){
        $this->load->model('job_card');
        $job_card = new Job_card();
        $job_card->load($this->job_card_id);
        return $job_card;
    }

    public function incident(){
        $this->load->model('incident');
        $incident = new Incident();
        $incident->load($this->incident_id);
        return $incident;
    }

    public function incident_job_cards_list($incident_id, $limit, $start, $keyword, $order){

        $order_string = dataTable_order_string(array('date '),$order,'date ');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $where = '';
        if($keyword != ''){
            $where .= ' AND (date LIKE "%'.$keyword.'%") ';
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS job_cards.id as id, priority, date, remarks, job_cards.created_by as created_by, incident_job_cards.incident_id as incident_id
                FROM job_cards
                LEFT JOIN incident_job_cards ON job_cards.id = incident_job_cards.job_card_id
                  WHERE incident_job_cards.incident_id = "'.$incident_id.'"
            ' .$where.$order_string ;

        $query = $this->db->query($sql);
        $records_total = $query->num_rows();
        $incident_job_cards = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;
        $this->load->model('job_card');
        $job_card = new Job_card();
        $rows = array();
        foreach ($incident_job_cards as $row) {
            $job_card->load($row->id);
            $data['job_card'] = $job_card;
            $type = 'Incident';
            $rows[] = array(
                set_date($row->date),
                $row->priority,
                $row->remarks,
                $job_card->created_by()->full_name(),
                $this->load->view('hse/incidents/profile/job_cards/job_cards_list_actions',$data,true)
            );
        }
        $data['data']=$rows;
        $data['recordsFiltered']=$this->count_rows($where);
        $data['recordsTotal']=$this->count_rows();
        return json_encode($data);

    }
}