<?php
class Job_card extends MY_Model {
    const DB_TABLE = 'job_cards';
    const DB_TABLE_PK = 'id';
    public $priority;
    public $date;
    public $is_closed;
    public $remarks;
    public $created_by;

    public function job_card_number(){
        return 'Job Card No : '.add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function created_by(){
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->created_by);
        return $employee;
    }

    public function delete_inspection_job_card(){
        $this->db->where('job_card_id', $this->{$this::DB_TABLE_PK});
        $this->db->delete('inspection_job_cards');
    }

    public function delete_incident_job_card(){
        $this->db->where('job_card_id', $this->{$this::DB_TABLE_PK});
        $this->db->delete('incident_job_cards');
    }

    public function job_card_labour(){
        $this->load->model('job_card_labour');
        $job_card_labour = $this->job_card_labour->get(1,0,['job_card_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($job_card_labour) ? array_shift($job_card_labour) : '' ;
    }

    public function job_card_service(){
        $this->load->model('job_card_service');
        $job_card_service = $this->job_card_service->get(1,0,['job_card_id' => $this->{$this::DB_TABLE_PK} ]);
        return !empty($job_card_service) ? array_shift($job_card_service) : '';
    }

    public function inspection_job_card(){
        $this->load->model('inspection_job_card');
        $inspection_job_card = $this->inspection_job_card->get(1,0,['job_card_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($inspection_job_card) ? array_shift($inspection_job_card) : '';
    }

    public function incident_job_card(){
        $this->load->model('incident_job_card');
        $incident_job_card = $this->incident_job_card->get(1,0,['job_card_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($incident_job_card) ? array_shift($incident_job_card) : '';
    }

    public function job_cards_list($limit, $start, $keyword, $order){
        $order_string = dataTable_order_string(['date'],$order,'date');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $where_clause = '';
        if($keyword != '') {
            $where_clause .= ' WHERE (date LIKE "%'.$keyword.'%" OR date )';
        }

        $sql = ' SELECT SQL_CALC_FOUND_ROWS * FROM (
                SELECT inspection_job_cards.job_card_id as job_card_id, job_cards.date as date, "Inspection" as type, inspection_job_cards.inspection_id as id
                FROM inspection_job_cards
                LEFT JOIN job_cards ON inspection_job_cards.job_card_id = job_cards.id
                UNION
                SELECT incident_job_cards.job_card_id as job_card_id,job_cards.date as date, "Incident" as type, incident_job_cards.incident_id as id
                FROM incident_job_cards
                LEFT JOIN job_cards ON incident_job_cards.job_card_id = job_cards.id
                
                ) AS artificial_table'.$where_clause.$order_string;

        $query = $this->db->query($sql);
        $records_total = $query->num_rows();
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;
        $this->load->model(['inspection_job_card','incident_job_card']);

        $rows = [];
        foreach($results as $row){
            if($row->type == 'Inspection'){
                $job_card = new self();
                $job_card->load($row->job_card_id);
                $job_card_type = $job_card->inspection_job_card();
            } else {
                $job_card = new self();
                $job_card->load($row->job_card_id);
                $job_card_type = $job_card->incident_job_card();
            }
            $data['type'] = $row->type;
            $data['job_card'] = $job_card;
            $data['job_card_type'] = $job_card_type;
            $rows[] = [
                set_date($job_card->date),
                $job_card->priority,
                $row->type,
                $job_card->remarks,
                $job_card->created_by()->full_name(),
                $this->load->view('hse/job_cards/job_cards_list_actions',$data,true)
            ];
        }

        $json = [
            "data" => $rows,
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered

        ];
        return json_encode($json);
    }

    public function job_card_reports($from, $to, $job_card_type)
    {

        if($job_card_type == 'Inspection') {
            $sql = '
                    SELECT inspection_job_cards.job_card_id as job_card_id, job_cards.date as date, "Inspection" as type, inspection_job_cards.inspection_id as id
                    FROM inspection_job_cards
                    LEFT JOIN job_cards ON inspection_job_cards.job_card_id = job_cards.id
                    WHERE job_cards.date >= "'.$from.'"
                    AND job_cards.date <= '.$to ;

        } else if($job_card_type == 'Incident') {

            $sql = '
                    SELECT incident_job_cards.job_card_id as job_card_id,job_cards.date as date, "Incident" as type, incident_job_cards.incident_id as id
                    FROM incident_job_cards
                    LEFT JOIN job_cards ON incident_job_cards.job_card_id = job_cards.id
                    WHERE job_cards.date >= "'.$from.'"
                    AND job_cards.date <= '.$to ;

        } else {
            $sql = '
                    SELECT inspection_job_cards.job_card_id as job_card_id, job_cards.date as date, "Inspection" as type, inspection_job_cards.inspection_id as id
                    FROM inspection_job_cards
                    LEFT JOIN job_cards ON inspection_job_cards.job_card_id = job_cards.id
                    WHERE job_cards.date >= "'.$from.'"
                    AND job_cards.date <= "'.$to.'"
                    UNION
                    SELECT incident_job_cards.job_card_id as job_card_id,job_cards.date as date, "Incident" as type, incident_job_cards.incident_id as id
                    FROM incident_job_cards
                    LEFT JOIN job_cards ON incident_job_cards.job_card_id = job_cards.id
                    WHERE job_cards.date >= "'.$from.'"
                    AND job_cards.date <= '.$to ;

        }


        $query = $this->db->query($sql);
        $results = $query->result();
        $job_cards = [];

        foreach($results as $row)
        {
            if($row->type == 'Inspection'){
                $job_card = new self();
                $job_card->load($row->job_card_id);
                $job_card_type = $job_card->inspection_job_card()->inspection();
            } else {
                $job_card = new self();
                $job_card->load($row->job_card_id);
                $job_card_type = $job_card->incident_job_card()->incident();
            }

            $job_cards[] = [
                'job_card_no' => $job_card->job_card_number(),
                'job_card_date' => $job_card->date,
                'site' => $job_card_type->site()->project_name,
                'priority' => $job_card->priority,
                'remarks' => $job_card->remarks

            ];
        }

        return $job_cards;
    }


}