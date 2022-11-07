<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 1/8/2020
 * Time: 3:54 PM
 */
class Inspection extends MY_Model {
    const DB_TABLE = 'inspections';
    const DB_TABLE_PK = 'id';
    public $status;
    public $inspection_date;
    public $site_id;
    public $inspector_id;
    public $description;
    public $created_by;

    public function delete_inspection_category(){
        $this->db->where('inspection_id', $this->{$this::DB_TABLE_PK});
        $this->db->delete('inspection_categories');
    }

    public function site(){
        $this->load->model('project');
        $site = new Project();
        $site->load($this->site_id);
        return $site;
    }

    public function inspection_category(){
        $this->load->model('inspection_category');
        $inspection_category = $this->inspection_category->get(1,0,['inspection_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($inspection_category) ? array_shift($inspection_category) : '';
    }

    public function inspection_categories()
    {
        $this->load->model('inspection_category');
        return $this->inspection_category->get(0, 0, ['inspection_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function created_by(){
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->created_by);
        return $employee;
    }

    public function inspector(){
        $this->load->model('employee');
        $inspector = new Employee();
        $inspector->load($this->inspector_id);
        return $inspector;
    }

    public function inspections_list($limit, $start, $keyword, $order){

        $category_id = $this->input->post('category_id');
        $order_string = dataTable_order_string(['inspection_date'],$order,'inspection_date');


        $this->db->select('inspections.id AS insp_id, inspections.*');
        $this->db->join('inspection_categories','inspections.id = inspection_categories.inspection_id','left');
        $this->db->where('category_id',$category_id);
        $this->db->order_by($order_string);
        $result_one = $this->db->get('inspections',$limit,$start);
        $records_total = count($result_one->result());

        $this->db->select('inspections.id AS insp_id, inspections.*');
        $this->db->join('inspection_categories','inspections.id = inspection_categories.inspection_id','left');
        $this->db->where('category_id',$category_id);
        if($keyword != '') $this->db->like('inspection_date',$keyword);
        $this->db->order_by($order_string);
        $result_two = $this->db->get('inspections',$limit,$start);
        $results = $result_two->result();
        $records_filtered = count($results);

        $this->load->model(['category','category_parameter','employee','project']);
        $rows = array();
        foreach ($results as $row) {
            $inspection = new self();
            $inspection->load($row->insp_id);
            $data['categories_options'] = $this->category->dropdown_options();
            $data['parameters_options'] = $this->category_parameter->dropdown_options();
            $data['inspectors_options'] = employee_options();
            $data['categories'] = $this->category->get();
            $data['projects_options'] = $this->project->on_going_projects_dropdown();
            $data['inspection'] = $inspection;
            $data['insp_type'] = str_replace(' ','_',$inspection->inspection_type);
            $data['category_id'] = $category_id;
            $data['category_name'] = hse_inspection_categories($category_id)->name;
            $data['category_parameters'] = $this->category_parameter->get(0,0,['category_id'=>$category_id]);
            $rows[] = array(
                set_date($inspection->inspection_date),
                $inspection->site()->project_name,
                $inspection->inspector()->full_name(),
                $inspection->description,
                $this->load->view('hse/inspections/inspections_list_actions',$data,true)
            );
        }
        $data['data']=$rows;
        $data['recordsFiltered']=$records_filtered;
        $data['recordsTotal']=$records_total;
        return json_encode($data);

    }

    public function inspection_job_card(){
        $this->load->model('inspection_job_card');
        $inspection_job_card = $this->inspection_job_card->get(1,0,['inspection_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($inspection_job_card) ? array_shift($inspection_job_card) : '';
    }

    public function fik_inspections_list($limit, $start, $keyword, $order){

        $order_string = dataTable_order_string(['inspection_date','inspection_date'],$order,'inspection_date');

        $where = ['inspection_type' => 'FIK Inspection'];
        if($keyword != ''){
            $where .= ' inspection_date LIKE "%'.$keyword.'%" ';
        }

        $inspections = $this->get($limit, $start, $where,$order_string);
        $this->load->model(['category','category_parameter','employee','project']);
        $rows = array();
        foreach ($inspections as $inspection) {
            $category = $this->category->get(1,0,['id' => 7]);
            $data['category'] = array_shift($category);
            $fik_category = $this->category->get(1,0,['id' => 8]);
            $data['fik_category'] = array_shift($fik_category);
            $data['category_parameters'] = $this->category_parameter->get(0,0,['category_id'=>7]);
            $data['fik_category_parameters'] = $this->category_parameter->get(0,0,['category_id'=>8]);
            $data['categories_options'] = $this->category->dropdown_options();
            $data['parameters_options'] = $this->category_parameter->dropdown_options();
            $data['inspectors_options'] = employee_options();
            $data['categories'] = $this->category->get();
            $data['projects_options'] = $this->project->on_going_projects_dropdown();
            $data['inspection'] = $inspection;
            $rows[] = array(
                set_date($inspection->inspection_date),
                $inspection->site()->project_name,
                $inspection->inspector()->full_name(),
                $inspection->description,
                $this->load->view('hse/inspections/first_aid_kits/first_aid_kits_list_actions',$data,true)
            );
        }
        $data['data']=$rows;
        $data['recordsFiltered']=$this->count_rows($where);
        $data['recordsTotal']=$this->count_rows();
        return json_encode($data);

    }
}