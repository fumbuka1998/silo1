<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 2/15/2019
 * Time: 3:39 PM
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center"> NET PAYABLE SHEET </h2>
<br/>
<br/>

<div class="container-fluid">
    <table>
        <thead>
        <tr>
            <td style="font-weight: bold; font-size: large">
                <?= strtoupper($departments->department_name) ?> DEPARTMENT:  NET PAYABLE FOR PAYROLL OF
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
        <th>Title</th>
        <th>Location</th>
        <th>NET PAYABLE</th>
    </tr>
    </thead>

    <tbody>
    <?php
    $total_netpayable = 0;

    foreach ($payment_info as $index => $payment){
        $total_netpayable += $payment->amount;
        $employee->load($sallary_info[$index]->employee_id);
        ?>

        <tr>
            <td style="text-align: left"><?= $index+1 ?></td>
            <td style="text-align: left" ><?= strtoupper($employee->full_name()) ?></td>
            <td style="text-align: left" ><?= strtoupper($sallary_info[$index]->title) ?></td>
            <td style="text-align: left" ><?= strtoupper($sallary_info[$index]->location) ?></td>
            <td style="text-align: right"><?= number_format($payment->amount,2) ?></td>
        </tr>

        <?php
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td style="text-align: left; font-weight: bold" colspan="4">TOTAL</td>
        <td style="text-align: right; font-weight: bold"><?= number_format($total_netpayable,2) ?></td>
    </tr>
    </tfoot>


</table>
