<?php

class Maintenance_service extends MY_Model
{


    const DB_TABLE = 'maintenance_services';
    const DB_TABLE_PK = 'service_id';

    public $service_date;
    public $currency_id;
    public $client_id;
    public $location;
    public $remarks;
    public $created_by;


    public function maintenance_services_no(){
        return 'SVC/'.add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function client(){
        $this->load->model('stakeholder');
        $stakeholder = new Stakeholder();
        $stakeholder->load($this->client_id);
        return $stakeholder;
    }

    public function currency()
    {
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($this->currency_id);
        return $currency;
    }

    public function crested_by(){
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->created_by);
        return $employee;
    }

    public function maintenance_service_items(){
        $this->load->model('maintenance_service_item');
        $service_items = $this->maintenance_service_item->get(0,0, ['service_id' => $this->{$this::DB_TABLE_PK}]);
        return $service_items;
    }

    public function maintenance_cost(){
        $cost = 0;
        $items = $this->maintenance_service_items();
        foreach ($items as $item){
            $cost += ($item->quantity * $item->rate);
        }
        return $cost;
    }

    public function clear_items(){
        $this->db->delete('maintenance_service_items',['service_id'=>$this->{$this::DB_TABLE_PK}]);
    }

    public function services($limit,$start,$keyword,$order)
    {
        $order_string = dataTable_order_string(['service_date','service_id','location'],$order,'service_date');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $where = '';
        if($keyword != ''){
            $where .= ' (
             service_date LIKE "%'.$keyword.'%"
             OR service_id LIKE "%'.$keyword.'%"
             OR service_id IN (
                SELECT service_id FROM maintenance_services
                LEFT JOIN stakeholders ON maintenance_services.client_id = stakeholders.stakeholder_id
                WHERE stakeholder_name LIKE "%'.$keyword.'%"
                )
             OR location LIKE "%'.$keyword.'%" 
             )';
        }

        $records_total = $this->count_rows($where);

        $where = $where != '' ? ' WHERE '.$where.'' : '';

        $sql = 'SELECT SQL_CALC_FOUND_ROWS service_id, service_date,  maintenance_services.currency_id, maintenance_services.client_id, location, remarks, currencies.symbol AS currency_symbol, maintenance_services.remarks  
				FROM maintenance_services
                LEFT JOIN stakeholders ON maintenance_services.client_id = stakeholders.stakeholder_id
                LEFT JOIN currencies ON maintenance_services.currency_id = currencies.currency_id
                '.$where.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;
        $this->load->model(['stakeholder', 'currency', 'measurement_unit', 'maintenance_service_item']);

        $data['stakeholder_options'] = $this->stakeholder->dropdown_options();
        $data['currency_options'] = currency_dropdown_options();
        $data['measurement_unit_options'] = measurement_unit_dropdown_options();

        $rows = [];
        foreach ($results as $row){
            $maintenance_service =  new self();
            $maintenance_service->load($row->service_id);
            $data['maintenance_service'] = $maintenance_service;

            $status = '<button style="width: 55px" class="btn btn-info btn-xs">Pending</button>';

            $sql = 'SELECT * FROM maintenance_invoices WHERE service_id = '.$row->service_id;
            $query = $this->db->query($sql);
            $found_invoice = $query->result();

            if($found_invoice){
                $data['invoiced'] = true;
                $data['invoice_number'] = $found_invoice[0]->outgoing_invoice_id;
                $status = $this->load->view('projects/services/list_actions', $data, true);
                $data['invoiced'] = false;

                  $sql = 'SELECT * FROM maintenance_service_receipts WHERE maintenance_service_id = '.$found_invoice[0]->service_id;
                  $query = $this->db->query($sql);
                  $found_paid_service = $query->result();

                  if($found_paid_service ){
                  	$data['paid'] = true;
                  	$data['paid_invoice'] = $found_paid_service[0]->receipt_id;
                  	$status = $this->load->view('projects/services/list_actions', $data, true);
                  	$data['paid'] = false;
                  }

            }


            $data['paid'] = false;
            $data['invoiced'] = false;
            $rows[] = [
                set_date($row->service_date),
                $maintenance_service->maintenance_services_no(),
                $maintenance_service->remarks,
                $maintenance_service->client()->stakeholder_name,
                $row->location,
                '<span style="text-align: right">' . $row->currency_symbol.' '.number_format($maintenance_service->maintenance_cost(), 2) . '</span>',
                $status,
                $this->load->view('projects/services/list_actions', $data, true)
            ];
        }

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function dropdown_options()
    {
        $sql = 'SELECT maintenance_services.client_id, stakeholder_name FROM maintenance_services
                 LEFT JOIN stakeholders ON maintenance_services.client_id = stakeholders.stakeholder_id';

        $query = $this->db->query($sql);
        $results = $query->result();

        $options[''] = '&nbsp;';
        foreach ($results as $result){
            $options[$result->client_id] = $result->stakeholder_name;
        }
        return $options;
    }

    public function maintenance_service_invoice_amount(){
        $this->load->model(['maintenance_invoice','outgoing_invoice']);
        $maintenance_invoices = $this->maintenance_invoice->get(0,0,['service_id'=>$this->{$this::DB_TABLE_PK}]);
        if(!empty($maintenance_invoices)){
            foreach ($maintenance_invoices as $service_invoice){
                $outgoing_invoice =  new Outgoing_invoice();
                $outgoing_invoice->load($service_invoice->outgoing_invoice_id);
                return $outgoing_invoice->outgoing_invoice_amount() + $outgoing_invoice->vat_amount();
            }
        }  else {
            return 0;
        }
    }

    public function outgoing_invoice(){
        $this->load->model(['maintenance_invoice','outgoing_invoice']);
        $maintenance_invoices = $this->maintenance_invoice->get(0,0,['service_id'=>$this->{$this::DB_TABLE_PK}]);
        if(!empty($maintenance_invoices)){
            foreach ($maintenance_invoices as $service_invoice){
                $outgoing_invoice =  new Outgoing_invoice();
                $outgoing_invoice->load($service_invoice->outgoing_invoice_id);
                return $outgoing_invoice;
            }
        } else {
            return false;
        }
    }

    public function generate_service_particulars($service_id,$feedback_type = 'echo'){
    	$this->load->model('measurement_unit');
		$unit = new Measurement_unit();
		$service = new self();
		$service->load($service_id);
		$data['unit'] = $unit;
		$data['service'] = $service;
		$ret_val['item_particulars'] = $this->load->view('finance/invoices/maintenance_service_particulars',$data,true);
		$ret_val['item_amount'] = $service->maintenance_cost();
		$ret_val['item_object'] = $service;
		$ret_val['item_id'] = 'Service_'.$service_id.'_serv';
		$ret_val['item_options'] = stringfy_dropdown_options(['Service_'.$service_id.'_serv'=>$service->maintenance_services_no()]);
        if($feedback_type != 'echo') {
            return json_encode($ret_val);
        } else {
            echo json_encode($ret_val);
        }
	}


}
