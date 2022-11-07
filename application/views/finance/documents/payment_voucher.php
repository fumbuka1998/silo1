<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 11/25/2016
 * Time: 6:40 PM
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">PAYMENT VOUCHER</h2>
<br/>
<table width="100%">
    <tr>
        <td width="50%">
            <strong>For: <?= $payment_voucher->cost_center_name() ?></strong><br/><br/>
            <?php if($payment_voucher->cheque_number != null){ ?>
            <strong>Cheque Number:</strong> <?= $payment_voucher->cheque_number ?><br/>
            <?php } ?>
            <strong>Payee:</strong> <?= $payment_voucher->payee ?>
        </td>
        <td style="vertical-align: top;" width="40%">
            <h3><strong>PV No: </strong><?= $payment_voucher->payment_voucher_number() ?></h3><br/>
            <strong>PV Date: </strong><?= custom_standard_date($payment_voucher->payment_date) ?><br/>
            <strong>Reference: </strong><?= $payment_voucher->reference ?><br/>
        </td>
    </tr>
</table>
<br/>
<table style="font-size: 12px" border="1" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>Description</th><th>Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $pv_items = $payment_voucher->payment_voucher_items();
        $payable_amount = $total_withheld_amount = 0;
        foreach ($pv_items as $pv_item){
            $payable_amount += $pv_item->amount;
            $total_withheld_amount += $pv_item->withholding_tax_amount();
            ?>
            <tr>
                <td><?= $pv_item->description ?></td>
                <td style="text-align: right"><?= $payment_voucher->currency()->symbol .' '.number_format($pv_item->amount,2) ?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
        <tfoot>
        <?php
        $total_amount  = ($payable_amount + $total_withheld_amount);
        if($payment_voucher->withholding_tax != null && $payment_voucher->withholding_tax > 0){
            ?>
            <tr>
                <th style="text-align: right">Withholding Tax</th><th style="text-align: right"><?= $payment_voucher->currency()->symbol .' '.number_format($total_withheld_amount,2) ?></th>
            </tr>
        <?php } ?>
        <tr>
            <th style="text-align: right">TOTAL</th><th style="text-align: right"><?=  $payment_voucher->currency()->symbol .' '.number_format($total_amount,2) ?></th>
        </tr>
        </tfoot>
    </table>
<br/>
<strong>Amount In Words: </strong><br/><?= numbers_to_words($total_amount) ?><br/><br/>
<strong>Remarks: </strong><br/><?= $payment_voucher->remarks ?><br/><br/>
<table width="100%">
    <tr>
        <td style="vertical-align: top" width="33.3%">
            <strong>Authorized By: </strong><br/>
            <i><?= $payment_voucher->payment_voucher_origin() ? $payment_voucher->payment_voucher_origin()->created_by()->full_name() : ''?></i><br/>
            <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span><br/>
            <?= $payment_voucher->payment_voucher_origin() ? $payment_voucher->payment_voucher_origin()->created_by()->position()->position_name : '' ?>
        </td>
        <td style="vertical-align: top"  width="33.3%">
            <strong>Issued By: </strong><br/><br/>
            <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span><br/>
            <?= $payment_voucher->employee()->full_name(); ?>
        </td>
        <td style="vertical-align: top"  width="33.3%">
            <strong>Received By: </strong><br/><br/>
            <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span><br/>
        </td>
    </tr>
</table>


