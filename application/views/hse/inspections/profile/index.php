<?php
$this->load->view('includes/header');
?>

    <section class="content-header">
        <h1>
            <?= $title ?>
            <small>Inspection Job Cards</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('hse/inspection')?>"><i class="fa fa-book"></i>Inspection</a></li>
            <li class="active">Job card</li>
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
                            <button data-toggle="modal" data-target="#inspection_job_form_form" class="btn btn-default btn-xs">
                                <i class="fa fa-plus"></i> Job Card
                            </button>
                            <div id="inspection_job_form_form" class="modal fade" role="dialog">
                                <?php $this->load->view('hse/inspections/profile/job_cards/job_card_form');?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="inspection_job_cards_list" inspection_id ="<?= $inspection->{$inspection::DB_TABLE_PK} ?>" class="table table-bordered table-hover" style="table-layout: fixed">
                                <thead>
                                <tr>
                                    <th style="width: 8%">Date</th><th>Priority</th><th style="width: 50%">Description</th><th>Created By</th><th style="width: 15%"></th>
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
