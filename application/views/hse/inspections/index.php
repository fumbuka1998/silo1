<?php
?>
<?php $this->load->view('includes/header'); ?>
    <section class="content-header">
        <h1>
            HSE | Inspections |
            <small><?= hse_inspection_categories($category_id)->name ?></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li class="active">Inspections</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools pull-right">
                            <button data-toggle="modal" data-target="#inspection_form" class="btn btn-default btn-xs">
                                <i class="fa fa-plus"></i> New Inspection
                            </button>
                            <div id="inspection_form" class="modal fade" role="dialog">
                                <?php $this->load->view('hse/inspections/inspection_form');?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="inspections_list" category_id = "<?= $category_id ?>" class="table table-bordered table-hover" style="table-layout: fixed;">
                                <thead>
                                <tr>
                                    <th style="width: 10%">Inspection Date</th><th style="width: 20%">Site/Project</th><th style="width: 15%">Inspector</th><th>Description</th><th style="width: 20%"></th>
                                </tr>
                                </thead>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');
