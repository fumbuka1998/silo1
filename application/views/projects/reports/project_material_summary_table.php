<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 11/8/2016
 * Time: 11:11 AM
 */

if(!empty($material_items)) {
    $project_id = $project->{$project::DB_TABLE_PK};
    ?>

    <table border="1" cellspacing="0" style="font-size: 12px" width="100%">
        <thead>
        <tr>
            <th>Item</th><th>Unit</th>
            <th>Budgeted Quantity</th>
            <th>Requested Quantity</th>
            <th>Approved Quantity</th>
            <th>Ordered Quantity</th>
            <th>Delivered In All Stores</th>
            <th>Delivered to Site Store</th>
            <th>Used From Site Store</th>
            <th>Balance At Site Store</th>
        </tr>
        </thead>
        <tbody>
        <?php
         $bg = '#efefef';
        foreach ($material_items as $material_item) {
            ?>
            <tr style="background: <?= $bg ?>">
                <td><?= $material_item->item_name ?></td>
                <td><?= $material_item->unit()->symbol ?></td>
                <td style="text-align: right"><?= $material_item->budgeted_quantity_for_project($project_id) ?></td>
                <td style="text-align: right"><?= $material_item->requested_quantity_for_project($project_id) ?></td>
                <td style="text-align: right"><?= $material_item->approved_quantity_for_project($project_id) ?></td>
                <td style="text-align: right"><?= $material_item->ordered_quantity_for_project($project_id) ?></td>
                <td style="text-align: right"><?= $material_item->delivered_quantity_in_all_stores_for_project($project_id) ?></td>
                <td style="text-align: right"><?= $material_item->delivered_quantity_in_site_store_for_project($project_id) ?></td>
                <td style="text-align: right"><?= $material_item->used_quantity_from_site_store_for_project($project_id) ?></td>
                <td style="text-align: right"><?= $material_item->location_stock($project_id, $location_id) ?></td>
            </tr>
            <?php
            $bg = $bg == '#efefef' ? '#ffffff' : '#efefef';
        }
        ?>
        </tbody>
    </table>
    <?php

} else {
    ?>
    <div style="padding: 5px; text-align: center; width: 100%;">Nothing to show</div>
    <?php
}
