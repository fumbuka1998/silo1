<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/6/2016
 * Time: 6:31 PM
 */

    $this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">INTERNAL TRANSFER</h2>
<table width="100%">
    <tr>
        <td style="width: 40%">
            <b>Location : </b><?= $transfer->location()->location_name ?>
        </td>
        <td style="width: 40%">
            <b>Transfer No : </b><?= $transfer->transfer_number() ?>
        </td>
    </tr>
</table>
<br/>
<table style="font-size: 12px" width="100%" cellspacing="0" border="1">
    <thead>
        <tr>
            <th>SN</th><th>Material/Asset</th><th>Unit</th><th>Quantity</th><th>From</th><th>To</th><th>Remarks</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $sn = 0;
        $material_items = $transfer->material_items();
        foreach($material_items as $item){
            $sn++;
            $stock = $item->stock();
            $material = $stock->material_item();
    ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $material->item_name ?></td>
                <td><?= $material->unit()->symbol ?></td>
                <td style="text-align: right"><?= $stock->quantity ?></td>
                <td><?= $item->source_sub_location()->sub_location_name ?></td>
                <td><?= $stock->sub_location()->sub_location_name ?></td>
                <td><?= $item->remarks ?></td>
            </tr>
    <?php
        }

        $asset_items = $transfer->asset_items();
        foreach($asset_items as $item){
            $sn++;
            $sub_location_history = $item->asset_sub_location_history();
            $asset = $sub_location_history->asset();
    ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $asset->asset_code() ?></td>
                <td colspan="2"></td>
                <td><?= $item->source_sub_location()->sub_location_name ?></td>
                <td><?= $sub_location_history->sub_location()->sub_location_name ?></td>
                <td><?= $item->remarks ?></td>
            </tr>
    <?php
        }
    ?>
    </tbody>
</table>
<br/>
<strong>Comments</strong><br/>
<?= $transfer->comments != '' ? $transfer->comments : 'N/A' ?>
<br/><br/>

<table width="100%">
    <tr>
        <td colspan="2">
            <strong>Issued Date: </strong><?= custom_standard_date($transfer->transfer_date) ?>
        </td>
    </tr>
    <tr>
        <td style="width: 50%">
            <strong>Issued By: </strong><?= $transfer->employee()->full_name() ?>
        </td>
        <td style="width: 50%">
            <strong>Received By: </strong><?= ucwords($transfer->receiver) ?>
        </td>
    </tr>
</table>
