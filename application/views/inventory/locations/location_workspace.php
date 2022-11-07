<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/7/2016
 * Time: 10:39 AM
 */
    $is_site_location = isset($project);
    $data['is_site_location'] = $is_site_location;
?>
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#location_material" data-toggle="tab">Material & Assets</a></li>
                <li><a href="#location_purchase_orders" data-toggle="tab">Purchase Orders</a></li>
                <?php if(check_privilege('Store Operations')){ ?><li><a href="#location_sales" data-toggle="tab">Sales</a></li><?php } ?>
                <li><a href="#location_grns" data-toggle="tab"><?= $is_site_location ? 'Delivery Notes' : 'GRNs' ?></a></li>
                <li><a href="#location_sub_locations" data-toggle="tab">Sub-Locations</a></li>
                <li><a href="#location_reports" data-toggle="tab">Reports</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane" id="location_purchase_orders">
                    <?php $this->load->view('inventory/locations/location_purchase_orders_tab'); ?>
                </div>
                <?php if(check_privilege('Store Operations')){ ?>
                <div class="tab-pane" id="location_sales">
                    <?php $this->load->view('inventory/locations/location_sales_tab'); ?>
                </div>
                <?php } ?>
                <div class="tab-pane" id="location_grns">
                    <?php $this->load->view('inventory/locations/grns_tab',$data); ?>
                </div>
                <div class="active  tab-pane" id="location_material">
                    <?php $this->load->view('inventory/locations/material_tab'); ?>
                </div>
                <div class="tab-pane" id="location_sub_locations">
                    <?php $this->load->view('inventory/locations/sub_location_tab'); ?>
                </div>
                <div class="tab-pane" id="location_reports">
                    <?php $this->load->view('inventory/reports/reports_tab'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
