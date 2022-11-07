<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 2/15/2019
 * Time: 3:39 PM
 */

$this->load->view('includes/letterhead');

?>
    <h2 style="text-align: center">LOAN PAYMENT HISTORY</h2>
    <br/>
    <br/>

<table>
    <tbody>
    <tr style="font-weight: bold">
        <td><h4>Employee name:</h4></td>
        <td><?= $employee_data->full_name() ?></td>
    </tr>
    <tr style="font-weight: bold">
        <td><h4>Department:</h4></td>
        <td><?= $employee_data->department()->department_name ?></td>
    </tr>
    <tr style="font-weight: bold">
        <td><h4>Title:</h4></td>
        <td><?= $employee_data->position()->position_name ?></td>
    </tr>
    <tr style="font-weight: bold">
        <td><h4>Loan</h4></td>
        <td><?= $loan->loan_type ?></td>
    </tr>
    <tr>
        <td><h4>Loan Approved Date:</h4></td>
        <td><?= set_date($employee_loan->loan_approved_date) ?></td>
    </tr>
    <tr>
        <td><h4>Total Loan Amount:</h4></td>
        <td><?= 'TSh '.number_format($employee_loan->total_loan_amount, 2) ?></td>
    </tr>
    <tr>
        <td><h4>Monthly Deduction Rate:</h4></td>
        <td><?= 'TSh '.number_format($employee_loan->monthly_deduction_amount, 2) ?></td>
    </tr>
    </tbody>
</table>
<table width="100%" border="1" cellspacing="0" style="font-size: 11px">
    <thead>
    <tr style="background: #b5c4e6">
        <th>S/No</th>
        <th>Payment Date</th>
        <th>Paid Amount</th>
        <th>Balance</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $count = 1;

    if($employee_loan_payments){

        foreach ($employee_loan_payments as $payment) {
            ?>
            <tr>
                <td style="text-align: center"><?= $count ?></td>
                <td><?= set_date($payment->paid_date) ?></td>
                <td style="text-align: right"><?= number_format($payment->paid_amount,2) ?></td>
                <td style="text-align: right"><?= number_format($payment->loan_balance_amount,2) ?></td>
            </tr>
            <?php
            $count++;
        }

    }else{

        ?>
        <tr> <td colspan="4" style="text-align: center;"> <h2>No Payment Made</h2></td></tr>
        <?php

    }

?>

    </tbody>

</table>
<?php

?>