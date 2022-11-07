<?php
/**
 * Created by PhpStorm.
 * User: kasobokihuna
 * Date: 23/05/2019
 * Time: 11:05
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center"><?= strtoupper($cost_center->cost_center_name) ?> EXPENSES</h2>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td style="  vertical-align: top">
            <strong>From: </strong><?= custom_standard_date($from) ?>
        </td>
        <td style="  vertical-align: top">
            <strong>To: </strong><?= custom_standard_date($to) ?>
        </td>
    </tr>
</table>
<br/>
<table width="100%" border="1" cellspacing="0" style="font-size: 11px">
    <thead>
    <tr>
        <th>Descriptions</th><th>Amount</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total_per_cc = 0;
    foreach ($cost_center_payments[$cost_center->cost_center_name] as $othr_admin_cost) {
        $total_per_cc += $othr_admin_cost['amount_in_basecurrency'];
        ?>
        <tr>
            <td style="text-align: left"><?= explode('-',$othr_admin_cost['cost_type'])[0] ?></td>
            <td style="text-align: right"><?= $print ? number_format($othr_admin_cost['amount_in_basecurrency'], 2) : $othr_admin_cost['othr_admin_costs_pop_up'] ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td style="text-align: right; font-weight: bold;">SUB TOTAL</td>
        <td style="text-align: right; font-weight: bold;"><?= number_format($total_per_cc, 2) ?></td>
    </tr>
    </tfoot>
</table>