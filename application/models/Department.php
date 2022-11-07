<?php

class Department extends MY_Model{
    
    const DB_TABLE = 'departments';
    const DB_TABLE_PK = 'department_id';

    public $department_name;
    public $description;

    public function department_options()
    {
        $this->load->model('department');
        $options[''] = '&nbsp;';
        $departments = $this->department->get(0,0,'','department_name');
        foreach($departments as $department){
            $options[$department->{$department::DB_TABLE_PK}] = $department->department_name;
        }
        return $options;
    }

    public function departments_list(){
        $keyword = $this->input->post('search')['value'];
        $start = $this->input->post('start');
        $limit = $this->input->post('length');

        $order_column = $this->input->post('order')[0]['column'];
        $order_dir = $this->input->post('order')[0]['dir'];
        switch ($order_column) {
            case 0;
                $order_column = 'department_name';
                break;
            case 1;
                $order_column = 'description';
                break;
            case 2;
                $order_column = 'number_of_employees';
                break;
            default:
                $order_column = 'department_name';
        }

        $order_string = $order_column.' '.$order_dir;

        $sql = 'SELECT departments.*,
                    (
                      SELECT COALESCE (COUNT(employee_id),0)
                      FROM employees
                      WHERE employees.department_id = departments.department_id
                    ) AS number_of_employees
                    FROM departments
                ';

        if($keyword != ''){
            $sql .= ' WHERE department_name LIKE "%'.$keyword.'%" OR description LIKE "%'.$keyword.'%" ';
        }

        $query = $this->db->query($sql);
        $records_filtered = $query->num_rows();

        $sql .= " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $query = $this->db->query($sql);
        $records_total = $this->db->count_all('departments');

        $results = $query->result();
        $rows = [];

        $this->load->model('department');
        foreach($results as $row){
            $department = new self();
            $department->load($row->department_id);
            $data['department'] = $department;
            $data['number_of_employees'] = $row->number_of_employees;
            $rows[] = [
                $row->department_name,
                $row->description,
                $row->number_of_employees,
                $this->load->view('human_resources/departments/list_actions',$data,true)
            ];
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function employees(){
        $this->load->model('employee');
        $where['department_id'] = $this->{$this::DB_TABLE_PK};
        return $this->employee->get(0,0,$where,'first_name');
    }
}

