<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/12/2018
 * Time: 3:29 PM
 */
class Receipt extends MY_Model
{

    const DB_TABLE = 'receipts';
    const DB_TABLE_PK = 'id';
    const receipt_types = ['stock_sale_receipt', 'project_certificate_receipt','maintenance_service_receipt'];

    public $debit_account_id;
    public $credit_account_id;
    public $receipt_date;
    public $invoice_id;
    public $reference;
    public $withholding_tax;
    public $currency_id;
    public $exchange_rate;
    public $remarks;
    public $created_by;


    public function receipt_number()
    {
        return 'RC'.add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

	public function detailed_reference(){
		$reference = $this->reference != '' ? $this->reference : null;
		return !is_null($reference) ? $this->receipt_number().' - '.$reference : $this->receipt_number();
	}

    public function outgoing_invoice(){
        $this->load->model('outgoing_invoice');
        $outgoing_invoice = new Outgoing_invoice();
        $outgoing_invoice->load($this->invoice_id);
        return $outgoing_invoice;
    }

    public function amount()
    {
        $sql = 'SELECT COALESCE(SUM(amount),0) AS receipt_amount FROM receipt_items WHERE receipt_id = '.$this->{$this::DB_TABLE_PK};
        $query = $this->db->query($sql);
        return floatval($query->row()->receipt_amount);
    }

    public function withheld_amount(){
        $sql = 'SELECT COALESCE(SUM(withheld_amount),0) AS withheld_amount FROM withholding_taxes
                LEFT JOIN receipt_items ON withholding_taxes.receipt_item_id = receipt_items.id
                WHERE receipt_id = '.$this->{$this::DB_TABLE_PK}.'
                ';
        return $this->db->query($sql)->row()->withheld_amount;
    }

    public function items()
    {
        $this->load->model('receipt_item');
        return $this->receipt_item->get(0,0,['receipt_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function item()
    {
        $this->load->model('receipt_item');
        $receipt_items = $this->receipt_item->get(0,0,['receipt_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($receipt_items) ? array_shift($receipt_items) : false;
    }

    public function delete_items(){
        $this->db->where('receipt_id', $this->{$this::DB_TABLE_PK});
        $this->db->delete(['receipt_items','project_certificate_receipts','stock_sale_receipts','maintenance_service_receipts']);
    }

    public function clear_items(){
        $this->db->delete('receipt_items',['receipt_id' => $this->{$this::DB_TABLE_PK}]);
        $this->db->delete('project_certificate_receipts',['receipt_id' => $this->{$this::DB_TABLE_PK}]);
        $this->db->delete('stock_sale_receipts',['receipt_id' => $this->{$this::DB_TABLE_PK}]);
        $this->db->delete('maintenance_service_receipts',['receipt_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function receipt_type()
    {
        foreach ($this::receipt_types as $receipt_type){
            $this->load->model($receipt_type);
            $junctions = $this->$receipt_type->get(1,0,['receipt_id' => $this->{$this::DB_TABLE_PK}]);
            if(!empty($junctions)){
                break;
            }
        }
        return $receipt_type;
    }

    public function receipts_list($limit, $start, $keyword, $order){

        $order_string = dataTable_order_string(['receipts.id','receipt_date','client_name','account_name','reference','amount'],$order,'receipt_date');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $sql = 'SELECT COUNT(id) AS records_total FROM receipts ';
        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;

        $where = '';
        if($keyword !=''){
            $where = ' WHERE (receipt_date LIKE "%'.$keyword.'%" OR receipts.reference LIKE "%'.$keyword.'%" OR receipts.id LIKE "%'.$keyword.'%" OR amount LIKE "%'.$keyword.'%" OR client_name LIKE "%'.$keyword.'%") ';
        }

        $sql = 'SELECT receipts.id AS receipt_no,receipt_date,client_name,receipts.reference,accounts.account_name,COALESCE(SUM(amount),0) AS amount FROM receipts
                LEFT JOIN receipt_items ON receipts.id = receipt_items.receipt_id
                LEFT JOIN outgoing_invoices ON receipts.invoice_id = outgoing_invoices.id
                LEFT JOIN clients ON outgoing_invoices.invoice_to = clients.client_id
                LEFT JOIN accounts ON receipts.debit_account_id = accounts.account_id 
                '.$where.'  GROUP BY receipt_id '.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT receipts.id FROM receipts
                LEFT JOIN receipt_items ON receipts.id = receipt_items.receipt_id
                LEFT JOIN outgoing_invoices ON receipts.invoice_id = outgoing_invoices.id
                LEFT JOIN clients ON outgoing_invoices.invoice_to = clients.client_id
                LEFT JOIN accounts ON receipts.debit_account_id = accounts.account_id 
                '.$where.'  GROUP BY receipt_id ';
        $query = $this->db->query($sql);
        $records_filtered = $query->num_rows();

        $rows = [];
        $data['debit_account_options'] = account_dropdown_options(['BANK','CASH IN HAND']);
        $data['currency_options'] = currency_dropdown_options();
        foreach ($results as $row){
            $receipt = new self();
            $receipt->load($row->receipt_no);
            $data['receipt'] = $receipt;
            $data['receipt_type'] = $receipt->receipt_type();
            $rows[] = [
                add_leading_zeros($row->receipt_no),
                custom_standard_date($row->receipt_date),
                $row->client_name,
                $row->account_name,
                $row->reference,
                '<span class="pull-right">'.$receipt->currency()->symbol.' '.number_format($receipt->amount(),2).'</span>',
                $this->load->view('finance/transactions/receipts/receipt_actions',$data,true)
            ];
        }

        $data['data'] = $rows;
        $data['recordsFiltered'] = $records_filtered;
        $data['recordsTotal'] = $records_total;
        return json_encode($data);

    }

    public function certificate_junction()
    {
        $this->load->model('project_certificate_receipt');
        $junctions = $this->project_certificate_receipt->get(1,0,['receipt_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($junctions) ? array_shift($junctions) : false;
    }

    public function certificate()
    {
        $certificate_junction = $this->certificate_junction();
        return $certificate_junction ? $certificate_junction->certificate() : false;
    }

    public function stock_sale_junction()
    {
        $this->load->model('stock_sale_receipt');
        $junctions = $this->stock_sale_receipt->get(1,0,['receipt_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($junctions) ? array_shift($junctions) : false;
    }

    public function stock_sale()
    {
        $junction = $this->stock_sale_junction();
        return $junction ? $junction->stock_sale() : false;
    }

    public function maintenance_service_junction(){
        $this->load->model('maintenance_service_receipt');
        $junctions = $this->maintenance_service_receipt->get(1,0,['receipt_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($junctions) ? array_shift($junctions) : false;
    }

    public function maintenance_service(){
        $junction = $this->maintenance_service_junction();
        return $junction ? $junction->maintenance_service() : false;
    }

    public function employee()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->created_by);
        return $employee;
    }

    public function currency()
    {
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($this->currency_id);
        return $currency;
    }

    public function credit_account($item)
    {
		$this->load->model(['stakeholder']);
		$account_id = $this->credit_account_id != '' ? $this->credit_account_id : null;
		if(!is_null($account_id)){
			$stakeholder = new Stakeholder();
			$stakeholder->load($this->credit_account_id);
			if($item == 'name') {
				$account_name_or_id = $stakeholder->stakeholder_name;
			} else {
				$account_name_or_id = 'stakeholder_'.$stakeholder->{$stakeholder::DB_TABLE_PK};
			}
		}
		return $account_name_or_id;
    }

    public function debit_account($item)
    {
		$this->load->model(['account']);
		$account_id = $this->debit_account_id != '' ? $this->debit_account_id : null;
		if(!is_null($account_id)){
			$account = new Account();
			$account->load($this->debit_account_id);
			if($item == 'name') {
				$account_name_or_id = $account->account_name;
			} else {
				$account_name_or_id = 'real_'.$account->{$account::DB_TABLE_PK};
			}
		}
		return $account_name_or_id;
    }

    public function supplementary_accounts()
    {
        return 'N/A';
    }

    public function client(){
        $this->load->model('stakeholder');
        $client_id = $this->credit_account('id');
        $client_id = explode('_',$client_id)[1];
        $where = array('stakeholder_id'=>$client_id);
        $clients = $this->stakeholder->get(0,0,$where);
        return array_shift($clients);

    }


}
