<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/5/2016
 * Time: 9:35 AM
 */

    $this->load->view('includes/letterhead');
    $source = $transfer->source();
    $destination = $transfer->destination();
?>
<h2 style="text-align: center">MATERIAL TRANSFER SHEET</h2>
<table style="font-size: 12px" width="100%">
    <tr>
        <td>
            <b>Transfer No : </b><?= $transfer->transfer_number() ?>
        </td>
        <td>
            <b>Project : </b><?= substr($transfer->project()->project_name,0,50) ?>
        </td>
    </tr>
    <tr>
        <td>
            <b>From : </b><?= $source->location_name ?>
        </td>
        <td>
            <b>To : </b><?= $destination->location_name ?>
        </td>
    </tr>
</table>

<br/><br/>

<table style="font-size: 12px" width="100%" border="1" cellspacing="0">
    <thead>
        <tr>
            <th>SN</th><th>Material/Item</th><th>Unit</th><th>Quantity</th><th>Price</th><th>Amount</th><th>Remarks</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $sn = 0;
        $material_items = $transfer->material_items();
        $total_amount = 0;
        foreach($material_items as $item){
            $sn++;
            $material = $item->material_item();
            $total_amount+= $amount = $item->quantity*$item->price;
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $material->item_name ?></td>
                <td><?= $material->unit()->symbol ?></td>
                <td style="text-align: right"><?= $item->quantity ?></td>
                <td style="text-align: right"><?= number_format($item->price,2) ?></td>
                <td style="text-align: right"><?= number_format($amount,2) ?></td>
                <td><?= $item->remarks ?></td>
            </tr>
            <?php
        }
        $asset_items = $transfer->asset_items();
        foreach($asset_items as $item){
            $sn++;
            $source_history = $item->asset_sub_location_history();
            $asset = $source_history->asset();
            $total_amount += $asset->book_value;
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $asset->asset_code() ?></td>
                <td colspan="3"></td>
                <td style="text-align: right"><?= number_format($asset->book_value,2) ?></td>
                <td><?= $item->remarks ?></td>
            </tr>
            <?php
        }
    ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5">TOTAL</th><th style="text-align: right"><?= number_format($total_amount, 2) ?></th><th></th>
        </tr>
    </tfoot>
</table>

<br/><br/>

<strong>Comments</strong><br/><?= $transfer->comments != '' ? nl2br($transfer->comments) : 'N/A' ?>

<br/><br/>

<table width="100%">
    <tr>
        <td style="width: 50%">
            <strong>Issued By: </strong><?= $transfer->sender()->full_name() ?>
        </td>
        <td style="width: 50%">
            <strong>Issued Date: </strong><?= custom_standard_date($transfer->transfer_date) ?>
        </td>
    </tr>
</table>
