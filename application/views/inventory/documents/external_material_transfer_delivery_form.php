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
<table cellspacing="0" cellpadding="6px" style="font-size: 14px" width="100%">
    <tr>
        <th colspan="2"><h2>DELIVERY NOTE</h2><hr/></th>
    </tr>
    <tr>
        <td>
            <b>No : </b><?= $transfer->transfer_number() ?>
        </td>
        <td>
            <b>Date : </b><?= custom_standard_date($transfer->transfer_date) ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <b>M/s. : </b><?= $transfer->project()->project_name ?>
        </td>
    </tr>
</table>
<br/>
<table style="font-size: 12px" width="100%" border="1" cellspacing="0">
    <thead>
        <tr>
            <th>SN</th><th>Particulars</th><th>Unit</th><th>Quantity</th><th>Remarks</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $sn = 0;
        $material_items = $transfer->material_items();
        foreach($material_items as $item){
            $sn++;
            $material = $item->material_item();
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $material->item_name ?></td>
                <td><?= $material->unit()->symbol ?></td>
                <td style="text-align: right"><?= round($item->quantity,3) ?></td>
                <td><?= $item->remarks ?></td>
            </tr>
            <?php
        }

        $asset_items = $transfer->asset_items();
        foreach($asset_items as $item){
            $sn++;
            $source_history = $item->asset_sub_location_history();
            $asset = $source_history->asset();
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $asset->asset_code() ?></td>
                <td colspan="2"></td>
                <td><?= $item->remarks ?></td>
            </tr>
            <?php
        }
    ?>
    </tbody>
</table>
<table style="margin-top: 40%" border="1" cellspacing="0" cellpadding="6px" width="100%">
    <tr>
        <td style="vertical-align: top">
            Vehicle Number: <br/><?= $transfer->vehicle_number ?>
        </td>
        <td style="vertical-align: top">
            Driver Name: <br/> <?= $transfer->driver_name ?>
        </td>
    </tr>
    <tr>
        <td style="width: 50%; vertical-align: top">
            Received above goods in good order and condition.<br/><br/>
            Received By<br/>
            (Full Name):
        </td>
        <td style="width: 50%; vertical-align: top">
            Company Official Rubber Stamp
        </td>
    </tr>
    <tr>
        <td colspan="2">Remarks<br/><br/><br/><br/><?= $transfer->comments ?></td>
    </tr>
    <tr>
        <td>
            Requested By:<br/><br/>
        </td>
        <td>
            Issued By:<br/><br/>
        </td>
    </tr>
</table>
