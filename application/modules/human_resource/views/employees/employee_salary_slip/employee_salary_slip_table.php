<?php
/**
 * Created by PhpStorm.
 * User: zeus
 * Date: 25/04/2019
 * Time: 10:41
 */

?>

<form method="post" target="_blank"  action="<?= base_url('human_resource/human_resources/payroll_salary_slip_table') ?>">
    <input id="heslb_payroll_id" type="hidden" name="payroll_id" value="<?= $payroll_id ?>">
    <input id="print_slip" type="hidden" name="print" value="true">

<table>
    <thead>
    <tr>
        <td style="font-weight: bold; font-size: large">
            <?= strtoupper($departments->department_name) ?> DEPARTMENT, PAYROLL SALARY SLIPS FOR
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

<table  class="table table-bordered table-hover employee_salary_slip_table">
    <thead>
    <tr style="background: rgba(165,177,202,0.63)">
        <th style="text-align: center"><input id="all_employee_checkbox" type="checkbox"></th>
        <th>Name</th>
        <th>Title</th>
        <th>Location</th>
    </tr>
    </thead>
    <tbody >
    <?php
    foreach ($employee_info as $employee){
        ?>
        <tr>
            <td style="text-align: center">
                <input id="<?= $employee['employee_id'] ?>" value="<?= $employee['employee_id'] ?>"  name="employee_checkbox[]" type="checkbox">
            </td>
            <td><?= $employee['employee_name'] ?></td>
            <td><?= $employee['title'] ?></td>
            <td><?= $employee['location'] ?></td>
        </tr>

        <?php
    } ?>
    </tbody>
</table>

<div class="pull-right margin-r-5">

        <button style="text-align: right" id="print_salary_slip" class="button btn-primary btn-xs"><i class="fa fa-print"></i> Print</button>

</div>
</form>
