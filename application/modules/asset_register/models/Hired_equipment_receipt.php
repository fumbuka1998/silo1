<?php

class Hired_equipment_receipt extends MY_Model{
    
    const DB_TABLE = 'hired_equipment_receipts';
    const DB_TABLE_PK = 'id';

    public $receipt_date;
    public $vendor_id;
    public $hiring_order_id;
    public $comments;
    public $created_at;
    public $created_by;

     public function delete_equipment_items(){
        $this->db->where('equipment_receipt_id', $this->{$this::DB_TABLE_PK});
        $this->db->delete(['hired_equipments']);
    }

    public function equipments_receipts(){
        $equipments_receipts = $this->get();
        return $equipments_receipts;

    }

    public function hired_equipments(){
        $this->load->model('Hired_equipment');
        $hired_equipment= new Hired_equipment();
        $hired_equipment=$hired_equipment->get(0,0,[
            'equipment_receipt_id' => $this->{$this::DB_TABLE_PK}]);
            return $hired_equipment;

    }

    public function hired_equipments_receipts($limit, $start, $keyword, $order){

        $records_total = $this->count_rows();

        $where = '';

        if($keyword != ''){
            $where .= 'receipt_date LIKE "%'.$keyword.'%" OR hiring_order_id LIKE "%'.$keyword.'%" ';
        }

        //order string
        $order_string = dataTable_order_string(['receipt_date','hiring_order_id'],$order,'receipt_date');

        $hired_equipment_receipts = $this->get($limit,$start,$where,$order_string);

        $rows = [];

        foreach ($hired_equipment_receipts as $hired_equipment_receipt){

            $data['hired_equipment_receipt'] = $hired_equipment_receipt;

            $rows[] = [

                $hired_equipment_receipt->receipt_date,
                $hired_equipment_receipt->vendor()->vendor_name,
                $hired_equipment_receipt->comments,
                $this->load->view('hired_equipments/hired_equipment_receipt_actions',$data,true)
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

    public function vendor()
    {
        $this->load->model('Vendor');
        $Vendor=new Vendor();
        $vendor=$Vendor->get(0,0,['vendor_id'=>$this->vendor_id]);
         return array_shift( $vendor);
    }


}