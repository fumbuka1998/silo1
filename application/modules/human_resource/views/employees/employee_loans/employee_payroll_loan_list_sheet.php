<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 2/15/2019
 * Time: 3:39 PM
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center"> PAYROLL <?= $loan_name == 'heslb' ? 'HESLB LOAN' : $loan_name == 'company' ? 'COMPANY LOAN' : 'ADVANCE' ?> PAYMENTS SHEET </h2>
<br/>
<br/>

<div class="container-fluid">
    <table>
        <thead>
        <tr>
            <td style="font-weight: bold; font-size: large">
                <?= strtoupper($departments->department_name) ?> DEPARTMENT:  <?= $loan_name == 'heslb' ? 'HESLB LOAN' : $loan_name == 'company' ? 'COMPANY LOAN' : 'ADVANCE' ?> PAYMENTS FOR PAYROLL OF
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


<table width="100%" border="1" cellspacing="0"
       style="font-size: 11px">
    <thead>
    <tr style="background: #a4b2cb">
        <th>S/No</th>
        <th>Name</th>
        <?php
        if($loan_name == 'heslb'){
            ?>

            <th>C.S.E.E Index Number</th>

            <?php
        }
        if($loan_name == 'advance'){
            $total_advance = 0;
            ?>
            <th>Amount</th>
            <?php
        }else{
            $total_loan = 0;
            $total_loan_repay = 0;
            $total_loan_balance = 0;
            ?>
            <th>Total Loan</th>
            <th>Monthly Payment</th>
            <th>Loan Balance</th>
            <?php
        }
        ?>
    </tr>
    </thead>

    <tbody>
    <?php
    foreach ($employee_info as $index => $payment){
        ?>

        <tr>
            <td style="text-align: left"><?= $index+1 ?></td>
            <td style="text-align: left"><?= $payment['employee_name'] ?></td>
            <?php
            if($loan_name == 'heslb'){
                ?>

                <td style="text-align: left"><?= $payment['csee_number'] ?></td>

                <?php
            }
            if($loan_name == 'advance'){
                $total_advance += $payment['loan_repay'];
                ?>
                <td style="text-align: right"><?= number_format($payment['loan_repay'],2) ?></td>
                <?php
            }else{
                $total_loan += $payment['total_loan'];
                $total_loan_repay += $payment['loan_repay'];
                $total_loan_balance += $payment['loan_balance'];
                ?>
                <td style="text-align: right"><?= number_format($payment['total_loan'],2) ?></td>
                <td style="text-align: right"><?= number_format($payment['loan_repay'],2) ?></td>
                <td style="text-align: right"><?= number_format($payment['loan_balance'],2) ?></td>
                <?php
            }
            ?>

        </tr>

        <?php
    }
    ?>
    </tbody>
    <tfoot>
    <tr >
        <?php
        if($loan_name == 'heslb'){
            ?>

            <td colspan="3" style="text-align: left; font-weight: bold">TOTAL</td>

            <?php
        }else{
            ?>
            <td colspan="2" style="text-align: left; font-weight: bold">TOTAL</td>
            <?
        }
        if($loan_name == 'advance'){
            ?>
            <td style="text-align: right; font-weight: bold"><?= number_format($total_advance,2) ?></td>
            <?php
        }else{

            ?>
            <td style="text-align: right; font-weight: bold"><?= number_format($total_loan,2) ?></td>
            <td style="text-align: right; font-weight: bold"><?= number_format($total_loan_repay,2) ?></td>
            <td style="text-align: right; font-weight: bold"><?= number_format($total_loan_balance,2) ?></td>
            <?php
        }
        ?>

    </tr>
    </tfoot>


</table>
