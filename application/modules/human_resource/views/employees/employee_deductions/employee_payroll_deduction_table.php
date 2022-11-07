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
            <?= strtoupper($departments->department_name) ?> DEPARTMENT, PAYROLL DEDUCTIONS FOR
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
        <?php foreach ($all_deductions as $deduction){
            $data[$deduction->deduction_name] = 0;
            ?>
            <th><?= strtoupper($deduction->deduction_name) ?></th>
            <?php
        } ?>
        <th>P.A.Y.E</th>
    </tr>
    </thead>
    <tbody>
    <?php
     $total_paye = 0;
    foreach ($employee_info as $employee){
        $total_paye += $employee['paye'];
        ?>
        <tr>
            <td><?= $employee['employee_full_name'] ?></td>
            <td><?= $employee['title'] ?></td>
            <td><?= $employee['location'] ?></td>
            <?php

            foreach ($all_deductions as $deduction){

                if(array_key_exists($deduction->deduction_name, $employee)){
                    $deduction_amount = $employee[$deduction->deduction_name];
                } else {
                    $deduction_amount = 0;
                }
                $data[$deduction->deduction_name] += $deduction_amount;
                ?>
                <td style="text-align: right"><?= number_format($deduction_amount,2) ?></td>
                <?php
            } ?>
            <td style="text-align: right"><?= number_format($employee['paye'],2) ?></td>

        </tr>

        <?php
    } ?>
    </tbody>
    <tfoot id="tfooter"  >
    <tr style="background: rgba(165,177,202,0.63)">
        <td colspan="3" style="font-weight: bold">TOTAL</td>
        <?php foreach ($all_deductions as $deduction){
            ?>
            <td style="text-align: right; font-weight: bold"><?= number_format($data[$deduction->deduction_name], 2) ?></td>
            <?php
        } ?>
       <td style="text-align: right; font-weight: bold"><?= number_format($total_paye,2) ?></td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <?php
           $i = 1;
           $color = 'btn-default';
        foreach ($all_deductions as $deduction){
            if($i%2 == 1){$color = 'btn-info';}else{$color = 'btn-success';}
            if($i == 4){$color = 'btn-yahoo';}
            if($i > 3 && $i%2 == 1){$color = 'btn-primary';}
            if($i > 5 && $i%2 == 0){$color = 'btn-yahoo';}
            ?>
            <td style="text-align: right" ><button style="text-align: right;<?= $payroll_payment->chek_if_this_payment_was_made($payroll_id, $deduction->deduction_name) == 'true' ? 'display: none;' : '' ?>" id="<?= $deduction->deduction_name ?>" class="button <?= $color ?> btn-xs deduction_payments">Pay <?= strtoupper($deduction->deduction_name) ?></button>
            <form method="post" target="_blank" action="<?= base_url('human_resource/human_resources/payroll_deduction_preview') ?>">
                <input type="hidden" name="payroll_id" value="<?= $payroll_id ?>">
                <input type="hidden" name="deduction_name" value="<?= $deduction->deduction_name ?>">
            <button style="color: #0c0c0c; text-align: right;<?= $payroll_payment->chek_if_this_payment_was_made($payroll_id, $deduction->deduction_name) != 'true' ? 'display: none;' : '' ?>" id="<?= 'preview'.$deduction->deduction_name ?>" class="button btn-info btn-xs deduction_payments_preview"><i class="fa fa-file-pdf-o"></i><?= '  '.strtoupper($deduction->deduction_name) ?> Payments</button>
            </form>
            </td>
            <?php
            $i++;
        } ?>
        <td style="text-align: right"><button style="text-align: right;<?= $payroll_payment->chek_if_this_payment_was_made($payroll_id, 'PAYE') == 'true' ? 'display: none;' : '' ?>" id="paye" class="button btn-primary btn-xs deduction_payments">Pay P.A.Y.E</button>
            <form method="post" target="_blank" action="<?= base_url('human_resource/human_resources/payroll_deduction_preview') ?>">
                <input type="hidden" name="payroll_id" value="<?= $payroll_id ?>">
                <input type="hidden" name="deduction_name" value="paye">
            <button style="color: #0c0c0c; text-align: right;<?= $payroll_payment->chek_if_this_payment_was_made($payroll_id, 'PAYE') != 'true' ? 'display: none;' : '' ?>" id="previewpaye" class="button btn-info btn-xs deduction_payments_preview"><i class="fa fa-file-pdf-o"></i> P.A.Y.E Payments</button>
            </form>
        </td>
    </tr>
    </tfoot>
</table>
