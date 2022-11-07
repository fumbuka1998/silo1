<?php
class Employee_contract_close extends MY_Model
{

    const DB_TABLE = 'employee_contract_closes';
    const DB_TABLE_PK = 'id';

    public $employee_contract_id;
    public $close_date;
    public $reason;
    public $attachment;
    public $created_at;
    public $created_by;

    public function employee_contract_closes_list ($limit, $start, $keyword, $order, $employee_id)
    {
        $records_total = $this->count_rows();

// $where = '';

        $where = 'employee_id = "' . $employee_id . '"';
        if ($keyword != '') {

            $where .= 'start_date LIKE "%' . $keyword . '%" ';
        }

        $order_string = dataTable_order_string(['start_date'], $order, 'start_date');

        $employee_contract_closes = $this->get($limit, $start, $where, $order_string);
        $rows = [];
        foreach ($employee_contract_closes as $employee_contract_close) {
            $data['contracts'] = $employee_contract_close;
            $rows[] = [
                $employee_contract_close->employee_contract_id,
                $employee_contract_close->close_date,
                $employee_contract_close->reason,
                $employee_contract_close->attachment,
                $employee_contract_close->created_at,
                $employee_contract_close->created_by()->full_name(),
                $this->load->view('employees/employee_contract_close_actions', $data, true)
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
}