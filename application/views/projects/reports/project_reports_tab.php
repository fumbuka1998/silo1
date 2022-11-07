<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 11/8/2016
 * Time: 10:36 AM
 */

$report_type_options = [

    'cost_tracking_worksheet' => 'Cost Tracking Worksheet',
    'budget_summary' => 'Budget Summary',
    'material_tracing_report' => 'Material Tracing Report',
    'requisition_report' => 'Requisition Report',
    'purchase_orders_report' => 'Purchase Orders Report',
    'grns_report' => 'GRNs Report',
    'approved_payments_report' => 'Approved Payments Report',
    'payments_report' => 'Payments Report',
    'projects_inventory_position' => 'Inventory Position',
    'projects_statement' => 'Account Statement',
    'fuel_consumption' => 'Fuel Consumption'
];
?>
<div class="box">
    <div class="box-header">
        <div class="col-xs-12">
            <div class="box-tools col-xs-12">
                <form method="post"  id="project_reports_form" target="_blank" action="<?= base_url('projects/reports') ?>">
                    <div class="form-group col-md-3">
                        <label for="" class="control-label">Report Type</label>
                        <?= form_dropdown('report_type',$report_type_options,'',' class="form-control"') ?>
                        <input name="project_id" type="hidden" value="<?= $project->{$project::DB_TABLE_PK} ?>">
                        <input name="print" type="hidden" value="true">
                    </div>
                    <div class="form-group col-md-3" style="display: none;">
                        <label for="" class="control-label">Equipments(Sub Location)</label>
                        <?= form_multiselect('sub_location_ids[]', $equipment_sub_location_options, '', ' class="sub_location_ids form-control searchable" ') ?>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="" class="control-label">From</label>
                        <input class="form-control datepicker" name="from" value="<?= $project->start_date ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="" class="control-label">To</label>
                        <input class="form-control datepicker" name="to" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <br/>
                        <button type="button" id="generate_project_report" class="btn btn-default btn-xs">
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
            <div id="chart_container" class="col-xs-12">

            </div>
            <div id="project_report_container" class="col-xs-12 table-responsive">

            </div>
        </div>
    </div>
</div>