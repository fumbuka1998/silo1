<?php
/**
 * Created by PhpStorm.
 * User: zeus
 * Date: 25/04/2019
 * Time: 10:41
 */

?>

<table>
    <thead>
    <tr>
        <td style="font-weight: bold; font-size: large">
            <?= strtoupper($departments->department_name) ?> DEPARTMENT, PAYROLL NET PAYABLE FOR
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

<br/>

<table  class="table table-bordered table-hover employee_payroll_deductions_table">
    <thead>
    <tr style="background: rgba(165,177,202,0.63)">
        <th>Name</th>
        <th>Title</th>
        <th>Location</th>
        <th>NET PAYABLE</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total_net_payable = 0;
    foreach ($employee_info as $employee){
        $total_net_payable += $employee['net_pay'];
        ?>
        <tr>
            <td><?= $employee['employee_full_name'] ?></td>
            <td><?= $employee['title'] ?></td>
            <td><?= $employee['location'] ?></td>
            <td style="text-align: right"><?= number_format($employee['net_pay'],2) ?></td>

        </tr>

        <?php
    } ?>
    </tbody>
    <tfoot id="tfooter"  >
    <tr style="background: rgba(165,177,202,0.63)">
        <td colspan="3" style="font-weight: bold">TOTAL</td>
        <td style="text-align: right; font-weight: bold"><?= number_format($total_net_payable,2) ?></td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td style="text-align: right"><button style="text-align: right;<?= $payroll_payment->chek_if_this_payment_was_made($payroll_id, 'net payable') == 'true' ? 'display: none;' : '' ?>" id="net_pay" class="button btn-success btn-xs net_payable_payments">Pay NET PAYABLE</button>
            <form method="post" target="_blank" action="<?= base_url('human_resource/human_resources/payroll_netpayable_preview') ?>">
                <input type="hidden" name="payroll_id" value="<?= $payroll_id ?>">
                <input type="hidden" name="deduction_name" value="net_pay">
                <button style="color: #0c0c0c; text-align: right;<?= $payroll_payment->chek_if_this_payment_was_made($payroll_id, 'net payable') != 'true' ? 'display: none;' : '' ?>" id="previewnet_pay" class="button btn-info btn-xs net_payable_payments_preview"><i class="fa fa-file-pdf-o"></i> NET PAYABLE Payments</button>
            </form>
        </td>
    </tr>
    </tfoot>
</table>
