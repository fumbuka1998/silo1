<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/9/2018
 * Time: 11:50 AM
 */

class Stock_sale extends MY_Model{
    const DB_TABLE = 'stock_sales';
    const DB_TABLE_PK = 'id';

    public $sale_date;
    public $stakeholder_id;
    public $location_id;
    public $project_id;
    public $currency_id;
    public $reference;
    public $comments;
    public $created_by;


    public function sale_number(){
        return 'SALE/'.add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function stock_sales_list($sales_for, $id, $limit, $start, $keyword, $order){
        $suppl_order = $sales_for == 'stakeholder' ? 'location_name' : 'stakeholder_name';
        $order_string = dataTable_order_string(['sales_no','sale_date',$suppl_order,'reference'],$order,'sale_date');

        $where_clause = ' ';
        if($keyword != ''){
            $where_clause = ' AND (sale_date LIKE "%'.$keyword.'%" OR stock_sales.id LIKE "%'.$keyword.'%"   OR stock_sales.reference LIKE "%'.$keyword.'%"  OR stakeholders.stakeholder_name LIKE "%'.$keyword.'%"   OR inventory_locations.location_name LIKE "%'.$keyword.'%" )';
        }

        $records_total = $this->count_rows(['stock_sales.'.$sales_for.'_id' => $id]);


        $sql = 'SELECT SQL_CALC_FOUND_ROWS stock_sales.id AS sales_no,reference,stakeholders.stakeholder_name,sale_date, stock_sales.location_id, stock_sales.stakeholder_id,location_name
                FROM stock_sales
                LEFT JOIN stakeholders AS stakeholders ON stock_sales.stakeholder_id = stakeholders.stakeholder_id
                LEFT JOIN inventory_locations ON stock_sales.location_id = inventory_locations.location_id
                WHERE stock_sales.'.$sales_for.'_id = '.$id.$where_clause.' ORDER BY '.$order_string.' LIMIT '.$limit.' OFFSET '.$start;

        $query = $this->db->query($sql);
        $results = $query->result();
        $rows = [];

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $this->load->model(['inventory_location','stakeholder']);
        if($sales_for == 'location') {
            $location = new Inventory_location();
            $location->load($id);
            $data['location'] = $location;
            $data['stakeholder_options'] = $this->stakeholder->dropdown_options();
            $data['sub_location_options'] = $location->sub_location_options();
        } else {
            $stakeholder = new Stakeholder();
            $stakeholder->load($id);
            $data['stakeholder_options'] = [$stakeholder->{$stakeholder::DB_TABLE_PK} => $stakeholder->stakeholder_name];
        }

        $data['currency_options'] = currency_dropdown_options();

        foreach ($results as $row){
            $sale = new self();
            $sale->load($row->sales_no);
            $data['sale'] = $sale;
            $data['paid_amount'] = $sale->paid_amount();
            $data['invoice'] = $sale->invoice();

            if($sales_for == 'stakeholder'){
                $location = new Inventory_location();
                $location->load($row->location_id);
                $data['sub_location_options'] = $location->sub_location_options();
                $data['location'] = $location;
            }



            $rows[] = $sales_for == 'location' ? [
                $sale->sale_number(),
                custom_standard_date($row->sale_date),
                anchor(base_url('stakeholders/stakeholder_profile/'.$row->stakeholder_id),$row->stakeholder_name),
                $row->reference,
                $this->load->view('inventory/sales/location_sales_actions',$data,true)
            ] : [
                $sale->sale_number(),
                custom_standard_date($row->sale_date),
                anchor(base_url('inventory/location_profile/'.$row->location_id),$row->location_name),
                $row->reference,
                $this->load->view('inventory/sales/location_sales_actions',$data,true)
            ];

        }

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);

    }

    public function material_items()
    {
        $this->load->model('stock_sales_material_item');
        return $this->stock_sales_material_item->get(0,0,['stock_sale_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function invoice()
    {
        $this->load->model('stock_sale_invoice');
        $sale_invoices = $this->stock_sale_invoice->get(1,0,['stock_sale_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($sale_invoices) ? array_shift($sale_invoices) : false;
    }

    public function asset_items(){
        $this->load->model('stock_sales_asset_item');
        return $this->stock_sales_asset_item->get(0,0,['stock_sale_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function distinct_asset_items(){
        $sql = 'SELECT DISTINCT asset_item_id FROM assets
                LEFT JOIN asset_sub_location_histories ON assets.id = asset_sub_location_histories.asset_id
                LEFT JOIN stock_sales_asset_items ON asset_sub_location_histories.id = stock_sales_asset_items.asset_sub_location_history_id
                WHERE stock_sale_id = '.$this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        $this->load->model('asset_item');
        $asset_items = [];
        $results = $query->result();
        foreach ($results as $row){
            $asset_item = new Asset_item();
            $asset_item->load($row->asset_item_id);
            $asset_items[] = $asset_item;
        }
        return $asset_items;

    }

    public function stakeholder()
    {
        $this->load->model('stakeholder');
        $stakeholder = new Stakeholder();
        $stakeholder->load($this->stakeholder_id);
        return $stakeholder;
    }

    public function location()
    {
        $this->load->model('inventory_location');
        $location = new Inventory_location();
        $location->load($this->location_id);
        return $location;
    }

    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

    public function employee()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->created_by);
        return $employee;
    }

    public function clear_items(){
        $this->db->where('stock_sale_id', $this->{$this::DB_TABLE_PK});
        $this->db->delete('stock_sales_material_items',['stock_sale_id' => $this->{$this::DB_TABLE_PK}]);
        $this->db->delete('stock_sales_asset_items',['stock_sale_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function stock_sales_dropdown($status = 'not_fully_paid'){
        $sql = 'SELECT * FROM (
                  SELECT stock_sales.id,stakeholder_name,
                  COALESCE(SUM(stock_sales_material_items.quantity*stock_sales_material_items.price),0) AS material_amount,
                  COALESCE(SUM(stock_sales_material_items.quantity*stock_sales_material_items.price),0) AS asset_amount
                  FROM stock_sales
                  LEFT JOIN stock_sales_material_items ON stock_sales.id = stock_sales_material_items.stock_sale_id
                  LEFT JOIN stock_disposal_asset_items ON stock_sales.id
                  LEFT JOIN stakeholders ON stock_sales.stakeholder_id = stakeholders.stakeholder_id
                  GROUP BY stock_sales.id
              ) AS main_table ';

        if($status == 'not_fully_paid'){
            $sql .= '  WHERE asset_amount+material_amount - (
                SELECT COALESCE(SUM(amount),0) FROM receipt_items
                LEFT JOIN receipts ON receipt_items.receipt_id = receipts.id
                LEFT JOIN stock_sale_receipts ON receipts.id = stock_sale_receipts.receipt_id
                WHERE stock_sale_id = main_table.id
              ) > 0 GROUP BY main_table.id
              ';
        }

        $query = $this->db->query($sql);
        $results = $query->result();
        $options[''] = '&nbsp;';
        foreach ($results as $row){
            $stock_sale = new self();
            $stock_sale->load($row->id);
            $options[$row->stakeholder_name][$row->id] = $stock_sale->sale_number();
        }
        return $options;
    }

    public function currency()
    {
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($this->currency_id);
        return $currency;
    }

    public function sale_amount(){
        $sql = 'SELECT (
                  (
                        SELECT COALESCE(SUM(quantity*price),0) AS material_amount
                        FROM stock_sales_material_items WHERE stock_sale_id = '.$this->{$this::DB_TABLE_PK}.'
                    ) + (
                        SELECT COALESCE(SUM(price),0) AS asset_amount
                        FROM stock_sales_asset_items WHERE stock_sale_id = '.$this->{$this::DB_TABLE_PK}.'
                    )
                ) AS amount
                ';
        $query = $this->db->query($sql);
        return $query->row()->amount;
    }

    public function paid_amount(){
        $sql = 'SELECT COALESCE(SUM(amount),0) AS paid_amount FROM receipt_items
              LEFT JOIN receipts ON receipt_items.receipt_id = receipts.id
              LEFT JOIN stock_sale_receipts ON receipts.id = stock_sale_receipts.receipt_id
              WHERE stock_sale_id = '.$this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        return $query->row()->paid_amount;
    }

    public function unpaid_balance(){
        return $this->sale_amount() - $this->paid_amount();
    }

    public function stock_sale_invoice_amount(){
        $this->load->model(['stock_sale_invoice','outgoing_invoice']);
        $stock_sale_invoices = $this->stock_sale_invoice->get(0,0,['stock_sale_id'=>$this->{$this::DB_TABLE_PK}]);
        if(!empty($stock_sale_invoices)){
            foreach ($stock_sale_invoices as $stock_sale_invoice){
                $outgoing_invoice =  new Outgoing_invoice();
                $outgoing_invoice->load($stock_sale_invoice->outgoing_invoice_id);
                return $outgoing_invoice->outgoing_invoice_amount() + $outgoing_invoice->vat_amount();
            }
        }  else {
            return 0;
        }
    }

    public function outgoing_invoice(){
        $this->load->model(['stock_sale_invoice','outgoing_invoice']);
        $stock_sale_invoices = $this->stock_sale_invoice->get(0,0,['stock_sale_id'=>$this->{$this::DB_TABLE_PK}]);
        if(!empty($stock_sale_invoices)){
            foreach ($stock_sale_invoices as $stock_sale_invoice){
                $outgoing_invoice =  new Outgoing_invoice();
                $outgoing_invoice->load($stock_sale_invoice->outgoing_invoice_id);
                return $outgoing_invoice;
            }
        } else {
            return false;
        }
    }

    public function generate_sale_particulars($sale_id,$feedback_type = 'echo'){
		$sale = new self();
		$sale->load($sale_id);
		$project = $sale->project();
		$currency = $sale->currency();
		$data['sale'] = $sale;
		$data['currency'] = $currency;
		$data['project'] = $project;
		$ret_val['item_particulars'] = $this->load->view('finance/invoices/stock_sale_particulars',$data,true);
		$ret_val['item_amount'] = $sale->sale_amount();
        $ret_val['item_object'] = $sale;
        $ret_val['item_id'] = 'Sale_'.$sale_id.'_asset';
        $ret_val['item_options'] = stringfy_dropdown_options(['Sale_'.$sale_id.'_asset'=>$sale->sale_number()]);
		if($feedback_type != 'echo') {
            return json_encode($ret_val);
        } else {
		    echo json_encode($ret_val);
        }
	}

}
