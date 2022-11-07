<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Material Items List
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('inventory')?>"><i class="fa fa-barcode"></i>Inventory</a></li>
        <li class="active">Material Items</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <?php if(check_privilege('Inventory Actions')){ ?>
                        <form class="form-inline">
                            <div class="form-group">
                                <label for="activities_excel">Excel File:  </label>
                                <input type="file" name="material_registration_excel" class="form-control">
                                <button type="button" excel_type="budget" class="btn btn-default btn-sm upload_material_registration_excel">Upload Excel</button>
                            </div>
                        </form>
                        <?php } ?>
                        <hr/>
                        <form target="_blank" method="post" action=" <?= base_url('inventory/pdf_material_items') ?>">
                            <div class="form-group col-md-4">
                                <label class="col-md-2" for="category_id">Nature:  </label>
                                <div class="col-md-10">
                                <?= form_dropdown('project_nature_id',$category_nature_options,'',' id="filter_by_nature" class="form-control searchable"') ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="col-md-2" for="category_id">Category:  </label>
                                <div class="col-md-10">
                                <?= form_dropdown('category_id',$material_item_category_options,'',' id="filter_by_category" class="form-control searchable"') ?>
                                </div>
                            </div>

                            <div class="box-tools pull-right">
                                <button class="btn btn-xs btn-default">
                                    <i class="fa fa-print"></i> Print List
                                </button>

                                <?php if(check_privilege('Inventory Actions')){ ?>
                                <a target="_blank" class="btn btn-default btn-xs" href="<?= base_url('inventory/download_material_registration_excel_template') ?>">
                                    <i class="fa fa-file-excel-o"></i> Excel Template
                                </a>
                                <button type="button" data-toggle="modal" data-target="#new_material_item" class="btn btn-xs btn-default">
                                    <i class="fa fa-plus"></i> New Material Item
                                </button>
                                <?php } ?>
                            </div>
                        </form>
                        <div id="new_material_item" class="modal fade" role="dialog">
                            <?php $this->load->view('inventory/material/material_item_form'); ?>
                        </div>
                    </div>
                </div>
                <div class="box-body">

                    <table id="material_items_list" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Thumbnail</th><th width="25%">Item Name</th><th>Category</th><th>Part Number</th><th>Unit</th><th>Description</th><th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');