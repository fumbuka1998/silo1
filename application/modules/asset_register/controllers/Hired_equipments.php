<?php

class Hired_equipments extends CI_Controller{
    
    public function __construct() {
        parent::__construct();
        check_login();
     }


    public function index(){

        $limit = $this->input->post('length');
        if ($limit != '') {
             $this->load->model('requisition');
             $this->load->model('Equipment_Requisition');

            $posted_params = dataTable_post_params();
           echo $this->Equipment_Requisition->requisitions_list($this->input->post('location_id'),$posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
        } else {
            $data['title'] = 'Hired Equipments';
            $data['approval_module_options'] = approval_module_dropdown_options();
            $this->load->model('account');
            $data['expense_accounts_options'] = $this->account->dropdown_options(['DIRECT EXPENSES','INDIRECT EXPENSES']);

            $this->load->model('Asset_group');
            $this->load->model('Inventory_location');
            $data['sub_location_options']=general_sub_location_options();
            $data['asset_group_options']= $this->Asset_group->asset_group_options();

            $this->load->model('Hired_equipment_receipt');
            $this->load->model('Hired_equipment');
            $data['hired_equipments']= $this->Hired_equipment->hired_equipments();
            $data['hired_equipment_receipts']= $this->Hired_equipment_receipt->equipments_receipts();

            $this->load->view('hired_equipments/index', $data);
        }
    }


    public function hired_equipments_list(){

        $this->load->model('Hired_Equipment');

        $posted_params = dataTable_post_params();

             echo $this->Hired_Equipment->hired_equipments_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);

    }

    public function hired_equipments_receipts(){

          $this->load->model('Hired_equipment_receipt');

           $posted_params = dataTable_post_params();

             echo $this->Hired_equipment_receipt->hired_equipments_receipts($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);

    }


    public function save_equipment_receipt(){

        $this->load->model('Hired_equipment_receipt');
        $Hired_equipment_receipt = new Hired_equipment_receipt();
        $edit = $Hired_equipment_receipt->load($this->input->post('equipment_receipt_id'));
        $Hired_equipment_receipt->receipt_date = $this->input->post('receipt_date');
        $Hired_equipment_receipt->vendor_id = $this->input->post('vendor_id');
        $Hired_equipment_receipt->hiring_order_id = NULL;
        $Hired_equipment_receipt->comments = $this->input->post('comments');
        $Hired_equipment_receipt->created_by = $this->session->userdata('employee_id');
        
        if($Hired_equipment_receipt->save()){
            
            if ($edit) {

                $Hired_equipment_receipt->delete_equipment_items();

            } 

            $this->load->model('Hired_equipment');

            $asset_group_ids = $this->input->post('asset_group_ids');
            $equipment_codes = $this->input->post('equipment_codes');
            $rates = $this->input->post('rates');
            $rate_modes = $this->input->post('rate_modes');
            $currency_id = $this->input->post('currency_id');
            

            foreach ($equipment_codes as $index => $equipment_code){
              
                    $Hired_equipment = new Hired_equipment();

                    $Hired_equipment->equipment_receipt_id = $Hired_equipment_receipt->{$Hired_equipment_receipt::DB_TABLE_PK};
                    $Hired_equipment->asset_group_id = $asset_group_ids[$index];
                    $Hired_equipment->equipment_code = $equipment_codes[$index];
                    $Hired_equipment->rate = $rates[$index];
                    $Hired_equipment->rate_mode = $rate_modes[$index];
                    $Hired_equipment->currency_id = $currency_id;
                    $Hired_equipment->save();
            }
        }
       
    }


    public  function delete_hired_equipment_receipt()
    {

        $this->load->model('Hired_equipment_receipt');
        $Hired_equipment_receipt= new Hired_equipment_receipt();
        $obj=$Hired_equipment_receipt->load($this->input->post('equipment_receipt_id'));

        $Hired_equipments=$Hired_equipment_receipt->hired_equipments();

        foreach ($Hired_equipments as $Hired_equipment){

            $Hired_equipment->delete();
        }
        $Hired_equipment_receipt->delete();
        }




}

