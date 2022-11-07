<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 8/13/2018
 * Time: 1:19 PM
 */

$this->load->view('includes/letterhead');
$requisition = $imprest_voucher->requisition();
?>
<h2 style="text-align: center">IMPREST VOUCHER</h2>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td style=" width: 30%">
            <strong>Imprest Date: </strong><?= custom_standard_date($imprest_voucher->imprest_date) ?>
        </td>
        <td style=" width: 30%">
            <strong>Imprest Voucher No: </strong><?= $requisition->requisition_number().', IMPV/'.add_leading_zeros($imprest_voucher->{$imprest_voucher::DB_TABLE_PK}) ?>
        </td>
    </tr>
    <tr>
        <td style=" width: 30%">
            <strong>Paid From: </strong><?= $imprest_voucher->credit_account()->account_name ?>
        </td>
        <td style=" width: 30%">
            <strong>Paid To: </strong><?= $imprest_voucher->debit_account()->account_name ?>
        </td>
    </tr>
</table>
<br/>
<table style="font-size: 10px" width="100%" cellspacing="0" border="1">
    <thead>
        <tr  style="background-color: #dfdfdf">
            <th>S.No</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Rate</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $sn = 0;
    $total_amount=0;

    $imprest_voucher_material_items = $imprest_voucher->material_items();
    foreach($imprest_voucher_material_items as $imprest_material_item){
        $sn++;
        $approved_material_item = $imprest_material_item->requisition_approval_material_item();
        $material = $approved_material_item->material_item();
        $total_amount += $amount = $approved_material_item->approved_quantity*$approved_material_item->approved_rate;
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= $material->item_name ?></td>
            <td><?= $approved_material_item->approved_quantity ?></td>
            <td><?= $material->unit()->symbol ?></td>
            <td style="text-align: right"><?=  number_format($approved_material_item->approved_rate,2) ?></td>
            <td style="text-align: right"><?= number_format($amount,2) ?></td>
        </tr>
        <?php
    }

    $imprest_voucher_asset_items = $imprest_voucher->asset_items();
    foreach($imprest_voucher_asset_items as $imprest_asset_item){
        $sn++;
        $approved_asset_item = $imprest_asset_item->requisition_approval_asset_item();
        $asset_item = $approved_asset_item->requisition_asset_item()->asset_item();
        $total_amount += $amount = $approved_asset_item->approved_quantity*$approved_asset_item->approved_rate;
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= $asset_item->asset_name ?></td>
            <td><?= $approved_asset_item->approved_quantity ?></td>
            <td></td>
            <td style="text-align: right"><?=  number_format($approved_asset_item->approved_rate,2) ?></td>
            <td style="text-align: right"><?= number_format($amount,2) ?></td>
        </tr>
        <?php
    }

    $imprest_voucher_cash_items = $imprest_voucher->cash_items();
    foreach($imprest_voucher_cash_items as $imprest_cash_item){
        $sn++;
        $approved_cash_item = $imprest_cash_item->requisition_approval_cash_item();
        $total_amount += $amount = $approved_cash_item->approved_quantity * $approved_cash_item->approved_rate;
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= $approved_cash_item->requisition_cash_item()->description ?></td>
            <td><?= $approved_cash_item->approved_quantity ?></td>
            <td><?= $approved_cash_item->requisition_cash_item()->measurement_unit()->symbol ?></td>
            <td style="text-align: right"><?=  number_format($approved_cash_item->approved_rate,2) ?></td>
            <td style="text-align: right"><?= number_format($amount,2) ?></td>
        </tr>
        <?php
    }

    $imprest_voucher_service_items = $imprest_voucher->service_items();
    foreach($imprest_voucher_service_items as $imprest_service_items){
        $sn++;
        $approved_service_item = $imprest_service_items->requisition_approval_service_item();
        $total_amount += $amount = $approved_service_item->approved_quantity * $approved_service_item->approved_rate;
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= $approved_service_item->requisition_service_item()->description ?></td>
            <td><?= $approved_service_item->approved_quantity ?></td>
            <td><?= $approved_service_item->requisition_service_item()->measurement_unit()->symbol ?></td>
            <td style="text-align: right"><?= number_format($approved_service_item->approved_rate,2) ?></td>
            <td style="text-align: right"><?= number_format($amount,2) ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
    <tfoot>
        <tr>
            <th style="text-align: right" colspan="5">TOTAL</th>
            <th style="text-align: right"><?= number_format($total_amount,2) ?></th>
        </tr>

        <?php
        if(!is_null($imprest_voucher->vat_inclusive)){
            if($imprest_voucher->vat_inclusive == 'VAT PRICED'){
                $total_amount_vat_exclusive = $total_amount/1.18;
                $vat_amount = $total_amount - $total_amount_vat_exclusive;
            } else {
                $vat_amount = $total_amount*0.18;
            }
            ?>
            <tr>
                <th style="text-align: right"  colspan="5">VAT </th>
                <th style="text-align: right"><?= number_format($vat_amount,2) ?></th>
            </tr>

            <?php
            $grand_total = $total_amount + $vat_amount;
            ?>

            <tr  style="background-color: #dfdfdf">
                <th style="text-align: right"  colspan="5">GRAND TOTAL</th>
                <th style="text-align: right"><?= number_format($grand_total,2) ?></th>
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
        <td style=" vertical-align: top">
            <strong>Remarks: </strong><br/><?= $imprest_voucher->remarks != null ? $imprest_voucher->remarks : 'N/A' ?>
        </td>
    </tr>
</table>
