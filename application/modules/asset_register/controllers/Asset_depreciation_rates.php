<?php
class Asset_depreciation_rates extends CI_Controller
{

    public function __construct ()

    {
        parent::__construct();
        check_login();

    }

    function depreciation_rates(){

        $this->load->model('Asset_depreciation_rate');
        $data['depreciation_rates']= $this->Asset_depreciation_rate->depreciation_rates();
        $data['depreciation_rate_items']= $this->Asset_depreciation_rate->depreciation_rate_items();
        $this->load->view('asset_register/settings',$data);
    }
   
    public function depreciation_rate_item_list()
    {
        $this->load->model('Asset_depreciation_rate');
        $posted_params = dataTable_post_params();
        echo $this->asset_depreciation_rate-> depreciation_rate_item_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);

    }

    public function save(){

        $this->load->model('Asset_depreciation_rate');
        $Asset_depreciation_rate = new Asset_depreciation_rate();
        $edit = $Asset_depreciation_rate->load($this->input->post('asset_depreciation_rate_id'));
        $Asset_depreciation_rate->start_date=$this->input->post('start_date');
        $Asset_depreciation_rate->created_at = datetime();
        $Asset_depreciation_rate->created_by = $this->session->userdata('employee_id');

            if($Asset_depreciation_rate->save()){

                $asset_group_ids = $this->input->post('asset_group_ids');
                $depreciation_rates = $this->input->post('rates');

                    $this->load->model('Asset_depreciation_rate_item');

                    foreach ($depreciation_rates as $index => $depreciation_rate) {

                       $Asset_depreciation_rate_item = new Asset_depreciation_rate_item();

                        $Asset_depreciation_rate_item->asset_group_id = $asset_group_ids[$index];
                        $Asset_depreciation_rate_item->rate = $depreciation_rates[$index];
                        $Asset_depreciation_rate_item->asset_depreciation_rate_id = $Asset_depreciation_rate->{$Asset_depreciation_rate::DB_TABLE_PK};
                        $Asset_depreciation_rate_item->save();


                    }



            }

    }

}