<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 9/7/2018
 * Time: 11:47 AM
 */

$this->load->view('includes/letterhead');
$currency = $retirement->imprest_voucher()->currency();
?>
<h2 style="text-align: center">RETIREMENT SHEET</h2>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td style=" width: 30%">
            <strong>Retirement Date: </strong><?= custom_standard_date($retirement->retirement_date) ?>
        </td>
        <td style=" width: 30%">
            <strong>Retirement No.: </strong><?= $retirement->imprest_voucher_retirement_number() ?>
        </td>
    </tr>
    <tr>
        <td style=" width: 30%">
            <strong>Retired From : </strong><?= $imprest_voucher->debit_account()->account_name ?>
        </td>
        <td style=" width: 30%">
            <strong>Reference : </strong><?= $requisition->requisition_number() ?>
        </td>
    </tr>

</table>
<br/>
<table style="font-size: 10px" width="100%" cellspacing="0" border="1">
    <thead>
    <tr>
        <th>S.No</th>
        <th>Description</th>
        <th>Unit</th>
        <th>Quantity</th>
        <th nowrap="true">Rate</th>
        <th nowrap="true">Amount</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sn = $total_amount = 0;
    $retired_material_items = $retirement->retired_material_items();
    if(!empty($retired_material_items)) {
        foreach ($retired_material_items as $retired_material_item) {
            $sn++;
            $total_amount += $amount = $retired_material_item->rate * $retired_material_item->quantity;
            ?>
            <tr>
                <td class="bordered"><?= $sn ?></td>
                <td class="bordered"><?= $retired_material_item->material_item()->item_name ?></td>
                <td class="bordered"><?= $retired_material_item->material_item()->unit()->symbol ?></td>
                <td class="bordered"
                    style="text-align: right"><?= $retired_material_item->quantity ?></td>
                <td class="right_bordered"
                    style="text-align: right"><?= number_format($retired_material_item->rate, 2) ?></td>
                <td class="right_bordered"
                    style="text-align: right"><?= $currency->symbol . ' ' . number_format($amount, 2) ?></td>
            </tr>
            <?php
        }
    }
    $retired_asset_items = $retirement->retired_asset_items();
    if(!empty($retired_asset_items)) {
        foreach ($retired_asset_items as $retired_asset_item) {
            $sn++;
            $total_amount += $amount = $retired_asset_item->book_value * $retired_asset_item->quantity;
            ?>
            <tr>
                <td class="bordered"><?= $sn ?></td>
                <td class="bordered"><?= $retired_asset_item->asset_item()->asset_name ?></td>
                <td class="bordered">No.</td>
                <td class="bordered"
                    style="text-align: right"><?= $retired_asset_item->quantity ?></td>
                <td class="right_bordered"
                    style="text-align: right"><?= number_format($retired_asset_item->book_value, 2) ?></td>
                <td class="right_bordered"
                    style="text-align: right"><?= $currency->symbol . ' ' . number_format($amount, 2) ?></td>
            </tr>
            <?php
        }
    }
    $retired_cash_items = $retirement->retired_cash();
    if(!empty($retired_cash_items)) {
        foreach ($retired_cash_items as $cash_item) {
            $sn++;
            $total_amount += $amount = $cash_item->rate * $cash_item->quantity;
            ?>
            <tr>
                <td class="bordered"><?= $sn ?></td>
                <td class="bordered"><?= $cash_item->description ?></td>
                <td class="bordered">No.</td>
                <td class="bordered" style="text-align: right"><?= $cash_item->quantity ?></td>
                <td class="right_bordered" style="text-align: right"><?= number_format($cash_item->rate, 2) ?></td>
                <td class="right_bordered" style="text-align: right"><?= $currency->symbol . ' ' . number_format($amount, 2) ?></td>
            </tr>
            <?php
        }
    }

    $retired_service_items = $retirement->retired_services();
    if(!empty($retired_service_items)){
        foreach ($retired_service_items as $service_item){
            $sn++;
            $total_amount += $amount = $service_item->rate * $service_item->quantity;
            ?>
            <tr>
                <td class="bordered"><?= $sn ?></td>
                <td class="bordered"><?= $service_item->description ?></td>
                <td class="bordered">No.</td>
                <td class="bordered" style="text-align: right"><?= $service_item->quantity ?></td>
                <td class="right_bordered" style="text-align: right"><?= number_format($service_item->rate, 2) ?></td>
                <td class="right_bordered" style="text-align: right"><?= $currency->symbol . ' ' . number_format($amount, 2) ?></td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>

    <tfoot>
    <tr>
        <td></td>
        <th style="text-align: right" colspan="4">TOTAL</th>
        <th style="text-align: right" class="total_amount_display"><?= $currency->symbol.' '.number_format($total_amount,2) ?></th>
    </tr>
    <?php
    $grand_total = $total_amount;
    if(!is_null($retirement->vat_inclusive)){
        if($retirement->vat_inclusive == 'VAT PRICED'){
            $total_amount_vat_exclusive = $total_amount/1.18;
            $vat_amount = $total_amount - $total_amount_vat_exclusive;
        } else {
            $vat_amount = $total_amount*0.18;
        }
        ?>
        <tr>
            <td></td>
            <th style="text-align: right" colspan="4">VAT </th>
            <th style="text-align: right"><?=  $currency->symbol.' '.number_format($vat_amount,2) ?></th>
        </tr>

        <?php
        $grand_total = $total_amount + $vat_amount;
        ?>

        <tr  style="background-color: #dfdfdf">
            <td></td>
            <th style="text-align: right" colspan="4">GRAND TOTAL</th>
            <th style="text-align: right"><?=  $currency->symbol.' '.number_format($grand_total,2) ?></th>
        </tr>
    <?php } ?>
    </tfoot>
</table><br/>
<strong>Amount In Words: </strong><?= numbers_to_words($grand_total) ?>
<table style="font-size: 12px" width="100%">
    <tr>
        <td colspan="3"><hr/></td>
    </tr>
    <tr>
        <td style=" vertical-align: top">
            <strong>Remarks: </strong><br/><?= $retirement->remarks != null ? $imprest_voucher->remarks : 'N/A' ?>
        </td>
    </tr>
</table>
