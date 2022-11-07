<?php

class Assets extends CI_Controller{
    
    public function __construct() {
        parent::__construct();
        check_login();
     }

    public function index(){

            $data['title'] = 'Asset Register';
          
            $this->load->view('index',$data);
     
    }

    public function settings(){

        $data['title'] = 'Asset Settings';

        $this->load->model('Asset_depreciation_rate');
        $this->load->model('Asset_depreciation_rate_item');
        //$data['depreciation_rates']= $this->Asset_depreciation_rate->depreciation_rates();
       $data['depreciation_items'] = $this->Asset_depreciation_rate_item->depreciation_rate_items();

        $this->load->model('Asset_group');
        $data['asset_groups']= $this->Asset_group->asset_groups_list();
        $data['parent_group_options'] = $this->Asset_group->asset_group_options();

        $this->load->view('asset_register/settings/index',$data);
    }

    public function assets_list(){

    $this->load->model('Asset');

    $posted_params = dataTable_post_params();

    if($posted_params['limit']!=null){

     echo $this->Asset->assets_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }else{

    $this->load->model('Asset_group');
    $this->load->model('Inventory_location');
    $data['sub_location_options']=general_sub_location_options();
    $data['asset_group_options']= $this->Asset_group->asset_group_options();
    $data['title'] = 'Asset List';
    $this->load->view('assets/index',$data);

    }


    }

    public function save_Asset(){

                $this->load->model('asset');

                 $quantity=$this->input->post('quantity');
                 $initial_code=$this->input->post('initial_code');

                if( $this->input->post('asset_id') == ''){


                    for($i=1; $i<=$quantity; $i++){

                        $asset_code=$this->input->post('asset_name').'/'.$initial_code;

                        $initial_code++;

                        $asset = new asset();
                        $edit = $asset->load($this->input->post('asset_id'));
                        $asset->description = $this->input->post('description');
                        $asset->created_by = $this->session->userdata('employee_id');
                        $asset->asset_name = $this->input->post('asset_name');
                        $asset->asset_code = $asset_code;
                        $asset->book_value = $this->input->post('book_value');
                        $asset->asset_group_id = $this->input->post('asset_group_id');
                        $asset->sub_location_id = $this->input->post('sub_location_id');
                        $asset->registration_date = $this->input->post('registration_date');
                        $asset->status = 'existing';
                        $asset->save();
                    }

                }else{

                        $asset = new asset();
                        $edit = $asset->load($this->input->post('asset_id'));
                        $asset->description = $this->input->post('description');
                        $asset->created_by = $this->session->userdata('employee_id');
                        $asset->asset_name = $this->input->post('asset_name');       
                        $asset->asset_code = $this->input->post('asset_code'); 
                        $asset->book_value = $this->input->post('book_value');
                        $asset->asset_group_id = $this->input->post('asset_group_id');
                        $asset->sub_location_id = $this->input->post('sub_location_id');
                        $asset->registration_date = $this->input->post('registration_date');
                        $asset->status = 'existing';
                        $asset->save();
                   

                }

               


    }

    public function delete_asset(){
    $this->load->model('asset');
    $asset = new asset();
    if($asset->load($this->input->post('asset_id'))){

    $notification=$asset->delete();
    echo $notification;
    }
    }



}

