<?php
/**
 * Created by PhpStorm.
 * User: Munyaki
 * Date: 03-Oct-17
 * Time: 4:45 PM
 */

$this->load->view('includes/letterhead');
?>

<h2 style="text-align:center; font-size: 14px">COST CENTER ASSIGNMENT</h2>
<table style="font-size: 11px; width: 100%">
    <tr>
        <td style=" vertical-align: top"><b>CA No: </b><br> <?=$cost_center_assignment->assignment_number() ?></td>

        <td style="vertical-align: top"><b>From: </b><br><?=$cost_center_assignment->source_project()->project_name ?></td>

        <td style=" vertical-align: top"><b>To:</b> <br><?=$cost_center_assignment->destination_project()->project_name ?></td>

    </tr>
</table>
<br>
<table style="font-size: 11px" width="100%" border="1" cellspacing="0">
    <thead>
    <tr style="background: #cdcdcd; color: #ed1c24; ">
        <th style="width: 5%">SN</th><th>Sub_location</th><th>Material/Item</th><th>Unit</th>
        <th>Quantity</th><th>Remarks</th>
    </tr>

    </thead>
    <tbody>
    <?php

    $sn = 0;


    foreach ($cost_center_assigned_items as $assigned_item){

        if($assignment_type == "material") {
            $material_stock = $assigned_item->stock();
            $item = $material_stock->material_item();

            $sn++;

            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $material_stock->sub_location()->sub_location_name ?></td>
                <td><?= $item->item_name ?></td>
                <td style="text-align: center"><?= $item->unit()->symbol ?></td>
                <td style="text-align: right"><?= $material_stock->quantity ?></td>
                <td><?= $material_stock->description ?></td>
            </tr>
            <?php


        } else {
            $item_name = $assigned_item->asset_sub_location_history()->asset()->asset_code();
            $sub_location = $assigned_item->asset_sub_location_history()->sub_location();
            $sn++;

            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $sub_location->sub_location_name ?></td>
                <td><?= $item_name ?></td>
                <td style="text-align: center">No.</td>
                <td style="text-align: right"> 1 </td>
                <td><?= $assigned_item->asset_sub_location_history()->description ?></td>
            </tr>
            <?php

        }

    }
    ?>
    </tbody>
</table>
<hr/><br/>
<table style="font-size: 11px;" width="100%">
    <tr>
        <td  style=" width:50%; vertical-align: top">
            <strong>Assigned By: </strong><?= $cost_center_assignment->employee()->full_name() ?>
        </td>
        <td  style=" width:50%; vertical-align: top">
            <strong>Assigning Date: </strong><?= custom_standard_date($cost_center_assignment->assignment_date) ?>
        </td>
    </tr>
    <tr>
        <td style="width: 50%"><br/>
            <strong>Signature: </strong>
            <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span>
        </td>
    </tr>
</table>