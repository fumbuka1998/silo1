<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/25/2018
 * Time: 10:53 PM
 */

$this->load->view('includes/letterhead');
$sub_contract_requisition = $sub_contract_requisition_approval->sub_contract_requisition();
?>
<h2 style="text-align: center">APPROVED SUB CONTRACT PAYMENT</h2>
<span style="font-weight: bold; font-size: 12px"><?= $cost_center_name ?></span>
<br/>
<strong>Req. No. </strong><?= $sub_contract_requisition->sub_contract_requisition_number() ?>
<br/>
<br/>
<br/>

<table style="font-size: 10px" width="100%" cellspacing="0" border="1">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Description</th>
            <th>Certificate No.</th>
            <th nowrap="true">Approved Amount</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $currency = $sub_contract_requisition->currency();
    $sn = 0;
    $total_amount=0;
    $approved_items = $sub_contract_requisition_approval->approval_items();
    foreach($approved_items as $approved_item){
        $sn++;
        $certificate = $approved_item->sub_contract_payment_requisition_item()->certificate();
        $total_amount += $amount = $approved_item->approved_amount;
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= $certificate->sub_contract()->contract_name.' - '.$certificate->sub_contract()->stakeholder()->stakeholder_name ?></td>
            <td><?= $certificate->certificate_number ?></td>
            <td style="text-align: right"><?= $currency->symbol . ' ' .number_format($amount,2) ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="3" style="text-align: right">TOTAL</th>
        <th style="text-align: right"><?=  $currency->symbol . ' ' .number_format($total_amount,2) ?></th>
    </tr>

    <?php
    if(!is_null($sub_contract_requisition_approval->vat_inclusive) && $sub_contract_requisition_approval->vat_inclusive != 0){
        if(!is_null($sub_contract_requisition_approval->vat_inclusive)) { ?>
            <tr>
                <th style="text-align: right"  colspan="3">VAT </th>
                <th style="text-align: right"><?= $currency->symbol.'  '. number_format($total_amount*0.18,2) ?></th>
            </tr>
        <?php }

        $grand_total = !is_null($sub_contract_requisition_approval->vat_inclusive) ? $total_amount*1.18 : $total_amount;
        ?>

        <tr>
            <th style="text-align: right"  colspan="3">GRAND TOTAL</th>
            <th style="text-align: right"><?= $currency->symbol.'  '. number_format($grand_total,2) ?></th>
        </tr>
    <?php } ?>
    </tfoot>
</table><br/>
<strong>Amount In Words: </strong><?= numbers_to_words($total_amount) ?>
<table style="font-size: 12px" width="100%">
    <tr>
        <td colspan="3"><hr/></td>
    </tr>
    <tr>
        <td style=" width: 30% vertical-align: top">
            <strong>Requested By: </strong><br/><?= $sub_contract_requisition->requester()->full_name() ?>
        </td>
        <td style=" width: 30% vertical-align: top">
            <strong>Request Date: </strong><br/><?= custom_standard_date($sub_contract_requisition->request_date) ?>
        </td>
        <td style=" vertical-align: top">
            <strong>Requesting Comments: </strong><br/><?= $sub_contract_requisition->requesting_comments ?>
        </td>
    </tr>
    <tr>
        <td colspan="3"><hr/></td>
    </tr>
    <tr>
        <td style=" vertical-align: top">
            <strong>Approved By: </strong><br/><?= $sub_contract_requisition_approval->created_by()->full_name() ?>
        </td>
        <td style="vertical-align: top">
            <strong>Approval Date: </strong><br/><?= custom_standard_date($sub_contract_requisition_approval->approval_date) ?>
        </td>
        <td style="vertical-align: top">
            <strong>Approving Comments: </strong><br/><?= $sub_contract_requisition_approval->approving_comments ?>
        </td>
    </tr>

</table>
