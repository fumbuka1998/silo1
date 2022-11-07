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

        $where = '';

        $order_string = dataTable_order_string(['receipt_no','receipt_date','account_name','reference','amount'],$order,'receipt_date');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;
        $sql = 'SELECT COUNT(id) AS records_total FROM receipts ';
        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;

        if($keyword !=''){
            $where = ' WHERE (receipt_date LIKE "%'.$keyword.'%" OR reference LIKE "%'.$keyword.'%" OR receipts.id LIKE "%'.$keyword.'%" OR amount LIKE "%'.$keyword.'%") ';
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS receipts.id AS receipt_no,receipt_date,reference,accounts.account_name,COALESCE(SUM(amount),0) AS amount FROM receipts
                LEFT JOIN receipt_items ON receipts.id = receipt_items.receipt_id
                LEFT JOIN accounts ON receipts.debit_account_id = accounts.account_id '.$where.'  GROUP BY receipt_id '.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

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
                $row->account_name,
                $row->reference,
                '<span class="pull-right">'.number_format($receipt->amount(),2).'</span>',
                $this->load->view('finance/receipts/receipt_actions',$data,true)
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

    public function credit_account()
    {
        $this->load->model('account');
        $account = new Account();
        $account->load($this->credit_account_id);
        return $account;
    }

    public function debit_account()
    {
        $this->load->model('account');
        $debit_account = new Account();
        $debit_account->load($this->debit_account_id);
        return $debit_account;
    }

    public function supplementary_accounts()
    {
        return 'N/A';
    }


}