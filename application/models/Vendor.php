<?php

class Vendor extends MY_Model{

    const DB_TABLE = 'vendors';
    const DB_TABLE_PK = 'vendor_id';

    public $vendor_name;
    public $phone;
    public $alternative_phone;
    public $email;
    public $address;
    public $account_id;


    public function account()
    {
        $this->load->model('account');
        $account = new Account();
        $account->load($this->account_id);
        return $account;
    }

    public function accounts_dropdown_options()
    {
        $account = $this->account();
        return [$account->{$account::DB_TABLE_PK} => $account->account_name];
    }

    public function vendor_options()
    {
        $options[''] = '&nbsp;';
        $vendors = $this->get(0,0,['active' => '1'],'vendor_name');
        foreach($vendors as $vendor){
            $options[$vendor->{$this::DB_TABLE_PK}] = $vendor->vendor_name;
        }
        return $options;
    }

    public function vendors_list($limit, $start, $keyword, $order){
        //order string
        $order_string = dataTable_order_string(['vendor_name','phone','alternative_phone','email','address'],$order,'vendor_name');

        $where = '';
        if($keyword != ''){
            $where .= 'vendor_name LIKE "%'.$keyword.'%" OR phone LIKE "%'.$keyword.'%" OR alternative_phone LIKE "%'.$keyword.'%"  OR email LIKE "%'.$keyword.'%"  OR address LIKE "%'.$keyword.'%" ';
        }

        $vendors = $this->get($limit,$start,$where,$order_string);
        $rows = [];
        foreach($vendors as $vendor){
            $rows[] = [
                anchor(base_url('procurements/vendor_profile/'.$vendor->{$vendor::DB_TABLE_PK}),$vendor->vendor_name),
                $vendor->phone,
                $vendor->alternative_phone,
                $vendor->email,
                $vendor->address
            ];
        }
        $records_filtered = $this->count_rows($where);
        $records_total = $this->count_rows();
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function supplied_items($from = null, $to = null)
    {
        $sql = 'SELECT goods_received_note_material_stock_items.item_id FROM goods_received_note_material_stock_items
                LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                LEFT JOIN purchase_orders ON purchase_order_grns.purchase_order_id = purchase_orders.order_id
                LEFT JOIN material_stocks ON goods_received_note_material_stock_items.stock_id = material_stocks.stock_id
                WHERE vendor_id = '.$this->{$this::DB_TABLE_PK};
        if(!is_null($from)){
            $sql .= ' AND issue_date >= "'.$from.'" ';
        }

        if(!is_null($to)){
            $sql .= ' AND issue_date <= "'.$to.'" ';
        }
        $sql .= ' ORDER BY date_received ASC';
        $query = $this->db->query($sql);

        $supplied_items = [];
        $rows = $query->result();

        $this->load->model('goods_received_note_material_stock_item');
        foreach ($rows as $row){
            $grn_item = new Goods_received_note_material_stock_item();
            $grn_item->load($row->item_id);
            $material_stock = $grn_item->stock_item();
            $material_item = $material_stock->material_item();
            $order_item = $grn_item->order_material_item();
            if(!(isset($last_order_id) && $last_order_id == $order_item->order_id)){
                $purchase_order = $order_item->purchase_order();
                $currency = $purchase_order->currency()->symbol;
            }

            $last_order_id = $order_item->order_id;

            $supplied_items[] = [
                'item_name' => $material_item->item_name,
                'unit' => $material_item->unit()->symbol,
                'quantity' => $material_stock->quantity,
                'price' => $order_item->price,
                'receiving_price' => $grn_item->receiving_price(),
                'order_number' => $purchase_order->order_number(),
                'date_delivered' => custom_standard_date($material_stock->date_received),
                'currency' => $currency,
                'cost_center_name' => $purchase_order->cost_center_name()
            ];
        }

        return $supplied_items;
    }

    public function invoices($from = null,$to = null,$status = ['unpaid','approved'])
    {
        $this->load->model('invoice');
        $sql = 'SELECT invoice_id FROM vendor_invoices
                LEFT JOIN invoices ON vendor_invoices.invoice_id = invoices.id
                WHERE vendor_id = '.$this->{$this::DB_TABLE_PK};

        if(in_array("unpaid", $status)){
            $sql .= ' AND 
                (amount - (
                  SELECT COALESCE(SUM(amount),0) FROM payment_voucher_items
                  LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                  LEFT JOIN invoice_payment_vouchers ON payment_vouchers.payment_voucher_id = invoice_payment_vouchers.payment_voucher_id
                  WHERE vendor_invoices.invoice_id = invoice_payment_vouchers.invoice_id
                ) > 0 )
            ';
        }

        if(in_array("approved", $status)){
            $sql .= ' AND invoice_id IN(
                  SELECT invoice_id FROM purchase_order_payment_request_invoice_items
                  LEFT JOIN purchase_order_payment_request_approval_invoice_items ON purchase_order_payment_request_invoice_items.id = purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id
                )
            ';
        }

        if(!is_null($from)){
            $sql .= ' AND invoice_date >= '.$from;
        }

        if(!is_null($to)){
            $sql .= ' AND invoice_date <= '.$to;
        }

        $query = $this->db->query($sql);
        $rows = $query->result();
        $invoices = [];
        foreach ($rows as $row){
            $invoice = new Invoice();
            $invoice->load($row->invoice_id);
            $invoices[] = $invoice;
        }
        return $invoices;
    }




    public function balance($currency_id = 1,$to = null){
        return $this->account()->balance($currency_id,$to);
    }

    public function balance_in_base_currency($as_of){
        return $this->account()->balance_in_base_currency($as_of);
    }





    public function statement_transactions($currency_id = 1,$from,$to){
        $sql = 'SELECT "invoice" AS transaction_type,  "debit" AS transaction_action, invoice_date AS transaction_date, reference, amount*exchange_rate AS debit_amount,0 AS credit_amount
                FROM invoices
                LEFT JOIN vendor_invoices ON invoices.id = vendor_invoices.invoice_id
                LEFT JOIN grn_invoices ON invoices.id = grn_invoices.invoice_id
                LEFT JOIN goods_received_notes ON grn_invoices.grn_id = goods_received_notes.grn_id
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                WHERE currency_id = '.$currency_id.' AND vendor_id = '.$this->{$this::DB_TABLE_PK}.' AND invoice_date >= "'.$from.'" AND invoice_date <= "'.$to.'"
                
                UNION ALL
                
                SELECT "payment" AS transaction_type, "credit" AS transaction_action, payment_date AS transaction_date, reference, 0 AS debit_amount,
                  SUM(amount*exchange_rate) AS credit_amount
                  FROM payment_vouchers
                  LEFT JOIN payment_voucher_items ON payment_vouchers.payment_voucher_id = payment_voucher_items.payment_voucher_id
                  WHERE currency_id = '.$currency_id.' AND debit_account_id = '.$this->account_id.' AND payment_date >= "'.$from.'" AND payment_date <= "'.$to.'" GROUP BY payment_voucher_items.payment_voucher_id
                  
                  ORDER BY transaction_date ASC
                  ';

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function vendors_with_orders($from = null, $to = null, $distinctively = false)
    {
        $where = '';
        if(!is_null($from) && $from != ''){
            $where .= ($where != '' ? ' AND ' : ' WHERE ').' issue_date >= "'.$from.'" ';
        }
        if(!is_null($to) && $to != ''){
            $where .= ($where != '' ? ' AND ' : ' WHERE ').' issue_date <= "'.$to.'" ';
        }

        if(!$distinctively) {
            $sql = 'SELECT vendors.vendor_id,vendor_name FROM vendors
                LEFT JOIN purchase_orders ON vendors.vendor_id = purchase_orders.vendor_id
                ' . $where;

            $query = $this->db->query($sql);
            return $query->result();
        } else {
            $sql = 'SELECT DISTINCT vendors.vendor_id FROM vendors
                LEFT JOIN purchase_orders ON vendors.vendor_id = purchase_orders.vendor_id
                ' . $where;

            $query = $this->db->query($sql);
            $results = $query->result();
            $vendors_with_orders = [];
            foreach($results as $row){
                $vendor = new self();
                $vendor->load($row->vendor_id);
                $vendors_with_orders[] = $vendor;
            }
            return $vendors_with_orders;
        }

    }

    public function vendor_project_purchase_orders($project_id,$currency_id,$from = null, $to = null){
        $this->load->model('purchase_order');
        $sql = 'SELECT order_id FROM project_purchase_orders
                LEFT JOIN purchase_orders ON project_purchase_orders.purchase_order_id = purchase_orders.order_id
                WHERE vendor_id ='.$this->{$this::DB_TABLE_PK}.' AND project_id='.$project_id.' AND currency_id =  '.$currency_id;

        if(!is_null($from)){
            $sql .= ' AND issue_date >= "'.$from.'" ';
        }

        if(!is_null($to)){
            $sql .= ' AND issue_date <= "'.$to.'" ';
        }

        $results = $this->db->query($sql)->result();
        $purchase_orders = [];
        foreach ($results as $row){
            $order = new Purchase_order();
            $order->load($row->order_id);
            $purchase_orders[] = $order;
        }
        return $purchase_orders;
    }

    public function balance_per_project($project_id,$currency_id,$from = null, $to = null){
        $orders = $this->vendor_project_purchase_orders($project_id,$currency_id,$from, $to);
        $balance_per_project = 0;
        foreach($orders as $order){
            if($order->status == "CLOSED" || $order->status == "CANCELLED") {
                $balance_per_project += 0;
            } else {
                $balance_per_project += $order->cif() - $order->amount_paid();
            }
        }
        return $balance_per_project;
    }

    public function overall_balance($from = null, $to = null){
        $this->load->model(['currency','purchase_order']);
        $currencies = $this->currency->get();
        $overall_balance = 0;
        foreach ($currencies as $currency) {
            $sql = 'SELECT order_id FROM purchase_orders 
                WHERE currency_id = "' . $currency->{$currency::DB_TABLE_PK} . '" 
                AND vendor_id = "'. $this->{$this::DB_TABLE_PK} .'"';

            if(!is_null($from)){
                $sql .= ' AND issue_date >= "' . $from . '" ';
            }

            if(!is_null($to)){
                $sql .= ' AND issue_date <= "' . $to . '" ';
            }

            $query = $this->db->query($sql);
            $orders = $query->result();

            $balance = 0;
            foreach ($orders as $order) {
                $purchase_order = new Purchase_order();
                $purchase_order->load($order->order_id);
                $balance += $purchase_order->cif() - $purchase_order->unreceived_amount() - $purchase_order->amount_paid();
            }
            $overall_balance += $balance * $currency->rate_to_native();
        }

        return $overall_balance;
    }

    public function vendors_with_unpaid_order_invoices()
    {
        $sql = 'SELECT DISTINCT vendor_invoices.vendor_id FROM vendor_invoices
            LEFT JOIN invoices ON vendor_invoices.invoice_id = invoices.id
            WHERE (
                      amount - (
                                  SELECT COALESCE(SUM(amount),0) FROM payment_voucher_items
                                  LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                                  LEFT JOIN invoice_payment_vouchers ON payment_vouchers.payment_voucher_id = invoice_payment_vouchers.payment_voucher_id
                                  WHERE vendor_invoices.invoice_id = invoice_payment_vouchers.invoice_id
                                )
                ) > 0 
                
                AND vendor_invoices.invoice_id IN(
                  SELECT invoice_id FROM purchase_order_payment_request_approval_invoice_items
                  LEFT JOIN purchase_order_payment_request_invoice_items ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id = purchase_order_payment_request_invoice_items.id
                  LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                  WHERE is_final = 1
                 
                )
                ';

        $query = $this->db->query($sql);
        $results = $query->result();
        $vendors_with_orders = [];
        foreach($results as $row){
            $vendor = new self();
            $vendor->load($row->vendor_id);
            $vendors_with_orders['vendor_'.$vendor->{$vendor::DB_TABLE_PK}] = $vendor->vendor_name;
        }
        return $vendors_with_orders;
    }

    public function supplied_items_in_bulk($from = null, $to = null){
        $sql = 'SELECT * FROM (
                
                  SELECT item_name, symbol AS UOM, (
                    SELECT COALESCE(SUM(quantity),0) FROM purchase_order_material_items
                    LEFT JOIN purchase_orders ON purchase_order_material_items.order_id = purchase_orders.order_id
                    WHERE purchase_order_material_items.material_item_id = material_items.item_id AND status != "CANCELLED" AND purchase_orders.vendor_id = '.$this->{$this::DB_TABLE_PK}.'';
        if(!is_null($from)){
            $sql .= ' AND issue_date >= "'.$from.'" ';
        }

        if(!is_null($to)){
            $sql .= ' AND issue_date <= "'.$to.'" ';
        }

        $sql .= '  ) AS quantity
                  FROM material_items
                  LEFT JOIN measurement_units ON material_items.unit_id = measurement_units.unit_id
                
                  UNION
                
                  SELECT asset_name, "No." AS UOM, (
                    SELECT COALESCE(SUM(quantity),0) FROM purchase_order_asset_items
                    LEFT JOIN purchase_orders ON purchase_order_asset_items.order_id = purchase_orders.order_id
                    WHERE purchase_order_asset_items.asset_item_id = asset_items.id AND status != "CANCELLED" AND purchase_orders.vendor_id = '.$this->{$this::DB_TABLE_PK}.'';
        if(!is_null($from)){
            $sql .= ' AND issue_date >= "'.$from.'" ';
        }

        if(!is_null($to)){
            $sql .= ' AND issue_date <= "'.$to.'" ';
        }

        $sql .= ' ) AS quantity
                  FROM asset_items
                
                ) AS ordered_items WHERE Quantity > 0 ORDER BY Quantity DESC';
        $results = $this->db->query($sql)->result();
        $table_items = [];
        if(!empty($results)){
            foreach($results as $result){
                $table_items[] = [
                    'item_name'=>$result->item_name,
                    'unit'=>$result->UOM,
                    'quantity'=>$result->quantity
                ];
            }
        }
        return $table_items;
    }

}

