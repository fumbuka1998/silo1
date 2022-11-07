<?php
/**
 * Created by PhpStorm.
 * User: Munyaki
 * Date: 02-Oct-17
 * Time: 1:02 PM
 */


$this->load->view('includes/letterhead');
?>


<h2 style="text-align:center; font-size: 15px">STOCK DISPOSAL ITEMS</h2>
<table style="width: 100%; font-size: 12px">
    <tr>
        <td><b>Disposed From: </b><br/> <?=$material_disposal->location()->location_name ?></td>

        <td><b>Project: </b><br/><?=$material_disposal->project()->project_name ?></td>

    </tr>
</table>
<br>
<table style="font-size: 10px" width="100%" border="1" cellspacing="0">
    <thead>
    <tr style="background: #cdcdcd; color: #ed1c24; ">
        <th style="width: 5%">SN</th><th width="20%">Sub_location</th><th width="20%">Material/Item</th><th style="width: 10%">Unit</th>
        <th style="width: 10%">Quantity</th><th style="width: 10%">Rate</th><th style="width: 10%">Remarks</th>
    </tr>

    </thead>
    <tbody>
    <?php

    $sn = 0;
    $disposed_material_items = $material_disposal->material_items();
    foreach ($disposed_material_items as $disposed_item){

        $sn++;
        $material_item = $disposed_item->material_item();
        ?>

        <tr>
            <td><?= $sn ?></td>
            <td><?= $disposed_item->sub_location()->sub_location_name ?></td>
            <td><?= $material_item->item_name ?></td>
            <td><?= $material_item->unit()->symbol ?></td>
            <td style="text-align: center"><?= $disposed_item->quantity ?></td>
            <td style="text-align: right"><?= $disposed_item->rate ?></td>
            <td><?= $disposed_item->remarks ?></td>

        </tr>
        <?php
    }

    $disposed_stock_items = $material_disposal->stock_items();
    foreach ($disposed_stock_items as $disposed_item){

        $sn++;
        $history = $disposed_item->asset_sub_location_history();
        $asset = $history->asset();
        ?>

        <tr>
            <td><?= $sn ?></td>
            <td><?= $history->sub_location()->sub_location_name ?></td>
            <td><?= $asset->asset_code() ?></td>
            <td colspan="3"></td>
            <td><?= $disposed_item->remarks ?></td>

        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
<hr/><br/>
<table style="font-size: 12px;" width="100%">
    <tr>
        <td  style=" width:50%; vertical-align: top">
            <strong>Disposed By: </strong><?=$material_disposal->employee()->full_name() ?>
        </td>
        <td  style=" width:50%; vertical-align: top">
            <strong>Disposal Date: </strong><?= custom_standard_date($material_disposal->disposal_date) ?>
        </td>
    </tr>
</table>




