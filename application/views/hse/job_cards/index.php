<?php
?>
<?php $this->load->view('includes/header'); ?>

    <section class="content-header">
        <h1>
            HSE
            <small>Job Cards</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li class="active">Job Cards</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="all_job_cards_list" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th style="width: 8%">Date</th><th>Priority</th><th style="width: 10%">Job Card Type</th><th style="width: 50%">Description</th><th>Created By</th><th style="width: 15%"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');