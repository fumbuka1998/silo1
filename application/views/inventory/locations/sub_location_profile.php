<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/15/2016
 * Time: 1:38 AM
 */

$sub_location_id = $sub_location->{$sub_location::DB_TABLE_PK};
?>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a class="sub_location_material_stock_activator" href="#sub_location_material_stock_<?= $sub_location_id ?>" data-toggle="tab">Material Stock</a></li>
        <li><a class="sub_location_tools_and_equipment_stock_activator" href="#sub_location_tools_and_equipment_stock_<?= $sub_location_id ?>" data-toggle="tab">Assets Stock</a></li>
    </ul>
    <div class="tab-content">
        <div class="active tab-pane" id="sub_location_material_stock_<?= $sub_location_id ?>">
            <?php $this->load->view('inventory/material/sub_location_material_tab'); ?>
        </div>
        <div class="tab-pane" id="sub_location_tools_and_equipment_stock_<?= $sub_location_id ?>">
            <?php $this->load->view('inventory/assets/sub_location_assets_tab'); ?>
        </div>
    </div>
</div>
