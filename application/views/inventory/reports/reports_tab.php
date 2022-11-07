<?php

/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 11/8/2016
 * Time: 10:36 AM
 */

$report_type_options = [
    'location_material_balance' => 'Material Balance',
    'location_material_movement' => 'Material Movement',
    'location_material_item_movement' => 'Material Item Movement',
    'location_material_item_availability' => 'Material Item Availability',
    'location_material_disposal' => 'Material Disposal',
    'location_asset_stock' => 'Asset Stock',
    'location_asset_movement' => 'Asset Movement'
];
?>
<div class="box">
    <div class="box-header">
        <div class="col-xs-12">
            <div class="box-tools col-xs-12">
                <form method="post" target="_blank" action="<?= base_url('inventory/location_reports') ?>">
                    <div class="form-group col-md-2">
                        <label for="" class="control-label">Report Type</label>
                        <?= form_dropdown('report_type', $report_type_options, '', ' class="form-control"') ?>
                        <input name="location_id" type="hidden" value="<?= $location->{$location::DB_TABLE_PK} ?>">
                        <input name="print" type="hidden" value="true">
                    </div>
                    <div style="display: none" class="form-group col-md-2">
                        <label for="material_id" class="control-label">Material Item</label>
                        <?= form_dropdown('material_id', [], '', ' class="form-control searchable"') ?>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="category_id" class="control-label">Material Category</label>
                        <?= form_dropdown('category_id', $material_item_category_options, '', ' class="form-control searchable"') ?>
                    </div>
                    <div style="display: none" class="form-group col-md-2">
                        <label for="asset_group_id" class="control-label">Category</label>
                        <?= form_dropdown('asset_group_id', $asset_group_options, '', ' class="form-control searchable"') ?>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="" class="control-label">Sub Location</label>
                        <?php
                        $sub_location_options = ['all_sub_locationwise' => 'ALL(Sub Locationwise)'] + $sub_location_options;
                        echo form_dropdown('sub_location_id', $sub_location_options, '', ' class="form-control searchable"') ?>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="" class="control-label">Project</label>
                        <?php
                        $project_options = ['all' => 'ALL'] + $project_options = ['all_projectwise' => 'ALL(Projectwise)'] + $project_options;
                        $project_options[''] = 'UNASSIGNED';
                        echo form_dropdown('project_id', $project_options, 'all', ' class="form-control searchable"');
                        ?>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="" class="control-label">From</label>
                        <input class="form-control datepicker" name="from" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="" class="control-label">To</label>
                        <input class="form-control datepicker" name="to" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group col-md-2 pull-right">
                        <br />
                        <button type="button" id="generate_location_report" class="btn btn-default btn-xs">
                            Generate
                        </button>
                        <button name="pdf" value="true" class="btn btn-default btn-xs">
                            <i class="fa fa-file-pdf-o"></i> PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div id="location_report_container" class="col-xs-12 table-responsive">

            </div>
        </div>
    </div>
</div>