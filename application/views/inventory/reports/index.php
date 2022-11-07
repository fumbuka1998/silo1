<?php
/**
 * Created by PhpStorm.
 * User: bizytechlimited
 * Date: 23/10/2018
 * Time: 08:52
 */

$this->load->view('includes/header');
$month_string = explode('-',date('Y-m-d'))[1] - 1 > 0 ? explode('-',date('Y-m-d'))[1] - 1 : 12;
$privious_month = explode('-', date('Y-m-d'))[0].'-'.add_leading_zeros($month_string,2).'-'.explode('-',date('Y-m-d'))[2];

/** @var TYPE_NAME $selected_report */
switch ($selected_report){
    case 'material_item_availability':
        $report_name = 'Material Item Availability';
        break;
    case 'inventory_sales':
        $report_name = 'Inventory Sales';
        break;
    case 'cost_center_assignements':
        $report_name = 'Cost Center Assignment';
        break;
}

?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Inventory Reports
            <small><?= $report_name ?></small>

        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('inventory')?>"><i class="fa fa-barcode"></i>Inventory</a></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box" id="location_reports">
                    <div class="box-header with-border">
                        <div class="col-xs-12">
                            <div class="box-tools">
                                <form method="post" target="_blank" action="<?= base_url('inventory/inventory_reports') ?>">

                                        <input name="print" type="hidden" value="true">
                                        <input name="report_type" hidden value="<?= $selected_report ?>">

                                    <div class="form-group col-md-2">
                                        <label for="material_id" class="control-label">Material Item</label>
                                        <?= /** @var TYPE_NAME $material_options */
                                        form_dropdown('material_id',$material_options,'',' class="form-control searchable"') ?>
                                    </div>
                                    <div style="display: none" class="form-group col-md-2">
                                        <label for="" class="control-label">Location</label>
                                        <?= form_dropdown('location_id',$location_options,'',' class="form-control searchable"') ?>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label for="" class="control-label">Sub Location</label>
                                        <?= form_dropdown('sub_location_id',[],'',' class="form-control searchable"') ?>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="" class="control-label">Source Project</label>
                                        <?php
                                        $project_options = ['all' => 'ALL'] + $project_options;
                                        $project_options[''] = 'UNASSIGNED';
                                        echo form_dropdown('source_id',$project_options,'all',' class="form-control searchable"');
                                        ?>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="" class="control-label">Destination Project</label>
                                        <?php
                                        echo form_dropdown('destination_id',$project_options,'all',' class="form-control searchable"');
                                        ?>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="" class="control-label">Client</label>
                                        <?php
                                        echo form_dropdown('client_id',$client_options,'all',' class="form-control searchable"');
                                        ?>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="" class="control-label">Project</label>
                                        <?php
                                        echo form_dropdown('project_id',$project_options,'all',' class="form-control searchable"');
                                        ?>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="" class="control-label">From</label>
                                        <input class="form-control datepicker" name="from" value="<?= $privious_month ?>">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="" class="control-label">To</label>
                                        <input class="form-control datepicker" name="to" value="<?= date('Y-m-d') ?>">
                                    </div>
                                    <div class="form-group col-md-2 pull-right">
                                        <br/>
                                        <button type="button" id="generate_inventory_report" class="btn btn-default btn-xs">
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
                            <div id="report_container" class="col-xs-12 table-responsive">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /.content -->
<?php $this->load->view('includes/footer'); ?>