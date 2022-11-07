<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 11/8/2016
 * Time: 11:11 AM
 */

if(!empty($material_items)) {

    ?>

    <table border="1" cellspacing="0" style="font-size: 10px" width="100%">
        <thead>
        <tr>
            <th rowspan="2">S/N</th>
            <th rowspan="2">Item</th><th rowspan="2">Unit</th>
            <th rowspan="2">Budgeted Quantity</th>
            <th rowspan="2">Opening Stock</th>
            <th colspan="2">Requisitions</th>
            <th colspan="6">Sources</th>
            <th colspan="7">Material Movement</th>
        </tr>
        <tr>
            <th>Requested</th>
            <th>Approved</th>
            <th>Ordered</th>
            <th>Received From Orders</th>
            <th>Cash Purchased</th>
            <th>Assigned In</th>
            <th>Assigned Out</th>
            <th>Total</th>
            <th>Balance At Main Store</th>
            <th>On Transit</th>
            <th>Used From Site Store</th>
            <th>Balance At Site Store</th>
            <th>Total</th>
            <th>Rate</th>
            <th>Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $bg = '#efefef';
        $sn = $total_value = 0;
        $sub_location_ids = 'SELECT sub_location_id FROM sub_locations WHERE location_id IN(1,'.$site_location_id.')';
        foreach ($material_items as $material_item) {
            $movement_row_total = $received_row_total = 0;
            $received_row_total += $opening_stock = $material_item->sub_location_opening_quantity($sub_location_ids,$project_id);
            $received_row_total += $received_from_orders = $material_item->received_quantity_from_orders($project_id);
            $received_row_total += $received_from_cash = $material_item->received_from_cash_purchase($sub_location_ids,$project_id);
            $received_row_total += $assigned_in = $material_item->sub_location_assigned_in_quantity($sub_location_ids, $project_id);
            $received_row_total -= $assigned_out = $material_item->sub_location_assigned_out_quantity($sub_location_ids, $project_id);
            $movement_row_total += $used = $material_item->used_quantity_from_site_store_for_project($project_id);
            $movement_row_total += $main_store_balance = $material_item->location_balance($project_id, 1);
            $movement_row_total += $on_transit = $material_item->sub_location_on_transit_quantity($sub_location_ids,$project_id);
            $movement_row_total += $site_balance = $material_item->location_balance($project_id, $site_location_id);
            if($movement_row_total > 0) {
                $price = $material_item->last_average_price($project_id);
                $total_value += $price*$movement_row_total;
                $sn++;
                ?>
                <tr style="background: <?= $bg ?>">
                    <td><?= $sn ?></td>
                    <td><?= $material_item->item_name ?></td>
                    <td><?= $material_item->unit()->symbol ?></td>
                    <td style="text-align: right"><?= $material_item->budgeted_quantity_for_project($project_id) ?></td>
                    <td style="text-align: right"><?= $opening_stock ?></td>
                    <td style="text-align: right"><?= $material_item->requested_quantity_for_project($project_id) ?></td>
                    <td style="text-align: right"><?= $material_item->approved_quantity_for_project($project_id) ?></td>
                    <td style="text-align: right"><?= $material_item->ordered_quantity_for_project($project_id) ?></td>
                    <td style="text-align: right"><?= $received_from_orders ?></td>
                    <td style="text-align: right"><?= $received_from_cash ?></td>
                    <td style="text-align: right"><?= $assigned_in ?></td>
                    <td style="text-align: right"><?= $assigned_out ?></td>
                    <td style="text-align: right"><?= $received_row_total ?></td>
                    <td style="text-align: right"><?= $main_store_balance ?></td>
                    <td style="text-align: right"><?= $on_transit ?></td>
                    <td style="text-align: right"><?= $used ?></td>
                    <td style="text-align: right"><?= $site_balance ?></td>
                    <td style="text-align: right"><?= $movement_row_total ?></td>
                    <td style="text-align: right"><?= number_format($price,2) ?></td>
                    <td style="text-align: right"><?= number_format(($movement_row_total*$price),2) ?></td>
                </tr>
                <?php
                $bg = $bg == '#efefef' ? '#ffffff' : '#efefef';
            }
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="19">TOTAL</th><th style="text-align: right"><?= number_format($total_value,2) ?></th>
        </tr>
        </tfoot>
    </table>
    <?php

} else {
    ?>
    <div style="padding: 5px; text-align: center; width: 100%;">Nothing to show</div>
    <?php
}
