<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/11/2018
 * Time: 11:19 AM
 */

class Project_certificate extends MY_Model{
    const DB_TABLE = 'project_certificates';
    const DB_TABLE_PK = 'id';

    public $project_id;
    public $certificate_number;
    public $certificate_date;
    public $certified_amount;
    public $comments;
    public $created_by;


    public function project_certificate_list($project_id,$limit, $start, $keyword, $order){;
        $where =' project_id = '.$project_id;
        if($keyword !=''){
            $where .= ' AND (certificate_date LIKE "%'.$keyword.'%" OR certified_amount LIKE "%'.$keyword.'%" ) ';
        }
        $orderstring = dataTable_order_string(['certificate_number','certificate_date','certified_amount'],$order,'certificate_date');
        $sql = 'SELECT COUNT(id) AS records_total FROM project_certificates WHERE project_id = '.$project_id;
        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;
        $records_filtered = $this->count_rows($where);
        $project_certificates = $this->get($limit,$start,$where,$orderstring);
        $certificates = [];
        $this->load->model('Project');
        $project= new Project();
        $project->load($project_id);
        $data['project'] = $project;
        $total_certified = $total_paid = 0;
        foreach ($project_certificates as $project_certificate){
            $data['certificate'] = $project_certificate;
            $total_paid += $certified_amount = $project_certificate->amount_paid();
            $total_certified += $project_certificate->certified_amount;
            $certificates[] = [
                $project_certificate->certificate_number,
                custom_standard_date($project_certificate->certificate_date),
                '<span class="pull-right">'.number_format($project_certificate->certified_amount,2).'</span>',
                '<span class="pull-right">'.number_format($certified_amount,2).'</span>',
                $this->load->view('projects/certificates/project_certificate_actions',$data,true)
            ];

        }
        $data['data'] = $certificates;
        $data['total_certified_amount'] = $total_certified;
        $data['total_paid_amount'] = $total_paid;
        $data['recordsFiltered'] = $records_filtered;
        $data['recordsTotal'] = $records_total;
        return json_encode($data);

    }

    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

    public function invoiced_amount(){
        $this->load->model('outgoing_invoice_item');
        $invoice_certificates = $this->outgoing_invoice_item->get(0,0,['project_certificate_id'=>$this->{$this::DB_TABLE_PK}]);
        $paid_amount = 0;
        if(!empty($invoice_certificates)){
            foreach($invoice_certificates as $invoice_certificate){
                $paid_amount += $invoice_certificate->quantity * $invoice_certificate->rate;
            }
        }
        return $paid_amount;
    }

    public function amount_paid($from = null,$to = null){
        $where =' WHERE certificate_id = '.$this->{$this::DB_TABLE_PK}.'';
        if(!is_null($from)){
            $where .= ' AND receipt_date >= "'.$from.'" ';
        }

        if(!is_null($to)){
            $where .= ' AND receipt_date <= "'.$to.'" ';
        }
        $sql = 'SELECT (
                          (
                            COALESCE(SUM(amount),0)
                          ) + (
                            COALESCE(SUM(withheld_amount),0)
                          )
                      ) AS amount_paid
                FROM receipt_items AS main_table
                LEFT JOIN withholding_taxes ON main_table.id = withholding_taxes.receipt_item_id
                LEFT JOIN receipts ON main_table.receipt_id = receipts.id
                LEFT JOIN project_certificate_receipts ON receipts.id = project_certificate_receipts.receipt_id
                '.$where;

        $query = $this->db->query($sql);
        return $query->row()->amount_paid;
    }

    public function certificates_dropdown($status = 'not_fully_paid'){

        $sql = 'SELECT certificate_number,project_certificates.id,project_name FROM project_certificates
                LEFT JOIN projects ON project_certificates.project_id = projects.project_id
                 ';

        if($status == 'not_fully_paid'){
            $sql .= 'WHERE ROUND(certified_amount - (
                        (
                          SELECT COALESCE(SUM(amount),0) FROM project_certificate_receipts
                          LEFT JOIN receipts ON project_certificate_receipts.receipt_id = receipts.id
                          LEFT JOIN receipt_items ON receipts.id = receipt_items.receipt_id
                          WHERE project_certificate_receipts.certificate_id = project_certificates.id
                        ) + (
                          SELECT COALESCE(SUM(withheld_amount),0) FROM withholding_taxes
                            LEFT JOIN receipt_items ON withholding_taxes.receipt_item_id = receipt_items.id
                            LEFT JOIN receipts ON receipt_items.receipt_id = receipts.id
                            LEFT JOIN project_certificate_receipts ON receipts.id = project_certificate_receipts.receipt_id
                          WHERE project_certificate_receipts.certificate_id = project_certificates.id
                    
                        )
                      )
                    ) > 0';
        }

        $query = $this->db->query($sql);
        $rows = $query->result();

        $options[''] = '&nbsp;';
        foreach ($rows as $row){
            $options[$row->project_name][$row->id] = $row->certificate_number;
        }
        return $options;
    }

    public function receipts(){
        $this->load->model('project_certificate_receipt');
        $receipt_junctions = $this->project_certificate_receipt->get(0,0,['certificate_id' => $this->{$this::DB_TABLE_PK}]);
        $receipts = [];
        foreach ($receipt_junctions as $junction){
            $receipts[] = $junction->receipt();
        }
        return $receipts;
    }

    public function project_certificate_invoice_amount(){
        $this->load->model(['project_certificate_invoice','outgoing_invoice']);
        $project_certificate_invoices = $this->project_certificate_invoice->get(0,0,['project_certificate_id'=>$this->{$this::DB_TABLE_PK}]);
        if(!empty($project_certificate_invoices)){
            foreach ($project_certificate_invoices as $project_certificate_invoice){
                $outgoing_invoice =  new Outgoing_invoice();
                $outgoing_invoice->load($project_certificate_invoice->outgoing_invoice_id);
                return $outgoing_invoice->outgoing_invoice_amount() + $outgoing_invoice->vat_amount();
            }
        }  else {
            return 0;
        }
    }

    public function outgoing_invoice(){
        $this->load->model(['project_certificate_invoice','outgoing_invoice']);
        $project_certificate_invoices = $this->project_certificate_invoice->get(0,0,['project_certificate_id'=>$this->{$this::DB_TABLE_PK}]);
        if(!empty($project_certificate_invoices)){
            foreach ($project_certificate_invoices as $project_certificate_invoice){
                $outgoing_invoice =  new Outgoing_invoice();
                $outgoing_invoice->load($project_certificate_invoice->outgoing_invoice_id);
                return $outgoing_invoice;
            }
        } else {
            return false;
        }
    }

	public function employee()
	{
		$this->load->model('employee');
		$employee = new Employee();
		$employee->load($this->created_by);
		return $employee;
	}

	public function generate_certificate_particulars($certificate_id,$feedback_type = 'echo')
    {
		$certificate = new self();
		$certificate->load($certificate_id);
		$project = $certificate->project();
		$data['certificate'] = $certificate;
		$data['project'] = $project;
		$ret_val['item_particulars'] = $this->load->view('finance/invoices/project_certificate_particulars',$data,true);
		$ret_val['item_amount'] = $certificate->certified_amount;
        $ret_val['item_object'] = $certificate;
        $ret_val['item_id'] = 'Certificate_'.$certificate_id.'_cert';
        $ret_val['item_options'] = stringfy_dropdown_options(['Certificate_'.$certificate_id.'_cert'=>$certificate->certificate_number]);
        if($feedback_type != 'echo') {
            return json_encode($ret_val);
        } else {
            echo json_encode($ret_val);
        }
    }

}
