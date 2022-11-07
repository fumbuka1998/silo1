<?php
class Asset_transfer extends MY_Model{

    const DB_TABLE = 'asset_transfers';
    const DB_TABLE_PK = 'id';

    public $asset_id;
    public $department_id;
    public $sub_location_id;
    public $employee_id;
    public $transfer_date;
    public $created_by;
    public $datetime_posted;
    public $description;



    public function asset_transfer_list($limit, $start, $keyword, $order){
        $records_total = $this->count_rows();

        $where = '';
        if($keyword != ''){
            $where .= 'transfer_date LIKE "%'.$keyword.'%" OR datetime_posted LIKE "%'.$keyword.'%" ';
        }


        $order_string = dataTable_order_string(['transfer_date','datetime_posted'],$order,'transfer_date');

        $transfers = $this->get($limit,$start,$where,$order_string);
        $rows = [];
        $this->load->model('Department');
        $this->load->model('asset');
        $this->load->model('Sub_location');
        $this->load->model('Employee');

        $data['asset_options']  = $this->asset->asset_dropdown_options();
        $data['department_options']  = $this->Department->department_options();
        $data['sub_location_options']  = general_sub_location_options();
        $data['employee_options']  = employee_options();

        foreach ($transfers as $transfer){
            $data['transfer_data'] = $transfer;
            $sub_location = $transfer->sub_location();
            $rows[] = [
                $transfer->asset_name()->asset_name,
                $transfer->department()->department_name,
                $sub_location->location()->location_name.'/'.$sub_location->sub_location_name,
                $transfer->employee_under()->full_name(),
                custom_standard_date($transfer->transfer_date),
                $transfer->created_by()->full_name(),
                standard_datetime($transfer->datetime_posted),
                $transfer->description,
                $this->load->view('asset_transfer/asset_transfer_actions',$data,true)
            ];
        }

        $records_filtered = $this->count_rows($where);

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function department(){
        $this->load->model('department');
        $department = new Department();
        $department->load($this->department_id);
        return $department;
    }

    public function sub_location(){
        $this->load->model('sub_location');
        $sub_location = new Sub_location();
        $sub_location->load($this->sub_location_id);
        return $sub_location;
    }
    public function employee_under(){
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->employee_id);
        return $employee;
    }
    public function created_by(){
        $this->load->model('employee');
        $created_by = new Employee();
        $created_by->load($this->created_by);
        return $created_by;
    }
    public function asset_name(){
        $this->load->model('asset');
        $asset_name = new asset();
        $asset_name->load($this->asset_id);
        return $asset_name;
    }

}