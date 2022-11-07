<?php $this->load->view('includes/header');?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Reports
            <small>Projects Performance</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('reports')?>"><i class="fa fa-pie-chart"></i>Reports</a></li>
            <li class="active">Project Performance</li>
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
                                <form method="post" target="_blank">
                                    <div class="form-group col-md-4">
                                        <label for="project_id" class="control-label">Project</label>
                                        <?= form_dropdown('project_id',$project_options,'',' class="form-control searchable" ') ?>
                                        <input name="triggered" value="true" type="hidden">
                                    </div>
                                    <div class="col-md-3">
                                        <br/>
                                        <button id="generate_project_performance_report" type="button" class="btn btn-default btn-xs">Generate</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">

                            <div id="chart_container" class="col-xs-12">

                            </div>
                            <div class="col-xs-12">
                                <hr/><br/>
                            </div>
                            <div id="report_container" class="col-xs-12 table-responsive">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /.content -->
<?php $this->load->view('includes/footer'); ?>