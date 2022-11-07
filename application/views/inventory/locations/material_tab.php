<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/1/2016
 * Time: 2:42 AM
 */
?>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#location_material_stock" data-toggle="tab">Material Stock</a></li>
        <li><a href="#location_assets_stock" data-toggle="tab">Assets Stock</a></li>
        <?php if(check_privilege('Store Operations')){ ?>
        <li><a href="#location_assets_handovers" data-toggle="tab">Assets Handovers</a></li>
            <?php if(is_null($location->project_id)){ ?>
            <li><a href="#location_transfer_orders" data-toggle="tab">Transfer Orders</a></li>
            <?php } ?>
        <li><a href="#location_material_transfers" data-toggle="tab">Transfers</a></li>
        <li><a href="#material_cost_center_assignment" data-toggle="tab">Cost Center Assignment</a></li>
        <li><a href="#location_material_disposals" data-toggle="tab">Material Disposal</a></li>
        <?php } ?>

    </ul>
    <div class="tab-content">
        <div class="active tab-pane" id="location_material_stock">
            <?php $this->load->view('inventory/material/location_material_stock_tab'); ?>
        </div>
        <div class=" tab-pane" id="location_assets_stock">
            <?php $this->load->view('inventory/assets/location_assets_stock_tab'); ?>
        </div>
        <?php if(check_privilege('Store Operations')){ ?>
        <div class=" tab-pane" id="location_assets_handovers">
            <?php $this->load->view('inventory/assets/location_assets_handovers_tab'); ?>
        </div>
        <div class=" tab-pane" id="location_material_transfers">
            <?php $this->load->view('inventory/material/transfers/location_material_transfer_tab'); ?>
        </div>
        <?php if(is_null($location->project_id)){ ?>
        <div class="tab-pane" id="location_transfer_orders">
            <?php $this->load->view('inventory/material/transfer_orders/location_transfer_orders_tab'); ?>
        </div>
        <?php } ?>
        <div class="tab-pane" id="material_cost_center_assignment">
            <?php $this->load->view('inventory/cost_center_assignment/cost_center_assignment_tab'); ?>
        </div>
        <div class="tab-pane" id="location_material_disposals">
            <?php $this->load->view('inventory/material/disposals/material_disposal_tab'); ?>
        </div>
        <?php } ?>
    </div>
</div>
