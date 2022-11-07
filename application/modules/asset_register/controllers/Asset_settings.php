<?php
class Asset_settings extends CI_Controller{



    public function __construct ()

    {
        parent::__construct();
        check_login();

    }


   
/********************
 *ASSET GROUP
 *******************/


    public function asset_group_list(){

        $this->load->model('asset_group');
        $posted_params = dataTable_post_params();
        echo $this->Asset_group->asset_group_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);

    }

    public function save_asset_group(){

        $this->load->model('asset_group');
        $group = new Asset_group();
        $edit = $group->load($this->input->post('group_id'));
        $group->description = $this->input->post('description');
        $group->created_at = datetime();
        $group->created_by = $this->session->userdata('employee_id');
        $group->group_name = $this->input->post('group_name');
        $group->parent_id = $this->input->post('parent_id');
        $parent = $group->parent();
        $group->level = $parent->level+1;
        $group->project_nature_id = $parent->project_nature_id;
        $group->save();

    }

    public function delete_asset_group(){
        $this->load->model('asset_group');
        $group = new Asset_group();
        if($group->load($this->input->post('group_id'))){
            $group->delete();
        }
    }

    public function group_list(){

        $this->load->model('asset_group');
        $groups = $this->asset_group->get();
        foreach ($groups as $group){
            inspect_object($group);
            inspect_object($group->children_groups());
        }
    }


 /********************
 *ASSET DEPRECIATION
 *******************/

    public function load_depreciation_content()
    {
        $this->load->model('Asset_depreciation_rate');
        $Asset_depreciation_rate = new Asset_depreciation_rate();
        $data['depreciation_rates'] = $Asset_depreciation_rate->depreciation_rates();
        $return['table'] = $this->load->view('settings/depreciation/rate_body_contents', $data, true);
        echo json_encode($return);

    }

    public function load_depreciation_rate_items(){
        $this->load->model('Asset_depreciation_rate');
        $Asset_depreciation_rate = new Asset_depreciation_rate();
        if($Asset_depreciation_rate->load($this->input->post('depreciation_rate_id'))){
            $data['depreciation_rate']=$Asset_depreciation_rate;
            $data['asset_groups'] = $Asset_depreciation_rate->joined_asset_group($this->input->post('depreciation_rate_id'));
            $data['depreciation_rate_items'] = $Asset_depreciation_rate->depreciation_rate_items();
            $return['table'] = $this->load->view('settings/depreciation/depreciation_rate_items',$data,true);
            echo json_encode($return);
        }
    }

    public function save_depreciation_rate(){

        $this->load->model('Asset_depreciation_rate');
        $Asset_depreciation_rate = new Asset_depreciation_rate();
        $edit = $Asset_depreciation_rate->load($this->input->post('depreciation_rate_id'));
        $Asset_depreciation_rate->start_date=$this->input->post('start_date');
        $Asset_depreciation_rate->created_at = datetime();
        $Asset_depreciation_rate->created_by = $this->session->userdata('employee_id');

        if($Asset_depreciation_rate->save()){
            if( $edit){
            }
            $this->load->model('Asset_depreciation_rate_item');
            $items_to_delete = $this->Asset_depreciation_rate_item->get(0, 0, ['asset_depreciation_rate_id' => $this->input->post('depreciation_rate_id')], ' id desc');
            $Asset_depreciation_rate_item = $items_to_delete;
                foreach ($Asset_depreciation_rate_item as $item){
                    $item->delete();
                }
            //save new items
            $this->load->model('Asset_depreciation_rate_item');
            $asset_group_ids = $this->input->post('asset_group_ids');
            $depreciation_rates = $this->input->post('depreciation_rates');

            foreach ($depreciation_rates as $index => $depreciation_rate) {
                $Asset_depreciation_rate_item = new Asset_depreciation_rate_item();
                $Asset_depreciation_rate_item->asset_group_id = $asset_group_ids[$index];
                $Asset_depreciation_rate_item->rate = $depreciation_rates[$index];
                $Asset_depreciation_rate_item->asset_depreciation_rate_id = $Asset_depreciation_rate->{$Asset_depreciation_rate::DB_TABLE_PK};
                   if($depreciation_rates[$index]!= 0 || $depreciation_rates[$index]!=''){

                       $Asset_depreciation_rate_item->save();
                   }
            }
        }

    }

    public function delete_depreciation_rate(){

        $this->load->model('Asset_depreciation_rate');
        $Asset_depreciation_rate= new Asset_depreciation_rate();
        if ($Asset_depreciation_rate->load($this->input->post('depreciation_rate_id')))
        {
            $Asset_depreciation_rate->delete();
        }
    }


}
