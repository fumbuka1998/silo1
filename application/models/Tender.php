<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/26/2018
 * Time: 8:14 PM
 */

class Tender extends MY_Model{
    const DB_TABLE = 'tenders';
    const DB_TABLE_PK = 'id';

    public $project_category_id;
    public $client_id;
    public $tender_name;
    public $date_announced;
    public $submission_deadline;
    public $date_procured;
    public $procurement_cost;
    public $procurement_currency_id;
    public $supervisor_id;
    public $created_by;

    public function tenders_list($level=null,$id=null,$limit, $start, $keyword, $order){
        if(is_null($level)) {
            $column_ordering = ['tender_no', 'tender_name','category_name','client_name','date_procured','supervisor_name'];
        } else if($level == 'client'){
            $column_ordering = ['tender_no', 'tender_name','category_name','date_announced','date_procured','supervisor_name'];
        } else {
            $column_ordering = ['tender_no', 'tender_name','client_name','date_procured','supervisor_name'];
        }

        $order_string = dataTable_order_string($column_ordering, $order, 'tender_no');

        $where_clause = '';
        if($keyword != '') {
            $where_clause .=  ($where_clause == '' ? ' WHERE ' : ' AND ').'(tender_name LIKE "%'.$keyword.'%" OR category_name LIKE "%'.$keyword.'%") ';
        }

        if(!is_null($level)&&!is_null($id)) {
            $where_clause .= ($where_clause == '' ? ' WHERE ' : ' AND ').'tenders.'. $level .'_id='. $id .'';
        }

        $sql = 'SELECT COUNT(id) AS records_total FROM tenders';

        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;

        $sql = 'SELECT SQL_CALC_FOUND_ROWS tenders.id AS tender_no,tender_name,project_categories.category_name,CONCAT(employees.first_name , employees.last_name) AS supervisor_name,date_announced,date_procured,tenders.client_id,client_name
                    FROM tenders
                    LEFT JOIN clients ON tenders.client_id = clients.client_id
                    LEFT JOIN project_categories ON tenders.project_category_id = project_categories.category_id
                    LEFT JOIN employees ON tenders.supervisor_id = employees.employee_id
                    '.$where_clause.' ORDER BY '.$order_string.' LIMIT '.$limit.' OFFSET '.$start;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];
        foreach($results as $row){
            $tender = new self();
            $tender->load($row->tender_no);
            $data['tender'] = $tender;
            $supervisor_name = $tender->supervisor()->full_name();
            if($level == 'client'){
                $rows[] = [
                    add_leading_zeros($row->tender_no),
                    anchor(base_url('tenders/tender_profile/' . $row->tender_no), $row->tender_name),
                    $row->category_name,
                    custom_standard_date($row->date_announced),
                    custom_standard_date($row->date_procured),
                    $supervisor_name,
                    $this->load->view('tenders/tender_actions',$data,true)
                ];
            } else if(is_null($level)){
                $rows[] = [
                    add_leading_zeros($row->tender_no),
                    anchor(base_url('tenders/tender_profile/' . $row->tender_no), $row->tender_name),
                    $row->category_name,
                    anchor(base_url('clients/profile/' .$row->client_id), $row->client_name),
                    custom_standard_date($row->date_procured),
                    $supervisor_name,
                    $this->load->view('tenders/tender_actions',$data,true)
                ];
            } else {
                $rows[] = [
                    add_leading_zeros($row->tender_no),
                    anchor(base_url('tenders/tender_profile/' . $row->tender_no), $row->tender_name),
                    anchor(base_url('clients/profile/'. $row->client_id), $row->client_name),
                    custom_standard_date($row->date_procured),
                    $supervisor_name,
                    $this->load->view('tenders/tender_actions',$data,true)
                ];
            }
        }

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function get_components($keyword = ''){
        $this->load->model('tender_component');
        $where = ' tender_id = '.$this->{$this::DB_TABLE_PK};
        if($keyword != ''){
            $where .= ' AND component_name LIKE "%'.$keyword.'%"';
        }
        return $this->tender_component->get(0,0,$where);
    }

    public function client()
    {
        $this->load->model('client');
        $client = new Client();
        $client->load($this->client_id);
        return $client;
    }

    public function category()
    {
        $this->load->model('project_category');
        $category = new Project_category();
        $category->load($this->project_category_id);
        return $category;
    }

    public function procurement_currency()
    {
        $this->load->model('currency');
        $procurement_currency = new Currency();
        $procurement_currency->load($this->procurement_currency_id);
        return $procurement_currency;
    }

    public function supervisor()
    {
        $this->load->model('employee');
        $supervisor = new Employee();
        $supervisor->load($this->supervisor_id);
        return $supervisor;
    }

    
}