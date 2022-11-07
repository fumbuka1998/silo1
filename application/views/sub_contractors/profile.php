<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?= $sub_contractor->name ?>
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('sub_contractors')?>"><i class="fa fa-shopping-cart"></i>Sub-Contractors</a></li>
        <li><a href="<?= base_url('sub_contractors/sub_contractors_list')?>"><i class="fa fa-list"></i>Sub-Contractors List</a></li>
        <li class="active"><?= $sub_contractor->name ?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#sub_contractors_details" data-toggle="tab">Sub-Contractor Details</a></li>
                    <li><a href="#sub_contracts" data-toggle="tab">Sub-Contracts</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="sub_contractors_details">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <div class="col-xs-12">
                                            <div class="box-tools pull-right">
                                                <?php $sub_contractor_id=$sub_contractor->{$sub_contractor::DB_TABLE_PK} ?>
                                                <button data-toggle="modal" data-target="#edit_form_<?= $sub_contractor_id ?>"
                                                        class="btn btn-default btn-xs">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <div id="edit_form_<?= $sub_contractor_id ?>" class="modal fade" tabindex="-1" role="dialog">
                                                    <?php $data['sub_contractor']=$sub_contractor; ?>
                                                    <?php $this->load->view('sub_contractors/sub_contractor_form',$data); ?>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="form-horizontal">

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Name:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $sub_contractor->name ?>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Phone:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $sub_contractor->phone ? $sub_contractor->phone : 'N/A' ?>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Alt. Phone:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $sub_contractor->alternative_phone ? $sub_contractor->alternative_phone : 'N/A' ?>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Email:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $sub_contractor->email ? $sub_contractor->email : 'N/A' ?>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Address:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $sub_contractor->address ? nl2br($sub_contractor->address) : 'N/A' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane"   id="sub_contracts">
                        <?php $this->load->view('sub_contractors/sub_contracts_tab'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');