<?php
/**
 * Created by PhpStorm.
 * User: Munyaki
 * Date: 28-Sep-17
 * Time: 2:37 PM
 */

$this->load->view('includes/letterhead');
?>

<h2 style="text-align:center;">MATERIAL TRANSFER ORDER</h2>
<table style="width: 100%">
    <tr>
       <td style="width: 50%;font-size: 15px;"><b>Source: </b> <?=$location->location_name ?></td>

        <td style="font-size: 15px;"><b>Destination: </b><?=$requisition_approval->requisition()->cost_center_name() ?></td>
    </tr>
</table>
<br>
<table style="font-size: 10px" width="100%" border="1" cellspacing="0">
    <thead>
    <tr style="background: #cdcdcd; color: #ed1c24; ">
        <th style="width: 5%">SN</th><th width="20%">Material/Item</th><th style="width: 10%">Unit</th>
        <th style="width: 10%">Quantity</th>
    </tr>

    </thead>
    <tbody>
    <?php

    $sn = 0;
    foreach ($transfer_order_items as $item){
        $approval_item=$item['approval_item'];
        $material_item=$item['material_item'];

        $sn++;

           ?>

        <tr>
            <td><?= $sn ?></td>
            <td><?= $material_item->item_name ?></td>
            <td><?= $material_item->unit()->symbol ?></td>
            <td style="text-align: right"><?= $approval_item->approved_quantity ?></td>

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
            <strong>Ordered By: </strong><?=$requisition_approval->created_by()->full_name() ?>
        </td>
        <td  style=" width:50%; vertical-align: top">
            <strong>Order Date: </strong><?= custom_standard_date($requisition_approval->approved_date) ?>
        </td>
    </tr>
</table>




