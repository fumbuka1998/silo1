<?php
/**
 * Created by PhpStorm.
 * User: Munyaki
 * Date: 03-Oct-17
 * Time: 4:45 PM
 */

$this->load->view('includes/letterhead');
?>

<h2 style="text-align:center; font-size: 14px">MATERIAL COST CENTER ASSIGNMENT</h2>
<table style="font-size: 11px; width: 100%">
    <tr>
        <td style=" vertical-align: top"><b>MCA No: </b><br> <?=$material_cost_center_assignments->assignment_number() ?></td>

        <td style="vertical-align: top"><b>From: </b><br><?=$material_cost_center_assignments->source_project()->project_name ?></td>

        <td style=" vertical-align: top"><b>To:</b> <br><?=$material_cost_center_assignments->destination_project()->project_name ?></td>

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

        $material_items = $assigned_item->stock();
        $item = $material_items->material_item();

        $sn++;

        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?=$material_items->sub_location()->sub_location_name ?></td>
            <td><?= $item->item_name ?></td>
            <td style="text-align: center"><?= $item->unit()->symbol ?></td>
            <td style="text-align: right"><?= $material_items->quantity ?></td>
            <td><?= $material_items->description ?></td>
        </tr>
        <?php

    }
    ?>
    </tbody>
</table>
<hr/><br/>
<table style="font-size: 11px;" width="100%">
    <tr>
        <td  style=" width:50%; vertical-align: top">
            <strong>Assigned By: </strong><?= $material_cost_center_assignments->employee()->full_name() ?>
        </td>
        <td  style=" width:50%; vertical-align: top">
            <strong>Assigning Date: </strong><?= custom_standard_date($material_cost_center_assignments->assignment_date) ?>
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