<?php

/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/18/2018
 * Time: 9:21 AM
 */

class Sub_contract_certificate_payment_voucher extends MY_Model
{
    const DB_TABLE = 'sub_contract_certificate_payment_vouchers';
    const DB_TABLE_PK = 'id';

    public $sub_contract_certificate_id;
    public $payment_voucher_id;

    public function sub_contract_certificate()
    {
        $this->load->model('sub_contract_certificate');
        $sub_contract_certificate = new Sub_contract_certificate();
        $sub_contract_certificate->load($this->sub_contract_certificate_id);
        return $sub_contract_certificate;
    }

    public function actual_cost($cost_center_id, $level = null, $from = null, $to = null)
    {
        $sql = 'SELECT (
                            (
                              SELECT COALESCE(SUM(payment_voucher_items.amount),0) FROM payment_voucher_items
                                INNER JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                                INNER JOIN sub_contract_certificate_payment_vouchers ON payment_vouchers.payment_voucher_id = sub_contract_certificate_payment_vouchers.payment_voucher_id
                                INNER JOIN sub_contract_certificates ON sub_contract_certificate_payment_vouchers.sub_contract_certificate_id = sub_contract_certificates.id';
        if ($level == 'activity' || $level == 'task') {
            $sql .= ' INNER JOIN sub_contract_certificate_tasks ON sub_contract_certificates.id = sub_contract_certificate_tasks.sub_contract_certificate_id';
            if ($level == 'task') {
                $sql .= ' AND sub_contract_certificate_tasks.task_id = "' . $cost_center_id . '"';
            }
            if ($level == 'activity') {
                $sql .= ' AND sub_contract_certificate_tasks.task_id IN (SELECT task_id FROM tasks WHERE activity_id = "' . $cost_center_id . '")';
            }
        }
        $sql .= ' INNER JOIN sub_contracts ON sub_contract_certificates.sub_contract_id = sub_contracts.id
                                WHERE ';
        if ($level == 'project') {
            $sql  .= ' sub_contracts.project_id = "' . $cost_center_id . '" AND sub_contracts.id IN (
                                           SELECT sub_contract_id FROM sub_contracts_items WHERE task_id IS NULL
                                    )';
        } else {
            $sql .= ' sub_contracts.project_id = "' . $cost_center_id . '" ';
        }

        if ($from != null) {
            $sql .= ' AND certificate_date >= "' . $from . '" ';
        }

        if ($to != null) {
            $sql .= ' AND certificate_date <= "' . $to . '" ';
        }
        $sql .=
            ') + (
                                
                                SELECT COALESCE(SUM(withheld_amount),0) FROM withholding_taxes
                                INNER JOIN payment_voucher_items ON withholding_taxes.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                                INNER JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                                INNER JOIN sub_contract_certificate_payment_vouchers ON payment_vouchers.payment_voucher_id = sub_contract_certificate_payment_vouchers.payment_voucher_id
                                INNER JOIN sub_contract_certificates ON sub_contract_certificate_payment_vouchers.sub_contract_certificate_id = sub_contract_certificates.id';
        if ($level == 'activity' || $level == 'task') {
            $sql .= ' INNER JOIN sub_contract_certificate_tasks ON sub_contract_certificates.id = sub_contract_certificate_tasks.sub_contract_certificate_id';
            if ($level == 'task') {
                $sql .= ' AND sub_contract_certificate_tasks.task_id = "' . $cost_center_id . '"';
            }
            if ($level == 'activity') {
                $sql .= ' AND sub_contract_certificate_tasks.task_id IN (SELECT task_id FROM tasks WHERE activity_id = "' . $cost_center_id . '")';
            }
        }
        $sql .= ' INNER JOIN sub_contracts ON sub_contract_certificates.sub_contract_id = sub_contracts.id
                                WHERE ';
        if ($level == 'project') {
            $sql  .= ' sub_contracts.project_id = "' . $cost_center_id . '" AND sub_contracts.id IN (
                                                                   SELECT sub_contract_id FROM sub_contracts_items WHERE task_id IS NULL
                                                            )';
        } else {
            $sql .= ' sub_contracts.project_id = "' . $cost_center_id . '" ';
        }

        if ($from != null) {
            $sql .= ' AND certificate_date >= "' . $from . '" ';
        }

        if ($to != null) {
            $sql .= ' AND certificate_date <= "' . $to . '" ';
        }

        $sql .= '           )
                      ) AS total_sub_contract_cost';

        $query = $this->db->query($sql);
        return doubleval($query->row()->total_sub_contract_cost);
    }
}
