<?php
$this->load->view('includes/header');
?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title ?>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('hse/job_card')?>"><i class="fa fa-book"></i>Job Card</a></li>
            <li class="active">Labours && Activities</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content"><div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools pull-right">
                            <button data-toggle="modal" data-target="#labour_and_activity_form" class="btn btn-default btn-xs">
                                <i class="fa fa-plus"></i> New Labour/Activity
                            </button>
                            <div id="labour_and_activity_form" class="modal fade" role="dialog">
                                <?php $this->load->view('hse/job_cards/profile/labours_and_activities/labour_and_activity_form');?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="job_card_labours_and_activities_list" type="<?= $type ?>" job_card_id ="<?= $job_card->{$job_card::DB_TABLE_PK} ?>" class="table table-bordered table-hover" style="table-layout: fixed">
                                <thead>
                                <tr>
                                    <th style="width: 20%;"> Labour </th><th> Activity </th><th style="width: 15%;"> </th>
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
