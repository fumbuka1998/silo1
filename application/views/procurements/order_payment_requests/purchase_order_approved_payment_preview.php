<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/1/2018
 * Time: 2:41 AM
 */

$this->load->view('includes/letterhead');
$last_approval = $payment_request_approval->purchase_order_payment_request()->last_approval();
$last_approval_id = $last_approval->{$last_approval::DB_TABLE_PK};
$payment_request = $payment_request_approval->purchase_order_payment_request();
$order = $payment_request->purchase_order();
?>

<h2 style="text-align: center">APPROVED PAYMENT</h2>
<table style="font-size: 12px" width="100%">
    <tr>
        <td style="font-weight: bold; font-size: 14px">
            <b>Requested For : </b><?= $payment_request_approval->cost_center_name() ?><br/><br/>
        </td>
    </tr>
    <tr>
        <td>
            <b>Request No. : </b><?= $payment_request->request_number() ?>
        </td>
        <td>
            <b>P.O No. : </b><?= $order->order_number() ?>
        </td>
    </tr>
    <tr>
        <td>
            <b>Request Date : </b><?= custom_standard_date($payment_request->request_date) ?>
        </td>
        <td>
            <b>Currency : </b><?= $payment_request->currency()->name_and_symbol() ?>
        </td>
    </tr>
</table>

<br/><br/>
<table style="font-size: 12px" width="100%" cellspacing="0" border="1">
    <thead>
    <tr>
        <th>No.</th>
        <th>Description</th>
        <th>Claimed By</th>
        <th>Reference</th>
        <th>Approved Amount</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $invoice_items = $payment_request_approval->purchase_order_payment_request()->invoice_items();
    $total_amount = $sn = 0;

    foreach ($invoice_items as $invoice_item){
        $sn++;
        $invoice = $invoice_item->invoice();
        $approved_item = $invoice_item->approved_item($last_approval_id);
        $amount = $approved_item->approved_amount;
        $total_amount += $amount;
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= $invoice_item->description ?></td>
            <td><?= $invoice_item->invoice()->stakeholder()->stakeholder_name ?></td>
            <td><?= $invoice->reference ?></td>
            <td style="text-align: right"><?=  $invoice_item->purchase_order_payment_request()->currency()->symbol.' '.number_format($amount,2) ?></td>
        </tr>
        <?php
    }
    $cash_items = $payment_request_approval->purchase_order_payment_request()->cash_items();
    foreach ($cash_items as $cash_item){
        $sn++;
        $approved_item = $cash_item->approved_item($last_approval_id);
        $amount = $approved_item->approved_amount;
        $total_amount += $amount;
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= $cash_item->description ?></td>
            <td><?= $cash_item->claimed_by ?></td>
            <td><?= $cash_item->reference ?></td>
            <td style="text-align: right"><?= $payment_request->currency()->symbol.'&nbsp;'.number_format($amount,2) ?></td>
        </tr>
    <?php }?>

    ?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="4" style="text-align: left">TOTAL</th><th style="text-align: right"><?= $payment_request->currency()->symbol.'&nbsp;'. number_format($total_amount,2) ?></th>
    </tr>
    </tfoot>
</table>
<br/><br/>

<table style="font-size: 12px" width="100%">
    <tr>
        <td style="width: 30%; vertical-align: top">
            <strong>Requested By: </strong><br/><?= $payment_request->requester()->full_name() ?>
        </td>
        <td style="width: 30%; vertical-align: top">
            <strong>Request Date: </strong><br/><?= custom_standard_date($payment_request->request_date) ?>
        </td>

        <td style=" vertical-align: top">
            <strong>Comments: </strong><br/><?= nl2br($payment_request->remarks) ?>
        </td>
    </tr>
    <tr>
        <td colspan="3"><hr/></td>
    </tr>
    <tr>
        <td style="width: 30%; vertical-align: top">
            <strong>Approved By: </strong><br/><?= $payment_request_approval->employee()->full_name() ?>
        </td>
        <td style="width: 30%; vertical-align: top">
            <strong>Approval Date: </strong><br/><?= custom_standard_date($payment_request_approval->approval_date) ?>
        </td>
        <td style=" vertical-align: top">
            <strong>Comments: </strong><br/><?= nl2br($payment_request_approval->comments) ?>
        </td>
    </tr>
</table>
