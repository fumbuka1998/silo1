<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 9/1/2018
 * Time: 9:26 AM
 */

class Imprest_voucher_retirement extends MY_Model{
    const DB_TABLE = ' imprest_voucher_retirements';
    const DB_TABLE_PK = 'id';

    public $retirement_date;
    public $imprest_voucher_id;
    public $retirement_to;
    public $is_examined;
    public $location_id;
    public $sub_location_id;
    public $examination_date;
    public $vat_inclusive;
    public $remarks;
    public $examined_by;
    public $created_by;


    public function imprest_voucher_retirement_number(){
        $imprest_voucher = $this->imprest_voucher();
        return 'IMPV/'.$imprest_voucher->{$imprest_voucher::DB_TABLE_PK}.'/IMPV-R/'.$this->{$this::DB_TABLE_PK};
    }

    public function created_by(){
        $this->load->model('employee');
        $created_by = new Employee();
        $created_by->load($this->created_by);
        return $created_by;
    }

    public function examined_by(){
        $this->load->model('employee');
        $examined_by = new Employee();
        $examined_by->load($this->examined_by);
        return $examined_by;
    }

    public function location(){
        $this->load->model('inventory_location');
        $inventory_location = new Inventory_location();
        $inventory_location->load($this->location_id);
        return $inventory_location;
    }
    public function sub_location(){
        $this->load->model('sub_location');
        $sub_location = new Sub_location();
        $sub_location->load($this->sub_location_id);
        return $sub_location;
    }
    public function imprest_voucher(){
        $this->load->model('imprest_voucher');
        $imprest_voucher = new Imprest_voucher();
        $imprest_voucher->load($this->imprest_voucher_id);
        return $imprest_voucher;
    }

    public function imprest_voucher_retirement_grn_junction($grn_id){
        $this->load->model('imprest_voucher_retirement_grn');
        $imprest_voucher_retirement_grn = new Imprest_voucher_retirement_grn();
        $imprest_voucher_retirement_grn->grn_id = $grn_id;
        $imprest_voucher_retirement_grn->imprest_voucher_retirement_id = $this->{$this::DB_TABLE_PK};
        $imprest_voucher_retirement_grn->save();
    }

    public function retired_material_items(){
        $this->load->model('imprest_voucher_retirement_material_item');
        $where['imprest_voucher_retirement_id'] = $this->{$this::DB_TABLE_PK};
        return $this->imprest_voucher_retirement_material_item->get(0,0,$where,'id DESC');
    }

    public function retired_asset_items(){
        $this->load->model('imprest_voucher_retirement_asset_item');
        $where['imprest_voucher_retirement_id'] = $this->{$this::DB_TABLE_PK};
        return $this->imprest_voucher_retirement_asset_item->get(0,0,$where,'id DESC');
    }

    public function retired_cash(){
        $this->load->model('imprest_voucher_retired_cash');
        $where['imprest_voucher_retirement_id'] = $this->{$this::DB_TABLE_PK};
        return $this->imprest_voucher_retired_cash->get(0,0,$where,'id DESC');
    }

    public function retired_services(){
        $this->load->model('imprest_voucher_retired_service');
        $where['imprest_voucher_retirement_id'] = $this->{$this::DB_TABLE_PK};
        return $this->imprest_voucher_retired_service->get(0,0,$where,'id DESC');
    }

    public function imprest_voucher_retirement_grns(){
        $this->load->model('imprest_voucher_retirement_grn');
        $where['imprest_voucher_retirement_id'] = $this->{$this::DB_TABLE_PK};
        $retirement_grns = $this->imprest_voucher_retirement_grn->get(0,0,$where);
        return !empty($retirement_grns) ? $retirement_grns : false;
    }

    public function imprest_voucher_retirement_grn(){
        $this->load->model('imprest_voucher_retirement_grn');
        $where['imprest_voucher_retirement_id'] = $this->{$this::DB_TABLE_PK};
        $retirement_grns = $this->imprest_voucher_retirement_grn->get(0,0,$where);
        if(!empty($retirement_grns)) {
            foreach ($retirement_grns as $retirement_grn) {
                return $retirement_grn;
            }
        } else {
            return false;
        }
    }

    public function delete_retired_items(){
        $this->db->delete('imprest_voucher_retirement_material_items',['imprest_voucher_retirement_id'=>$this->{$this::DB_TABLE_PK}]);
        $this->db->delete('imprest_voucher_retirement_asset_items',['imprest_voucher_retirement_id'=>$this->{$this::DB_TABLE_PK}]);
        $this->db->delete('imprest_voucher_retired_cash',['imprest_voucher_retirement_id'=>$this->{$this::DB_TABLE_PK}]);
        $this->db->delete('imprest_voucher_retired_services',['imprest_voucher_retirement_id'=>$this->{$this::DB_TABLE_PK}]);
    }

    public function retirements_examination_list($limit,$start,$keyword,$order,$imprest_voucher_id){
        $where=' imprest_voucher_id = '.$imprest_voucher_id.'';
        if ($keyword!=''){
            if($where != ''){
                $where .= ' AND ';
            }
            $where.=' (output_per_day LIKE "%'.$keyword.'%" ) AND is_examined = "0"';
        }

        $imprest_voucher_retirements = $this->get($limit,$start,$where,'id ASC');

        $rows=[];
        $data['expense_account_options'] = account_dropdown_options(['DIRECT EXPENSES', 'INDIRECT EXPENSES']);
        foreach ($imprest_voucher_retirements as $row){
            $retirement= new self();
            $retirement->load($row->id);
            $data['retirement'] = $retirement;
            $rows[] = [
                $retirement->imprest_voucher_retirement_number(),
                $retirement->status(),
                $this->load->view('finance/transactions/approved_cash_requisitions/imprest/retirement_examination_list_action',$data,true)
            ];
        }

        $data['data']=$rows;
        $data['recordsFiltered']= $this->count_rows($where);
        $data['recordsTotal']= $this->count_rows(['imprest_voucher_id' => $imprest_voucher_id]);
        return json_encode($data);

    }

    public function status(){
        $is_examined = $this->is_examined;
        if($is_examined == 2){
            $status = '<span class="label label-danger">Rejected</span>';
        } else if($is_examined == 1){
            $status = '<span class="label label-success">Accepted</span>';
        } else {
            $status = '<span class="label label-warning">Awaits Examination</span>';
        }
        return $status;
    }

    public function update_vat_info(){
        $imprest_voucher = $this->imprest_voucher();
        $imprest_voucher_retirement = new self();
        $imprest_voucher_retirement->load($this->{$this::DB_TABLE_PK});
        $imprest_voucher_retirement->vat_inclusive = $imprest_voucher->vat_inclusive;
        $imprest_voucher_retirement->save();
    }
}