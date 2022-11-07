<?php

class Employee_loan_repay extends MY_Model
{
    const DB_TABLE = 'employee_loan_repay';
    const DB_TABLE_PK = 'id';

    public $employee_id;
    public $loan_id;
    public $employee_loan_id;
    public $paid_amount;
    public $loan_balance_amount;
    public $paid_date;
    public $description;
    public $created_by;

    public function loan_repay_list($limit, $start, $keyword, $order, $employee_id = false)
    {
        $where = $employee_id != false ? 'WHERE employee_id = '.$employee_id : '';
        $sql = 'SELECT * FROM employee_loan_repay '.$where;

        $query = $this->db->query($sql);
        $records_total = $query->num_rows();

        $order_string = dataTable_order_string(['paid_date', 'loan_type'.'paid_amount', 'loan_balance_amount'],$order,'paid_date');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $where = $employee_id != false ? ' WHERE employee_loan_repay.employee_id = '.$employee_id : '';
        if($keyword != ''){
            $where .= ' AND ( loan_type LIKE "%'.$keyword.'%" OR paid_date LIKE "%'.$keyword.'%" ) ';
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS employee_loan_repay.*, loan_type  FROM employee_loan_repay 
                LEFT JOIN loans ON employee_loan_repay.loan_id = loans.id
                '.$where.$order_string;
        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];
        $count = 1;
        foreach ($results as $row){
            ////$data['allowance_data'] = $allowance;
            $rows[] = [
                $count,
                $row->loan_type,
                set_date($row->paid_date),
                number_format($row->paid_amount,2),
                number_format($row->loan_balance_amount,2)
            ];
            $count++;
        }

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function loan_payment_history($loan_id = false, $employee_id = false)
    {
        $where = 'WHERE ( employee_id = '.$employee_id.' AND loan_id = '.$loan_id.' )';
        $sql = 'SELECT * FROM employee_loan_repay '.$where;
        $query = $this->db->query($sql);
        return $query->result();
    }


}