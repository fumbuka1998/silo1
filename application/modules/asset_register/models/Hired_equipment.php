<?php

class Hired_equipment extends MY_Model{
    
    const DB_TABLE = 'hired_equipments';
    const DB_TABLE_PK = 'id';

    public $equipment_code;
    public $rate;
    public $rate_mode;
    public $currency_id;
    public $equipment_receipt_id;
    public $asset_group_id;

    public function hired_equipments(){
        $hired_equipments=$this->get();
        return $hired_equipments;
    }


    public function hired_equipments_list1($limit, $start, $keyword, $order){

        /*$asset_group_id=$this->input->post('asset_group_id');

        if($asset_group_id!=''){

            $where=' asset_group_id ="'.$asset_group_id.'" ';
            
        }else{ $where = '';}*/
        $records_total = $this->count_rows();

        $where = '';

        if($keyword != ''){
            
            $where .= ' equipment_code LIKE "%'.$keyword.'%" OR rate LIKE "%'.$keyword.'%" OR rate_mode LIKE "%'.$keyword.'%" OR asset_group_id LIKE "%'.$keyword.'%" ';
        }

        //order string
        $order_string = dataTable_order_string(['equipment_code','rate','rate_mode','asset_group_id'],$order,'equipment_code');

        $hired_equipments = $this->get($limit,$start,$where,$order_string);
        
        $rows = [];
        //$this->load->model('Inventory_location');
       // $this->load->model('Asset_group');
        //$data['sub_location_options']=general_sub_location_options();
        //$data['asset_group_options']= $this->Asset_group->asset_group_options();
        
        foreach ($hired_equipments as $hired_equipment){

           // $data['hired_equipment'] = $hired_equipment;
            $rows[] = [

                 $hired_equipment->equipment_code,
                 $hired_equipment->equipment_code,
                 $hired_equipment->equipment_code,
                 $hired_equipment->equipment_code,
                 $hired_equipment->equipment_code
                //$hired_equipment->equipment_group()->group_name,
                //'<span class="pull-right">' . number_format($hired_equipment->rate) . '</span>',
                //$hired_equipment->rate_mode,
                
                //$this->load->view('assets/asset_actions',$data,true)
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


    public function hired_equipments_list($limit, $start, $keyword, $order){

        $records_total = $this->count_rows();

        $where = '';
        if($keyword != ''){
            $where .= 'equipment_code LIKE "%'.$keyword.'%" OR rate LIKE "%'.$keyword.'%" ';
        }

        //order string
        $order_string = dataTable_order_string(['equipment_code','rate'],$order,'equipment_code');

        
        $hired_equipments = $this->get($limit,$start,$where,$order_string);

     
        $rows = [];
    
        foreach ($hired_equipments as $hired_equipment){

            $data['hired_equipment'] = $hired_equipment;
            $rows[] = [

                 $hired_equipment->equipment_code,
                 $hired_equipment->equipment_group()->group_name,
                 '<span class="pull-right">' . number_format($hired_equipment->rate) . '</span>',
                 $hired_equipment->rate_mode

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



    public function hired_equipments_dropdown_options(){

        $hired_equipments = $this->get();
        $options[''] = '&nbsp;';
        foreach ($hired_equipments as $hired_equipment){
            $options[$hired_equipment->{$this::DB_TABLE_PK}] = $hired_equipment->equipment_code;
        }
        return $options;
    }

   
    public function equipment_group(){

        $this->load->model('Asset_group');
        $Asset_group=new Asset_group();
        $Asset_group->load($this->asset_group_id);
        return $Asset_group;


    }


}