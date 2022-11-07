<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 8/13/2018
 * Time: 9:49 AM
 */

class Imprest_voucher extends MY_Model{
    const DB_TABLE = 'imprest_vouchers';
    const DB_TABLE_PK = 'id';

    public $imprest_date;
    public $credit_account_id;
    public $debit_account_id;
    public $currency_id;
    public $remarks;
    public $vat_inclusive;
    public $exchange_rate;
    public $handler_id;
    public $created_by;

    public function imprest_voucher_number(){
        return 'IMPV/'.add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function detailed_reference(){
        return $this->requisition()->requisition_number().' - '.$this->imprest_voucher_number();

    }

    public function credit_account()
    {
        $this->load->model('account');
        $account = new Account();
        $account->load($this->credit_account_id);
        return $account;
    }

    public function currency()
    {
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($this->currency_id);
        return $currency;
    }

    public function debit_account()
    {
        $this->load->model('account');
        $account = new Account();
        $account->load($this->debit_account_id);
        return $account;
    }

    public function imprests_list($limit, $start, $keyword, $order){
        $order_string = dataTable_order_string(['imprest_date','remarks','credit_account_id','debit_account_id'],$order,'imprest_date');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $where_clause = '';

        $sql = 'SELECT COUNT(imprest_vouchers.id) AS records_total FROM imprest_vouchers '.$where_clause;

        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;


        if ($keyword != '') {
            $where_clause .= ' WHERE (imprest_date LIKE "%'.$keyword.'%" OR remarks LIKE "%'.$keyword.'%"  OR credit_account_id LIKE "%'.$keyword.'%"  OR debit_account_id LIKE "%'.$keyword.'%" OR
                imprest_vouchers.id IN(
                      SELECT DISTINCT imprest_voucher_id FROM imprest_voucher_asset_items
                      LEFT JOIN requisition_approval_asset_items ON imprest_voucher_asset_items.requisition_approval_asset_item_id = requisition_approval_asset_items.id
                      LEFT JOIN requisition_approvals ON requisition_approval_asset_items.requisition_approval_id = requisition_approvals.id
                      WHERE requisition_id LIKE "%'.$keyword.'%"
                    
                      UNION
                    
                        SELECT DISTINCT imprest_voucher_id FROM imprest_voucher_cash_items
                      LEFT JOIN requisition_approval_cash_items ON imprest_voucher_cash_items.requisition_approval_cash_item_id = requisition_approval_cash_items.id
                      LEFT JOIN requisition_approvals ON requisition_approval_cash_items.requisition_approval_id = requisition_approvals.id
                      WHERE requisition_id  LIKE "%'.$keyword.'%"
                    
                      UNION
                    
                        SELECT DISTINCT imprest_voucher_id FROM imprest_voucher_material_items
                      LEFT JOIN requisition_approval_material_items ON imprest_voucher_material_items.requisition_approval_material_item_id = requisition_approval_material_items.id
                      LEFT JOIN requisition_approvals ON requisition_approval_material_items.requisition_approval_id = requisition_approvals.id
                      WHERE requisition_id  LIKE "%'.$keyword.'%"
                      
                      UNION 
                        
                        SELECT DISTINCT imprest_voucher_id FROM imprest_voucher_service_items
                      LEFT JOIN requisition_approval_service_items ON imprest_voucher_service_items.requisition_approval_service_item_id = requisition_approval_service_items.id
                      LEFT JOIN requisition_approvals ON requisition_approval_service_items.requisition_approval_id = requisition_approvals.id
                      WHERE requisition_id  LIKE "%'.$keyword.'%"
)
              
              )';
        }

        $sql = ' SELECT SQL_CALC_FOUND_ROWS imprest_vouchers.id AS imprest_voucher_id,imprest_date,remarks,credit_account_id FROM imprest_vouchers
                '.$where_clause.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $this->load->model(['material_item','asset_item']);
        $data['asset_options'] = $this->asset_item->dropdown_options();
        $data['material_options'] = $this->material_item->dropdown_options();
        $data['location_options'] = locations_options();
        $data['currency_options'] = currency_dropdown_options();
        $data['account_options'] = account_dropdown_options(['CASH IN HAND', 'BANK']);

        $rows = [];
        foreach ($results as $row){
            $imprest_voucher = new self();
            $imprest_voucher->load($row->imprest_voucher_id);
            $data['imprest_voucher'] = $imprest_voucher;
            $data['retirements'] = $imprest_voucher->retirements();
            $data['balance'] = $imprest_voucher->balance();
            $data['total_quantity'] = $imprest_voucher->total_quantity();
            $data['total_received_quantity'] = $imprest_voucher->total_received_quantity();
            $requisition = $imprest_voucher->requisition();

            $rows[] = [
                custom_standard_date($row->imprest_date),
                $requisition->requisition_number().', '.$imprest_voucher->imprest_voucher_number(),
                $imprest_voucher->credit_account()->account_name,
                $imprest_voucher->debit_account()->account_name,
                '<span style="text-align: right">'.$imprest_voucher->currency()->symbol.' '.number_format($imprest_voucher->total_amount_vat_inclusive(),2).'</span>',
                $imprest_voucher->status(),
                $this->load->view('finance/transactions/approved_cash_requests/imprest/list_actions',$data,true)
            ];

        }

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function material_items(){
        $this->load->model('imprest_voucher_material_item');
        $where['imprest_voucher_id'] = $this->{$this::DB_TABLE_PK};
        return $this->imprest_voucher_material_item->get(0,0,$where);
    }

    public function asset_items(){
        $this->load->model('imprest_voucher_asset_item');
        $where['imprest_voucher_id'] = $this->{$this::DB_TABLE_PK};
        return $this->imprest_voucher_asset_item->get(0,0,$where);
    }

    public function cash_items(){
        $this->load->model('imprest_voucher_cash_item');
        $where['imprest_voucher_id'] = $this->{$this::DB_TABLE_PK};
        return $this->imprest_voucher_cash_item->get(0,0,$where);
    }

    public function service_items(){
        $this->load->model('imprest_voucher_service_item');
        $where['imprest_voucher_id'] = $this->{$this::DB_TABLE_PK};
        return $this->imprest_voucher_service_item->get(0,0,$where);
    }

    public function total_amount(){
        $total_amount= 0;
        $imprest_voucher_asset_items = $this->asset_items();
        if(!empty($imprest_voucher_asset_items)) {
            foreach ($imprest_voucher_asset_items as $imprest_asset_item) {
                $total_amount += $imprest_asset_item->quantity * $imprest_asset_item->rate;
            }
        }

        $imprest_voucher_material_items = $this->material_items();
        if(!empty($imprest_voucher_material_items)) {
            foreach ($imprest_voucher_material_items as $imprest_material_item) {
                $total_amount += $imprest_material_item->quantity * $imprest_material_item->rate;
            }
        }

        $imprest_voucher_cash_items = $this->cash_items();
        if(!empty($imprest_voucher_cash_items)) {
            foreach ($imprest_voucher_cash_items as $imprest_cash_item) {
                $total_amount += $imprest_cash_item->quantity * $imprest_cash_item->rate;

            }
        }

        $imprest_voucher_service_items = $this->service_items();
        if(!empty($imprest_voucher_service_items)) {
            foreach ($imprest_voucher_service_items as $imprest_service_item) {
                $total_amount += $imprest_service_item->quantity * $imprest_service_item->rate;
            }
        }

        return $total_amount;
    }

    public function total_amount_vat_inclusive(){
        return $this->total_amount() + $this->vat_amount();
    }

    public function status(){
        $balance = $this->balance();
        $total_quantity = $this->total_quantity();
        $total_received_quantity = $this->total_received_quantity();
        if ($balance == $total_quantity && $total_received_quantity == 0) {
            $status = '<span class="label label-info">Not Retired</span>';
        } else if (($balance == 0 && $total_quantity == $total_received_quantity) || $total_received_quantity > $total_quantity) {
            $status = '<span class="label label-success">Retired</span>';
        } else if( $balance != $total_quantity && $total_received_quantity > 0 && $total_received_quantity < $total_quantity){
            $status = '<span class="label" style="background-color: #00ca6d; font-size: 10px;">Partial Retirement(s)</span>';
        }
        return $status;
    }

    public function grns(){
        $sql = 'SELECT goods_received_notes.grn_id AS grn_id FROM goods_received_notes
                LEFT JOIN imprest_voucher_retirement_grns ON goods_received_notes.grn_id = imprest_voucher_retirement_grns.grn_id
                LEFT JOIN imprest_voucher_retirements ON imprest_voucher_retirement_grns.imprest_voucher_retirement_id = imprest_voucher_retirements.id
                LEFT JOIN imprest_vouchers ON imprest_voucher_retirements.imprest_voucher_id = imprest_vouchers.id
                WHERE imprest_voucher_id='.$this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        $results = $query->result();
        $options = [];
        $this->load->model('goods_received_note');
        foreach ($results as $row) {
            $grn = new Goods_received_note();
            $grn->load($row->grn_id);
            $options[] = $grn;
        }
        return $options;
    }

    private function requisition_approval_id($item_types = ['material','asset','cash','service']){
        $this->load->model([
            'imprest_voucher_material_item',
            'imprest_voucher_asset_item',
            'imprest_voucher_cash_item',
            'imprest_voucher_service_item',
            'requisition_approval_material_item',
            'requisition_approval_asset_item',
            'requisition_approval_cash_item',
            'requisition_approval_service_item',
        ]);

        foreach ($item_types as $item_type){
            $iv_items_model = 'imprest_voucher_' . $item_type . '_item';
            $imprest_voucher_items = $this->$iv_items_model->get(0,0,['imprest_voucher_id' => $this->{$this::DB_TABLE_PK}]);
            if(!empty($imprest_voucher_items)){
                foreach ($imprest_voucher_items as $imprest_voucher_item){
                    $model = 'requisition_approval_'.$item_type.'_item';
                    $requisition_approval_item_id = 'requisition_approval_'.$item_type.'_item_id';
                    $found_items = $this->$model->get(0,0,['id'=> $imprest_voucher_item->$requisition_approval_item_id]);
                    if(!empty($found_items)){
                        foreach($found_items as $found_item){
                            return $found_item->requisition_approval_id;
                            break;
                        }
                    } else{
                        false;
                        break;
                    }
                }
            }
        }
    }

    public function requisition_approval(){
        $this->load->model('requisition_approval');
        $requisition_approval = new Requisition_approval();
        $requisition_approval->load($this->requisition_approval_id());
        return $requisition_approval;
    }

    public function requisition(){
        return $this->requisition_approval()->requisition();
    }

    public function cost_center_name(){
        return $this->requisition()->cost_center_name();
    }

    public function retirements(){
        $this->load->model('imprest_voucher_retirement');
        $where['imprest_voucher_id'] = $this->{$this::DB_TABLE_PK};
        $retirements = $this->imprest_voucher_retirement->get(0,0,$where);
        return !empty($retirements) ? $retirements : false;
    }

    public function retirement(){
        $this->load->model('imprest_voucher_retirement');
        $where['imprest_voucher_id'] = $this->{$this::DB_TABLE_PK};
        $retirements = $this->imprest_voucher_retirement->get(0,0,$where);
        foreach ($retirements as $retirement){
            return $retirement;
        }
    }

    public function imprest_voucher_retirement_grn(){
        $this->load->model('imprest_voucher_retirement');
        $where['imprest_voucher_id'] = $this->{$this::DB_TABLE_PK};
        $retirements = $this->imprest_voucher_retirement->get(0,0,$where);
        foreach($retirements as $retirement){
             $retirement_grns = $retirement->imprest_voucher_retirement_grns();
             if(!empty($retirement_grns)){
                 foreach ($retirement_grns as $grn){
                     return $grn;
                 }
             } else {
                 return false;
             }
        }
    }

    public function balance(){
        $total_quantity = $this->total_quantity();
        $total_received_quantity = $this->total_received_quantity();
        return $total_quantity - $total_received_quantity;
    }

    public function total_quantity(){
        $total_quantity = 0;
        $imprest_voucher_material_items = $this->material_items();
        if(!empty($imprest_voucher_material_items)) {
            foreach ($imprest_voucher_material_items as $imprest_material_item) {
                $approved_material_item = $imprest_material_item->requisition_approval_material_item();
                $total_quantity += $approved_material_item->approved_quantity;
            }
        }

        $imprest_voucher_asset_items = $this->asset_items();
        if(!empty($imprest_voucher_asset_items)) {
            foreach ($imprest_voucher_asset_items as $imprest_asset_item) {
                $approved_asset_item = $imprest_asset_item->requisition_approval_asset_item();
                $total_quantity += $approved_asset_item->approved_quantity;
            }
        }

        $imprest_voucher_cash_items = $this->cash_items();
        if(!empty($imprest_voucher_cash_items)) {
            foreach ($imprest_voucher_cash_items as $imprest_cash_item) {
                $approved_cash_item = $imprest_cash_item->requisition_approval_cash_item();
                $total_quantity += $approved_cash_item->approved_quantity;
            }
        }

        $imprest_voucher_service_items = $this->service_items();
        if(!empty($imprest_voucher_service_items)) {
            foreach ($imprest_voucher_service_items as $imprest_service_items) {
                $approved_service_item = $imprest_service_items->requisition_approval_service_item();
                $total_quantity += $approved_service_item->approved_quantity;
            }
        }

        return $total_quantity;
    }

    public function total_received_quantity(){
        return $this->material_received() + $this->asset_received() + $this->service_received() + $this->cash_received();
    }

    public function material_received(){
        $received_material_quantity = 0;
        $imprest_voucher_material_items = $this->material_items();
        foreach ($imprest_voucher_material_items as $imprest_voucher_material_item) {
            $approved_material_item = $imprest_voucher_material_item->requisition_approval_material_item();
            $material = $approved_material_item->material_item();
            $item_id = $material->{$material::DB_TABLE_PK};
            $received_material_quantity += $imprest_voucher_material_item->retired_material($this->{$this::DB_TABLE_PK},$item_id);
        }
        return $received_material_quantity;
    }

    public function asset_received(){
        $received_asset_quantity = 0;
        $imprest_voucher_asset_items = $this->asset_items();
        foreach ($imprest_voucher_asset_items as $imprest_voucher_asset_item) {
            $approved_asset_item = $imprest_voucher_asset_item->requisition_approval_asset_item();
            $asset_item = $approved_asset_item->requisition_asset_item()->asset_item();
            $asset_item_id = $asset_item->{$asset_item::DB_TABLE_PK};
            $received_asset_quantity += $imprest_voucher_asset_item->retired_asset($this->{$this::DB_TABLE_PK},$asset_item_id);
        }
        return $received_asset_quantity;
    }

    public function cash_received(){
        $received_cash_quantity = 0;
        $this->load->model('imprest_voucher_retirement');
        $where['imprest_voucher_id'] = $this->{$this::DB_TABLE_PK};
        $retirements = $this->imprest_voucher_retirement->get(0,0,$where);
        foreach($retirements as $retirement){
            $retired_cash = $retirement->retired_cash();
            foreach ($retired_cash as $cash) {
                $received_cash_quantity += $cash->quantity;
            }
        }
        return $received_cash_quantity;
    }

    public function service_received(){
        $received_service_quantity = 0;
        $this->load->model('imprest_voucher_retirement');
        $where['imprest_voucher_id'] = $this->{$this::DB_TABLE_PK};
        $retirements = $this->imprest_voucher_retirement->get(0,0,$where);
        foreach ($retirements as $retirement){
            $retired_services = $retirement->retired_services();
            foreach ($retired_services as $retired_service) {
                $received_service_quantity += $retired_service->quantity;
            }
        }
        return $received_service_quantity;
    }

    public function cost_figure($cost_center_id,$level, $from = null, $to = null){
        if($level == 'project' || $level == 'project_overall') {
            $sql = 'SELECT (
        
                (
                    SELECT COALESCE(SUM(approved_quantity * approved_rate * exchange_rate),0) FROM requisition_approval_service_items
                    LEFT JOIN imprest_voucher_service_items ON requisition_approval_service_items.id = imprest_voucher_service_items.requisition_approval_service_item_id
                    LEFT JOIN project_imprest_voucher_items ON imprest_voucher_service_items.id = project_imprest_voucher_items.imprest_voucher_service_item_id
                    LEFT JOIN imprest_vouchers ON imprest_voucher_service_items.imprest_voucher_id = imprest_vouchers.id
                    WHERE project_id = ' . $cost_center_id;
                    if (!is_null($from)) {
                        $sql .= ' AND imprest_date >= "' . $from . '" ';
                    }

                    if (!is_null($to)) {
                        $sql .= ' AND imprest_date <= "' . $to . '" ';
                    }

            $sql .= ') + (
                    SELECT COALESCE(SUM(approved_quantity * approved_rate * exchange_rate),0) FROM requisition_approval_cash_items
                    LEFT JOIN imprest_voucher_cash_items ON requisition_approval_cash_items.id = imprest_voucher_cash_items.requisition_approval_cash_item_id
                    LEFT JOIN project_imprest_voucher_items ON imprest_voucher_cash_items.id = project_imprest_voucher_items.imprest_voucher_cash_item_id
                    LEFT JOIN imprest_vouchers ON imprest_voucher_cash_items.imprest_voucher_id = imprest_vouchers.id
                    WHERE project_id = ' . $cost_center_id;
                    if (!is_null($from)) {
                        $sql .= ' AND imprest_date >= "' . $from . '" ';
                    }

                    if (!is_null($to)) {
                        $sql .= ' AND imprest_date <= "' . $to . '" ';
                    }

                    $sql .= '
                )
        
            ) AS cost_figure';

        } else {

            $sql = 'SELECT (
        
                (
                    SELECT COALESCE(SUM(approved_quantity * approved_rate * exchange_rate),0) FROM requisition_approval_service_items
                    LEFT JOIN imprest_voucher_service_items ON requisition_approval_service_items.id = imprest_voucher_service_items.requisition_approval_service_item_id
                    LEFT JOIN cost_center_imprest_voucher_items ON imprest_voucher_service_items.id = cost_center_imprest_voucher_items.imprest_voucher_service_item_id
                    LEFT JOIN imprest_vouchers ON imprest_voucher_service_items.imprest_voucher_id = imprest_vouchers.id
                    WHERE cost_center_id = ' . $cost_center_id;
            if (!is_null($from)) {
                $sql .= ' AND imprest_date >= "' . $from . '" ';
            }

            if (!is_null($to)) {
                $sql .= ' AND imprest_date <= "' . $to . '" ';
            }

            $sql .= ') + (
                    SELECT COALESCE(SUM(approved_quantity * approved_rate * exchange_rate),0) FROM requisition_approval_cash_items
                    LEFT JOIN imprest_voucher_cash_items ON requisition_approval_cash_items.id = imprest_voucher_cash_items.requisition_approval_cash_item_id
                    LEFT JOIN cost_center_imprest_voucher_items ON imprest_voucher_cash_items.id = cost_center_imprest_voucher_items.imprest_voucher_cash_item_id
                    LEFT JOIN imprest_vouchers ON imprest_voucher_cash_items.imprest_voucher_id = imprest_vouchers.id
                    WHERE cost_center_id = ' . $cost_center_id;
            if (!is_null($from)) {
                $sql .= ' AND imprest_date >= "' . $from . '" ';
            }

            if (!is_null($to)) {
                $sql .= ' AND imprest_date <= "' . $to . '" ';
            }

            $sql .= '
                )
        
            ) AS cost_figure';
        }

        $query = $this->db->query($sql);
        return $query->row()->cost_figure;

    }

    public function update_vat_info(){
        $requisition_approval = $this->requisition_approval();
        if($requisition_approval){
            $imprest_voucher = new self();
            $imprest_voucher->load($this->{$this::DB_TABLE_PK});
            $imprest_voucher->vat_inclusive = $requisition_approval->vat_inclusive;
            $imprest_voucher->save();
        }
    }

    public function vat_amount(){
        if($this->vat_inclusive == 'VAT PRICED') {
            $total_amount_vat_exclusive = $this->total_amount()/1.18;
            $vat_amount = $this->total_amount() - $total_amount_vat_exclusive;
            return $vat_amount;
        } else if($this->vat_inclusive == 'VAT COMPONENT') {
            return (18 * 0.01 * $this->total_amount());
        } else {
            return 0;
        }
    }

    public function has_project_items(){
        $sql = 'SELECT * FROM (
                  SELECT id FROM imprest_voucher_service_items AS main1
                  WHERE id IN (
                    SELECT imprest_voucher_service_item_id FROM project_imprest_voucher_items
                    WHERE project_imprest_voucher_items.imprest_voucher_service_item_id = main1.id
                  )
                  AND imprest_voucher_id = '.$this->{$this::DB_TABLE_PK}.' 
    
                  UNION 
                  SELECT id FROM imprest_voucher_cash_items AS main2
                  WHERE id IN (
                    SELECT imprest_voucher_cash_item_id FROM project_imprest_voucher_items
                    WHERE project_imprest_voucher_items.imprest_voucher_cash_item_id = main2.id
                  ) 
                  AND imprest_voucher_id = '.$this->{$this::DB_TABLE_PK}.'
                ) AS project_imprest_items
                ';
        return $this->db->query($sql)->num_rows() > 0 ? true : false;
    }


}
