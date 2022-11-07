<?php $this->load->view('includes/header');?>
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Reports
                <small>Projects Inventory Movement</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
                <li><a href="<?= base_url('reports')?>"><i class="fa fa-pie-chart"></i>Reports</a></li>
                <li class="active">Projects Inventory Movement</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border no-print">
                            <div class="col-xs-12">
                                <div class="box-tools">
                                    <form method="post" target="_blank" action="<?= base_url('reports/project_inventory_movement') ?>">
                                        <div class="form-group col-md-4">
                                            <label for="project_id" class="control-label">Project</label>
                                            <?= form_dropdown('project_id',$project_options,'',' class="form-control searchable" required ') ?>
                                            <input name="print" value="true" type="hidden">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="from" class="control-label">As of</label>
                                            <input type="text" class="form-control datepicker" name="as_of" value="<?= date('Y-m-d') ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <br/>
                                            <button id="generate_project_inventory_movement_report" type="button" class="btn btn-default btn-xs">Generate</button>
                                            <button class="btn btn-default btn-xs">
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