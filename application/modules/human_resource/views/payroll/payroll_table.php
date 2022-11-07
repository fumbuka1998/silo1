<?php

/**
 * Created by PhpStorm.
 * User: zeus
 * Date: 23/03/2019
 * Time: 15:05
 */

if ($save) {
    ///inspect_object($employee_basic_info); exit;
?>
    <div class="container-fluid">
        <table>
            <thead>
                <tr>
                    <td style="font-weight: bold; font-size: large">
                        <?= strtoupper($departments->department_name) ?> DEPARTMENT PAYROLL FOR
                        <?= strtoupper(DateTime::createFromFormat('!m', date('m', strtotime($payroll_date)))->format('F')) . ' ' . date('Y', strtotime($payroll_date)) ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold; font-size: large">
                        Currency: Tanzanian Shilings (TSh)
                    </td>
                </tr>
            </thead>
        </table>
    </div>

    <div class="col-xs-12 table-responsive">
        <table id="payroll_table" <?php if ($print) { ?> width="100%" border="1" cellspacing="0" style="font-size: 11px" <?php } else { ?> class="table table-bordered table-hover table-striped " <?php } ?>>
            <thead>
                <tr style="background: #b9c6e2">
                    <td style="text-align: center;" colspan="4"><strong>RECRUITMENT</strong></td>
                    <td style="text-align: center;" colspan="<?= 13 + count($all_allowances_found) ?>"><strong>EMPLOYEE</strong></td>
                    <td style="text-align: center;" colspan="<?= count($all_employer_deductions) + 2 ?>"><strong>EMPLOYER</strong></td>
                </tr>
                <tr style="background: #b9c6e2">
                    <td>S/No.</td>
                    <td>Position</td>
                    <td>Name</td>
                    <td>Location</td>
                    <td style="font-weight: bold">B.Salary</td>
                    <?php
                    foreach ($all_allowances_found as $allowance) {
                        $data[$allowance->allowance_name] = 0; ?>
                        <td><?= substr($allowance->allowance_name, 0, 4) . '. Allow' ?></td>
                    <?php } ?>
                    <td style="font-weight: bold">Gross Salary</td>
                    <td>Deduct NSSF</td>
                    <td>Taxable Amount</td>
                    <td>Deduct P.A.Y.E</td>
                    <td>HESLB</td>
                    <td>Loan Repay</td>
                    <td>Balance</td>
                    <td>COY LOAN</td>
                    <td>Loan Repay</td>
                    <td>Balance</td>
                    <td>Advance</td>
                    <td style="font-weight: bold">NETPAY</td>
                    <?php
                    foreach ($all_employer_deductions as $deduction) {
                        $data[$deduction->deduction_name] = 0; ?>
                        <td><?= $deduction->deduction_name ?></td>
                    <?php } ?>
                    <td style="font-weight: bold;">TOTAl EMPLOYER'S CONTRIBUTION</td>
                    <td style="font-weight: bold;">GROSS BY EMPLOYER</td>
                </tr>
            </thead>
            <tbody>

                <?php
                $count = 1;
                $total_basic_s = 0;
                $total_gross = 0;
                $total_de_nssf = 0;
                $total_tax_amount = 0;
                $total_d_paye = 0;
                $total_heslb = 0;
                $total_beslb_repay = 0;
                $total_heslb_balance = 0;
                $total_company = 0;
                $total_company_repay = 0;
                $total_company_balance = 0;
                $total_advance = 0;
                $total_net = 0;
                $total_emplyr_contribution = 0;
                $total_emplyr_paid = 0;

                foreach ($employee_basic_info as $employee_info) {
                    $employee->load($employee_info->employee_id);
                    $heslb_allignment = $employee_info->heslb_loan > 0 ? 'right' : 'center';
                    $heslb_repay_allignment = $employee_info->heslb_loan_repay > 0 ? 'right' : 'center';
                    $heslb_balance_allignment = $employee_info->heslb_loan_balance > 0 ? 'right' : 'center';
                    $company_allignment = $employee_info->company_loan > 0 ? 'right' : 'center';
                    $company_repay_allignment = $employee_info->company_loan_repay > 0 ? 'right' : 'center';
                    $company_balance_allignment = $employee_info->company_loan_balance > 0 ? 'right' : 'center';
                    $advance_payments_align = $employee_info->advance_payment > 0 ? 'right' : 'center';
                    $total_basic_s = $total_basic_s + $employee_info->basic_salary;
                    $total_gross = $total_gross + $employee_info->gross_salary;
                    $total_de_nssf = $total_de_nssf + $employee_info->deducted_nssf;
                    $total_tax_amount = $total_tax_amount + $employee_info->taxable_amount;
                    $total_d_paye = $total_d_paye + $employee_info->paye;
                    $total_heslb = $total_heslb + $employee_info->heslb_loan;
                    $total_beslb_repay = $total_beslb_repay + $employee_info->heslb_loan_repay;
                    $total_heslb_balance = $total_heslb_balance + $employee_info->heslb_loan_balance;
                    $total_company = $total_company + $employee_info->company_loan;
                    $total_company_repay = $total_company_repay + $employee_info->company_loan_repay;
                    $total_company_balance = $total_company_balance + $employee_info->company_loan_balance;
                    $total_advance = $total_advance + $employee_info->advance_payment;
                    $total_net = $total_net + $employee_info->net_pay; ?>
                    <tr>
                        <td style="min-width: 10px"><?= $count ?></td>
                        <td style="min-width: 100px"><?= $employee_info->title ?></td>
                        <td style="min-width: 150px"><?= $employee->full_name() ?></td>
                        <td><?= $employee_info->location = 'Head Quarters' ? 'HQ' : $employee_info->location ?></td>
                        <td style="font-weight: bold; text-align: right"><?= number_format($employee_info->basic_salary, 2) ?></td>
                        <?php
                        foreach ($all_allowances_found as $allowance) {
                            foreach ($employee_allowances_found as $emp_allow) {
                                if ($employee_info->employee_id == $emp_allow->employee_id && $emp_allow->allowance_name == $allowance->allowance_name) {
                                    $allignment = $emp_allow->allowance_amount > 0 ? 'right' : 'center';
                                    $data[$allowance->allowance_name] = $data[$allowance->allowance_name] + $emp_allow->allowance_amount; ?>
                                    <td style="text-align: <?= $allignment ?>"><?= $emp_allow->allowance_amount > 0 ? number_format($emp_allow->allowance_amount, 2) : '-' ?></td>
                        <?php
                                }
                            }
                        }  ?>
                        <td style="font-weight: bold; text-align: right"><?= number_format($employee_info->gross_salary, 2) ?></td>
                        <td style="text-align: right"><?= number_format($employee_info->deducted_nssf, 2) ?></td>
                        <td style="text-align: right"><?= number_format($employee_info->taxable_amount, 2) ?></td>
                        <td style="text-align: right"><?= number_format($employee_info->paye, 2) ?></td>
                        <td style="text-align: <?= $heslb_allignment ?>"><?= $employee_info->heslb_loan > 0 ? number_format($employee_info->heslb_loan, 2) : '-';  ?></td>
                        <td style="text-align: <?= $heslb_repay_allignment ?>"><?= $employee_info->heslb_loan_repay > 0 ? number_format($employee_info->heslb_loan_repay, 2) : '-'  ?></td>
                        <td style="text-align: <?= $heslb_balance_allignment ?>"><?= $employee_info->heslb_loan_balance > 0 ? number_format($employee_info->heslb_loan_balance, 2) : '-' ?></td>
                        <td style="text-align: <?= $company_allignment ?>"><?= $employee_info->company_loan > 0 ? number_format($employee_info->company_loan, 2) : '-' ?></td>
                        <td style="text-align: <?= $company_repay_allignment ?>"><?= $employee_info->company_loan_repay > 0 ? number_format($employee_info->company_loan_repay, 2) : '-'  ?></td>
                        <td style="text-align: <?= $company_balance_allignment ?>"><?= $employee_info->company_loan_balance > 0 ? number_format($employee_info->company_loan_balance, 2) : '-' ?></td>
                        <td style="text-align: <?= $advance_payments_align ?>"><?= $employee_info->advance_payment > 0 ? number_format($employee_info->advance_payment, 2) : '-' ?></td>
                        <td style="font-weight: bold; text-align: right"><?= number_format($employee_info->net_pay, 2) ?></td>
                        <?php
                        $total_contribution = 0;
                        foreach ($all_employer_deductions as $deduction) {
                            $deduction_amount = 0;
                            foreach ($employee_deductions_found as $emp_deduct) {
                                if ($employee_info->employee_id == $emp_deduct->employee_id && $emp_deduct->deduction_name == $deduction->deduction_name) {
                                    $deduction_amount = $emp_deduct->deduction_amount;
                                    $data[$deduction->deduction_name] = $data[$deduction->deduction_name] + $emp_deduct->deduction_amount;
                                }
                            }
                            $total_contribution += $deduction_amount ?>
                            <td style="text-align: <?= $deduction_amount > 0 ? 'right' : 'center' ?>"><?= $deduction_amount > 0 ? number_format($deduction_amount, 2) : '-' ?></td>
                        <?php }
                        $total_emplyr_contribution += $total_contribution;
                        $total_emplyr_paid += $emplyr_paid = $employee_info->gross_salary + $total_contribution;
                        ?>
                        <td style="font-weight: bold; text-align: right"><?= number_format($total_contribution, 2) ?></td>
                        <td style="font-weight: bold; text-align: right"><?= number_format($emplyr_paid, 2) ?></td>
                    </tr>
                <?php
                    $count++;
                }
                ?>
            </tbody>
            <tfoot>
                <tr style="background: #b9c6e2">
                    <td colspan="2" style="font-weight: bold">GRAND TOTAL</td>
                    <td colspan="2" style="font-weight: bold">As proposed</td>
                    <td style="font-weight: bold; text-align: right"><?= number_format($total_basic_s, 2) ?></td>
                    <?php
                    foreach ($all_allowances_found as $allowance) {
                        $data[$allowance->allowance_name . 'align'] = $data[$allowance->allowance_name] > 0 ? 'right' : 'center'; ?>
                        <td style="font-weight: bold; text-align: <?= $data[$allowance->allowance_name . 'align'] ?>"><?= $data[$allowance->allowance_name] > 0 ? number_format($data[$allowance->allowance_name], 2) : '-'  ?></td>
                    <?php
                    }
                    $t_heslb_allignment = $total_heslb > 0 ? 'right' : 'center';
                    $t_heslb_repay_allignment = $total_beslb_repay > 0 ? 'right' : 'center';
                    $t_heslb_balance_allignment = $total_heslb_balance > 0 ? 'right' : 'center';
                    $t_company_allignment = $total_company > 0 ? 'right' : 'center';
                    $t_company_repay_allignment = $total_company_repay > 0 ? 'right' : 'center';
                    $t_company_balance_allignment = $total_company_balance > 0 ? 'right' : 'center';
                    $t_advance_payments_align = $total_advance > 0 ? 'right' : 'center';
                    ?>
                    <td style="font-weight: bold; text-align: right"><?= number_format($total_gross, 2) ?></td>
                    <td style="font-weight: bold; text-align: right"><?= number_format($total_de_nssf, 2) ?></td>
                    <td style="font-weight: bold; text-align: right"><?= number_format($total_tax_amount, 2) ?></td>
                    <td style="font-weight: bold; text-align: right"><?= number_format($total_d_paye, 2) ?></td>
                    <td style="font-weight: bold; text-align: <?= $t_heslb_allignment ?>"><?= $total_heslb > 0 ? number_format($total_heslb, 2) : '-' ?></td>
                    <td style="font-weight: bold; text-align: <?= $t_heslb_repay_allignment ?>"><?= $total_beslb_repay > 0 ? number_format($total_beslb_repay, 2) : '-'  ?></td>
                    <td style="font-weight: bold; text-align: <?= $t_heslb_balance_allignment ?>"><?= $total_heslb_balance > 0 ? number_format($total_heslb_balance, 2) : '-'  ?></td>
                    <td style="font-weight: bold; text-align: <?= $t_company_allignment ?>"><?= $total_company > 0 ? number_format($total_company, 2) : '-' ?></td>
                    <td style="font-weight: bold; text-align: <?= $t_company_repay_allignment ?>"><?= $total_company_repay > 0 ? number_format($total_company_repay, 2) : '-'  ?></td>
                    <td style="font-weight: bold; text-align: <?= $t_company_balance_allignment ?>"><?= $total_company_balance > 0 ? number_format($total_company_balance, 2) : '-'  ?></td>
                    <td style="font-weight: bold; text-align: <?= $t_advance_payments_align ?>"><?= $total_advance > 0 ? number_format($total_advance, 2) : '-'  ?></td>
                    <td style="font-weight: bold; text-align: right"><?= number_format($total_net, 2) ?></td>
                    <?php
                    foreach ($all_employer_deductions as $deduction) { ?>
                        <td style="font-weight: bold; text-align: right"><?= number_format($data[$deduction->deduction_name], 2) ?></td>
                    <?php } ?>
                    <td style="font-weight: bold; text-align: right"><?= number_format($total_emplyr_contribution, 2) ?></td>
                    <td style="font-weight: bold; text-align: right"><?= number_format($total_emplyr_paid, 2) ?></td>
                <tr>
            </tfoot>
        </table>
        <br />
        <table width="35%" cellspacing="0.5" <?php if ($print) { ?> style="font-size: 11px" <?php } ?>>
            <tr>
                <td style="text-align: left; font-size: small">
                    <span class="pull-left"><strong>GROSS: </strong></span>
                </td>
                <td style="text-align: right; font-size: small">
                    <span class="pull-right"><strong><?= number_format($total_emplyr_paid, 2) ?></strong></span>
                </td>
            </tr>
            <tr>
                <td style="text-align: left; font-size: small">
                    <span class="pull-left"><strong>NET EMEPLOYEE PAYMENT: </strong></span>
                </td>
                <td style="text-align: right; font-size: small">
                    <span class="pull-right"><strong><?= number_format($total_net, 2) ?></strong></span>
                </td>
            </tr>
            <?php
            $total_headcount = $total_emplyr_paid + $total_net + $total_d_paye;
            foreach ($all_employer_deductions as $deduction) { ?>
                <tr>
                    <td style="text-align: left; font-size: small">
                        <span class="pull-left"><strong><?= strtoupper($deduction->deduction_name) ?>: </strong></span>
                    </td>
                    <td style="text-align: right; font-size: small">
                        <span class="pull-right"><strong><?= number_format($data[$deduction->deduction_name], 2) ?></strong></span>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td style="text-align: left; font-size: small">
                    <span class="pull-left"><strong>PAYE: </strong></span>
                </td>
                <td style="text-align: right; font-size: small">
                    <span class="pull-right"><strong><?= number_format($total_d_paye, 2) ?></strong></span>
                </td>
            </tr>
            <tr>
                <td style="text-align: left; font-size: small">
                    <span class="pull-left"><strong>HEADCOUNT: </strong></span>
                </td>
                <td style="text-align: right; font-size: small">
                    <span class="pull-right"><strong><?= number_format($total_headcount, 2) ?></strong></span>
                </td>
            </tr>
        </table>

    </div>

    <?php
    if (!$print && $can_approve) {
        //        inspect_object($payroll->current_approval_level());
        //
        //        $this->load->model('approval_chain_level');
        //        $level = new Approval_chain_level();
        //        $level->load($payroll->current_approval_level()->id);
        //
        //        inspect_object($payroll->can_approve_positions());
    ?>
        <div class="form-group col-md-3 pull-right">
            <?php
            if (!$next_level) {
            ?>
                <button current_level="<?= $current_level ?>" payroll_id="<?= $payroll[0]->id ?>" id="<?= 'payroll_final_approval' . $payroll[0]->department_id . $payroll[0]->id ?>" class="btn btn-info btn-sm pull-right" type="button">
                    Approve
                </button>
            <?php
            } else {
            ?>
                <button current_level="<?= $current_level ?>" payroll_id="<?= $payroll[0]->id ?>" id="<?= 'submit_payroll_for_final_approval' . $payroll[0]->department_id . $payroll[0]->id ?>" class="btn btn-info btn-sm pull-right" type="button">
                    Subbmit For Approval
                </button>
            <?php
            }
            ?>

            <button current_level="<?= $current_level ?>" payroll_id="<?= $payroll[0]->id ?>" id="<?= 'reject_payroll' . $payroll[0]->department_id . $payroll[0]->id ?>" class="btn btn-danger btn-sm pull-right margin-r-5" type="button">
                Reject
            </button>
        </div>

    <?php

    }

    #-------------------------------------------------------------#

} else { ?>
    <div class="container-fluid">
        <table>
            <thead>
                <tr>
                    <td style="font-weight: bold; font-size: large">
                        <?= strtoupper($departments->department_name) ?> DEPARTMENT PAYROLL FOR
                        <?= strtoupper(DateTime::createFromFormat('!m', date('m', strtotime($payroll_date)))->format('F')) . ' ' . date('Y', strtotime($payroll_date)) ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold; font-size: large">
                        Currency: Tanzanian Shilings (TSh)
                    </td>
                </tr>
            </thead>
        </table>
    </div>

    <div id="payroll_div" class="col-xs-12 table-responsive">
        <table id="payroll_table" <?php if ($print) { ?> width="100%" border="1" cellspacing="0" style="font-size: 11px" <?php } else { ?> class="table table-bordered table-hover table-striped" <?php } ?>>
            <thead>
                <tr style="background: #b9c6e2">
                    <td style="text-align: center;" colspan="4"><strong>RECRUITMENT</strong></td>
                    <td style="text-align: center;" colspan="<?= 5 + count($all_allowances) + count($all_ssfs) + count($all_loans) ?>"><strong>EMPLOYEE</strong></td>
                    <td style="text-align: center;" colspan="<?= 4 + count($all_ssfs) + count($all_hifs) ?>"><strong>EMPLOYER</strong></td>
                </tr>
                <tr style="background: #b9c6e2">
                    <td>S/No.</td>
                    <td>Postion</td>
                    <td>Name</td>
                    <td>Location</td>
                    <td style="font-weight: bold">B.Salary</td>
                    <?php foreach ($all_allowances as $item) {
                        $data[$item->allowance_name] = 0; ?>
                        <td><?= $item->allowance_name ?></td>
                    <?php } ?>
                    <td style="font-weight: bold">Gross Salary</td>
                    <?php foreach ($all_ssfs as $item) {
                        $data['Deduct' . $item->ssf_name] = 0; ?>
                        <td>Deduct <?= $item->ssf_name ?></td>
                    <?php
                    }
                    ?>
                    <td>Taxable Amount</td>
                    <td>Deduct P.A.Y.E</td>
                    <?php
                    foreach ($all_loans as $item) {
                        $data[$item->id . explode(' ', strtoupper($item->loan_type))[0]] = 0;
                        switch (explode(' ', strtoupper($item->loan_type))[0]) {
                            case 'ADVANCE': ?>
                                <td><?= strtoupper($item->loan_type) ?></td>
                            <?php
                                break;
                            default:
                                $data[$item->id . explode(' ', strtoupper($item->loan_type))[0] . 'total'] = 0;
                                $data[$item->id . explode(' ', strtoupper($item->loan_type))[0] . 'repay'] = 0;
                                $data[$item->id . explode(' ', strtoupper($item->loan_type))[0] . 'balance'] = 0; ?>
                                <td><?= strtoupper($item->loan_type) ?></td>
                                <td><?= strtoupper('loan repay') ?></td>
                                <td><?= strtoupper($item->loan_type . ' balance') ?></td>
                    <?php
                                break;
                        }
                    }
                    ?>
                    <td style="font-weight: bold">NETPAY</td>
                    <td>SDL</td>
                    <td>WCF</td>
                    <?php foreach ($all_ssfs as $item) {
                        $data[$item->ssf_name] = 0; ?>
                        <td><?= $item->ssf_name ?></td>
                    <?php
                    }
                    foreach ($all_hifs as $item) {
                        $data[$item->hif_name] = 0; ?>
                        <td><?= $item->hif_name ?></td>
                    <?php
                    }
                    ?>
                    <td style="font-weight: bold;">TOTAl EMPLOYER'S CONTRIBUTION</td>
                    <td style="font-weight: bold;">GROSS BY EMPLOYER</td>
                </tr>
            </thead>
            <tbody>

                <?php
                $count = 1;
                $total_basic_salary = 0;
                $total_gross_salary = 0;
                $total_deduct_nssf = 0;
                $total_taxable_amount = 0;
                $total_deduct_paye = 0;
                $total_netpay = 0;
                $total_sdl = 0;
                $total_wcf = 0;
                $total_nssf = 0;
                $total_nhif = 0;
                $total_emplyr_contribution = 0;
                $total_emplyr_paid = 0;
                foreach ($employee_data as $employee) {
                    $total_basic_salary = $total_basic_salary + $employee['employee_basic_salary'];
                    $total_gross_salary = $total_gross_salary + $employee['employee_gross_salary'];
                    //  $total_deduct_nssf = $total_deduct_nssf + $employee['employee_deducted_nssf'];
                    $total_taxable_amount = $total_taxable_amount + $employee['employee_taxable_amount'];
                    $total_deduct_paye = $total_deduct_paye + $employee['employee_paye'];
                    $total_netpay = $total_netpay + $employee['employee_netpay'];
                    $total_sdl = $total_sdl + $employee['sdl'];
                    $total_wcf = $total_wcf + $employee['wcf'];
                    ///  $total_nssf = $total_nssf + $employee['nssf'];
                    ////   $total_nhif = $total_nhif + $employee['nhif']; 
                ?>
                    <tr>
                        <td><?= $count ?></td>
                        <td style="min-width: 150px"><?= $employee['employee_title'] ?></td>
                        <td style="min-width: 170px" <?php if (!$print && $employee['flag']) { ?> id="<?= $employee['id'] ?>" worked_days="<?= $employee['worked_days'] ?>" terminating_date="<?= $employee['terminating_date'] ?>" terminating_date_modified="<?= set_date($employee['terminating_date']) ?>" class="flaged" <?php } ?>>
                            <?php if (!$print && $employee['flag']) { ?> <span style="cursor: pointer;" title="<?= $employee['contract_status'] ?>"><?= $employee['contract_status'] ?></span>
                                <img style="max-width: 20px; max-height: 20px" src="<?= base_url('images/red_flag.png') ?>" class="img-circle" alt="User Image">
                            <?php }
                            echo $employee['employee_name'] ?>
                        </td>
                        <td style="min-width: 100px"><?= $employee['employee_location'] = 'Head Quarters' ? 'HQ' : $employee['employee_location'] ?></td>
                        <td style="font-weight: bold; text-align: right"><?= number_format($employee['employee_basic_salary'], 2) ?></td>
                        <?php foreach ($all_allowances as $item) {
                            $allowance_amount = 0;
                            foreach ($employee['employee_allowances'] as $emp_allowance) {
                                if ($emp_allowance['allowance_name'] == $item->allowance_name) {
                                    $allowance_amount = $emp_allowance['allowance_amount'];
                                }
                                $data[$item->allowance_name] = $data[$item->allowance_name] + $allowance_amount;
                            } ?>
                            <td style="text-align: right"><?= number_format($allowance_amount, 2) ?></td>
                        <?php } ?>
                        <td style="font-weight: bold; text-align: right <?= $employee['recalculated'] ? '; background: #a5a3a3"' : '"' ?> > <?= number_format($employee['employee_gross_salary'], 2) ?></td>
                        <?php foreach ($all_ssfs as $item) {
                            $disp_ssf = 0;
                            foreach ($employee['employee_ssfs'] as $emp_ssf) {
                                if ($emp_ssf['ssf_name'] == $item->ssf_name) {
                                    $disp_ssf = $emp_ssf['ssf_deducted_amount'];
                                }
                            }
                            $data['Deduct' . $item->ssf_name] = $data['Deduct' . $item->ssf_name] + $disp_ssf;
                        ?>
                        <td style=" text-align: right"><?= number_format($disp_ssf, 2) ?></td>
                    <?php } ?>
                    <td style=" text-align: right"><?= number_format($employee['employee_taxable_amount'], 2) ?></td>
                    <td style="text-align: right"><?= number_format($employee['employee_paye'], 2) ?></td>
                    <?php foreach ($all_loans as $item) {
                        switch (explode(' ', strtoupper($item->loan_type))[0]) {
                            case 'ADVANCE':
                                $advance_disp = 0;
                                foreach ($employee['employee_loans'] as $emp_loan) {
                                    if ($emp_loan['loan_id'] == $item->id) {
                                        $advance_disp = $emp_loan['monthly_deduction_amount'];
                                    }
                                }
                                $data[$item->id . explode(' ', strtoupper($item->loan_type))[0]] = $data[$item->id . explode(' ', strtoupper($item->loan_type))[0]] + $advance_disp; ?>
                                <td style="text-align: right"><?= number_format($advance_disp, 2) ?></td>
                            <?php
                                break;
                            default:

                                $loan_amount = 0;
                                $loan_repay_amount = 0;
                                $loan_balance_amount = 0;
                                foreach ($employee['employee_loans'] as $emp_loan) {
                                    if ($emp_loan['loan_id'] == $item->id) {
                                        $loan_amount = $emp_loan['total_loan_amount'];
                                        $loan_repay_amount = $emp_loan['monthly_deduction_amount'];
                                        $loan_balance_amount = $emp_loan['loan_balance_amount'];
                                    }
                                }
                                $data[$item->id . explode(' ', strtoupper($item->loan_type))[0] . 'total'] = $data[$item->id . explode(' ', strtoupper($item->loan_type))[0] . 'total'] + $loan_amount;
                                $data[$item->id . explode(' ', strtoupper($item->loan_type))[0] . 'repay'] = $data[$item->id . explode(' ', strtoupper($item->loan_type))[0] . 'repay'] + $loan_repay_amount;
                                $data[$item->id . explode(' ', strtoupper($item->loan_type))[0] . 'balance'] = $data[$item->id . explode(' ', strtoupper($item->loan_type))[0] . 'balance'] + $loan_balance_amount; ?>
                                <td style="text-align: right"><?= number_format($loan_amount, 2) ?></td>
                                <td style="text-align: right"><?= number_format($loan_repay_amount, 2) ?></td>
                                <td style="text-align: right"><?= number_format($loan_balance_amount, 2) ?></td>
                    <?php
                                break;
                        }
                    }
                    ?>
                    <td style="font-weight: bold; text-align: right"><?= number_format($employee['employee_netpay'], 2) ?></td>
                    <?php
                    $total_contribution = $employee['sdl'] + $employee['wcf'];
                    ?>
                    <td style="text-align: right"><?= number_format($employee['sdl'], 2) ?></td>
                    <td style="text-align: right"><?= number_format($employee['wcf'], 2) ?></td>
                    <?php foreach ($all_ssfs as $item) {
                        $ssf_employer_disp = 0;
                        foreach ($employee['employer_paying_ssf'] as $emp_ssf) {
                            if ($emp_ssf['ssf_name'] == $item->ssf_name) {
                                $ssf_employer_disp = $emp_ssf['ssf_deducted_amount'];
                            }
                        }
                        $data[$item->ssf_name] = $data[$item->ssf_name] + $ssf_employer_disp;
                        $total_contribution += $ssf_employer_disp; ?>
                        <td style="text-align: right"><?= number_format($ssf_employer_disp, 2) ?></td>
                    <?php
                    }

                    foreach ($all_hifs as $item) {
                        $hif_employer_disp = 0;
                        foreach ($employee['employer_paying_hifs'] as $emp_hif) {
                            if ($emp_hif['hif_name'] == $item->hif_name) {
                                $hif_employer_disp = $emp_hif['hif_deduction_amount'];
                            }
                        }
                        $data[$item->hif_name] = $data[$item->hif_name] + $hif_employer_disp;
                        $total_contribution += $ssf_employer_disp; ?>
                        <td style="text-align: right"><?= number_format($hif_employer_disp, 2) ?></td>
                    <?php
                    }
                    $total_emplyr_contribution += $total_contribution;
                    $total_emplyr_paid += $emplyr_paid = $employee['employee_gross_salary'] + $total_contribution
                    ?>
                    <td style="font-weight: bold; text-align: right"><?= number_format($total_contribution, 2) ?></td>
                    <td style="font-weight: bold; text-align: right"><?= number_format($emplyr_paid, 2) ?></td>
                    </tr>

                <?php
                    $count++;
                }
                ?>
            </tbody>
            <tfoot>
                <tr style="background: #b9c6e2">
                    <td colspan="2" style="font-weight: bold">GRAND TOTAL</td>
                    <td colspan="2" style="font-weight: bold">As proposed</td>
                    <td style="font-weight: bold; text-align: right"><?= number_format($total_basic_salary, 2) ?></td>
                    <?php foreach ($all_allowances as $item) { ?>
                        <td style="font-weight: bold; text-align: right"><?= number_format($data[$item->allowance_name], 2) ?></td>
                    <?php } ?>
                    <td style="font-weight: bold; text-align: right"><?= number_format($total_gross_salary, 2) ?></td>
                    <?php foreach ($all_ssfs as $item) { ?>
                        <td style="font-weight: bold; text-align: right"><?= number_format($data['Deduct' . $item->ssf_name], 2) ?></td>
                    <?php } ?>
                    <td style="font-weight: bold; text-align: right"><?= number_format($total_taxable_amount, 2) ?></td>
                    <td style="font-weight: bold; text-align: right"><?= number_format($total_deduct_paye, 2) ?></td>
                    <?php foreach ($all_loans as $item) {
                        switch (explode(' ', strtoupper($item->loan_type))[0]) {
                            case 'ADVANCE': ?>
                                <td style="font-weight: bold; text-align: right"><?= number_format($data[$item->id . explode(' ', strtoupper($item->loan_type))[0]], 2) ?></td>
                            <?php
                                break;
                            default: ?>
                                <td style="font-weight: bold; text-align: right"><?= number_format($data[$item->id . explode(' ', strtoupper($item->loan_type))[0] . 'total'], 2) ?></td>
                                <td style="font-weight: bold; text-align: right"><?= number_format($data[$item->id . explode(' ', strtoupper($item->loan_type))[0] . 'repay'], 2) ?></td>
                                <td style="font-weight: bold; text-align: right"><?= number_format($data[$item->id . explode(' ', strtoupper($item->loan_type))[0] . 'balance'], 2) ?></td>
                    <?php
                                break;
                        }
                    } ?>
                    <td style="font-weight: bold; text-align: right"><?= number_format($total_netpay, 2) ?></td>
                    <td style="font-weight: bold; text-align: right"><?= number_format($total_sdl, 2) ?></td>
                    <td style="font-weight: bold; text-align: right"><?= number_format($total_wcf, 2) ?></td>
                    <?php foreach ($all_ssfs as $item) { ?>
                        <td style="font-weight: bold; text-align: right"><?= number_format($data[$item->ssf_name], 2) ?></td>
                    <?php
                    }
                    foreach ($all_hifs as $item) { ?>
                        <td style="font-weight: bold; text-align: right"><?= number_format($data[$item->hif_name], 2) ?></td>
                    <?php } ?>
                    <td style="font-weight: bold; text-align: right"><?= number_format($total_emplyr_contribution, 2) ?></td>
                    <td style="font-weight: bold; text-align: right"><?= number_format($total_emplyr_paid, 2) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <br />
    <table width="35%" cellspacing="0.5" <?php if ($print) { ?> style="font-size: 11px" <?php } ?>>
        <tr>
            <td style="text-align: left; font-size: small">
                <span class="pull-left"><strong>GROSS: </strong></span>
            </td>
            <td style="text-align: right; font-size: small">
                <span class="pull-right"><strong><?= number_format($total_emplyr_paid, 2) ?></strong></span>
            </td>
        </tr>
        <tr>
            <td style="text-align: left; font-size: small">
                <span class="pull-left"><strong>NET EMEPLOYEE PAYMENT: </strong></span>
            </td>
            <td style="text-align: right; font-size: small">
                <span class="pull-right"><strong><?= number_format($total_netpay, 2) ?></strong></span>
            </td>
        </tr>
        <?php
        $total_headcount = $total_emplyr_paid + $total_netpay + $total_deduct_paye + $total_wcf + $total_sdl;
        foreach ($all_ssfs as $item) {
            $total_headcount += $data[$item->ssf_name] ?>
            <tr>
                <td style="text-align: left; font-size: small">
                    <span class="pull-left"><strong><?= strtoupper($item->ssf_name) ?>: </strong></span>
                </td>
                <td style="text-align: right; font-size: small">
                    <span class="pull-right"><strong><?= number_format($data[$item->ssf_name], 2) ?></strong></span>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td style="text-align: left; font-size: small">
                <span class="pull-left"><strong>PAYE: </strong></span>
            </td>
            <td style="text-align: right; font-size: small">
                <span class="pull-right"><strong><?= number_format($total_deduct_paye, 2) ?></strong></span>
            </td>
        </tr>
        <tr>
            <td style="text-align: left; font-size: small">
                <span class="pull-left"><strong>SDL: </strong></span>
            </td>
            <td style="text-align: right; font-size: small">
                <span class="pull-right"><strong><?= number_format($total_sdl, 2) ?></strong></span>
            </td>
        </tr>
        <tr>
            <td style="text-align: left; font-size: small">
                <span class="pull-left"><strong>WCF: </strong></span>
            </td>
            <td style="text-align: right; font-size: small">
                <span class="pull-right"><strong><?= number_format($total_wcf, 2) ?></strong></span>
            </td>
        </tr>
        <?php foreach ($all_hifs as $item) {
            $total_headcount += $data[$item->hif_name] ?>
            <tr>
                <td style="text-align: left; font-size: small">
                    <span class="pull-left"><strong><?= strtoupper($item->hif_name) ?>: </strong></span>
                </td>
                <td style="text-align: right; font-size: small">
                    <span class="pull-right"><strong><?= number_format($data[$item->hif_name], 2) ?></strong></span>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td style="text-align: left; font-size: small">
                <span class="pull-left"><strong>HEADCOUNT: </strong></span>
            </td>
            <td style="text-align: right; font-size: small">
                <span class="pull-right"><strong><?= number_format($total_headcount, 2) ?></strong></span>
            </td>
        </tr>
    </table>
<?php
}
