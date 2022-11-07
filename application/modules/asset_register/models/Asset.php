<?php

class Asset_old extends MY_Model{
    
    const DB_TABLE = 'assets';
    const DB_TABLE_PK = 'id';

    public $asset_name;
    public $asset_code;
    public $book_value;
    public $useful_life;
    public $salvage_value;
    public $status;
    public $asset_group_id;
    public $sub_location_id;
    public $registration_date;
    public $created_by;
    public $created_at;



    public function assets_list($limit, $start, $keyword, $order){

        $asset_group_id=$this->input->post('asset_group_id');

        if($asset_group_id!=''){

            $where=' asset_group_id ="'.$asset_group_id.'" ';
            
        }else{ $where = '';}

        $records_total = $this->count_rows($where);
       
        if($keyword != ''){
            $where .= $where != '' ? ' AND ' : '';
            $where .= ' (asset_name LIKE "%'.$keyword.'%" OR asset_code LIKE "%'.$keyword.'%" OR book_value LIKE "%'.$keyword.'%" OR status LIKE "%'.$keyword.'%" )';
        }

        //order string
        $order_string = dataTable_order_string(['asset_name','asset_code','book_value','status','registration_date'],$order,'asset_name');

        $assets = $this->get($limit,$start,$where,$order_string);
        
        $rows = [];
        $this->load->model('Inventory_location');
        $this->load->model('Asset_group');
        $data['sub_location_options']=general_sub_location_options();
        $data['asset_group_options']= $this->Asset_group->asset_group_options();
        
        foreach ($assets as $asset){
            $data['asset'] = $asset;
            $rows[] = [

                $asset->asset_name,
                $asset->asset_code,
                $asset->Asset_group()->group_name,
                '<span class="pull-right">' . number_format($asset->book_value) . '</span>',
                $asset->status,
                custom_standard_date($asset->registration_date),
                $this->load->view('assets/asset_actions',$data,true)
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

    public function asset_dropdown_options(){

        $assets = $this->get();
        $options[''] = '&nbsp;';
        foreach ($assets as $asset){
            $options[$asset->{$this::DB_TABLE_PK}] = $asset->asset_name;
        }
        return $options;
    }

    public  function asset_list_filter($asset_group_id='all',$location_id='all'){

            $this->db->select()->from('assets');
            $this->db->join('asset_groups','asset_groups.id=assets.asset_group_id');

             if($asset_group_id !=''){

                 $this->db->where('asset_groups.id',$asset_group_id);
              }

              if($location_id != ''){

                 $this->db->where('sub_location_id',$location_id);
              }

            $query=$this->db->get();
            return $query->result_array();

        }

    public function asset_group(){

            $this->load->model('Asset_group');
            $Asset_group=new Asset_group();
            $Asset_group->load($this->asset_group_id);
            return $Asset_group;


        }


}