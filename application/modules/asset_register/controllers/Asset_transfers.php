<?php
class Asset_transfers extends CI_Controller
{

    public function __construct ()
    {
        parent::__construct();
        check_login();
    }
    public function asset_transfers(){

        $this->load->model('asset_transfer');
        $this->load->model('Department');
        $this->load->model('asset');
        $this->load->model('Sub_location');
        $this->load->model('Employee');
        $data['title'] = 'Asset Transfers';
        $data['asset_options']  = $this->asset->asset_dropdown_options();
        $data['department_options']  = $this->Department->department_options();
        $data['sub_location_options']  = general_sub_location_options();
        $data['employee_options']  = employee_options();
        $this->load->view('asset_transfer/index.php',$data);
    }


    public function asset_transfer_list()
    {
        $this->load->model('asset_transfer');
        $posted_params = dataTable_post_params();
        echo $this->asset_transfer->asset_transfer_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);

    }
    public function save(){

        $this->load->model('asset_transfer');
        $transfer = new Asset_transfer();
        $edit = $transfer->load($this->input->post('transfer_id'));
        $transfer->asset_id=$this->input->post('asset_id');
        $transfer->department_id = $this->input->post('department_id');
        $transfer->sub_location_id = $this->input->post('sub_location_id');
        $transfer->employee_id = $this->input->post('employee_id');
        $transfer->transfer_date = $this->input->post('transfer_date');
        $transfer->created_by = $this->session->userdata('employee_id');
        $transfer->datetime_posted = datetime();
        $transfer->description = $this->input->post('description');
        //$transfer->transfer_id = $this->input->post('transfer_id');
        $transfer->save();
    }

    public function delete_asset_transfer(){
        $this->load->model('asset_transfer');
        $transfer = new Asset_transfer();
        if($transfer->load($this->input->post('trans_id'))){
            $transfer->delete();
        }
    }

    public function transfer_list(){
        $this->load->model('asset_transfer');
        $transfers = $this->asset_transfer_list->get();
        foreach ($transfers as $transfer){
            inspect_object($transfer);
            //inspect_object($transfer->children_groups());
        }
    }

}