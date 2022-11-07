<?php
$this->load->view('includes/header');
$job_card_options = [
        '' => 'All',
    'Inspection' => 'Inspection Job Card',
    'Incident' => 'Incident Job Card'
]
?>
<section class="content-header">
    <h1>
        <small><?= $title ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('hse/job_card') ?>"><i class="fa fa-barcode"></i>Job Cards</a></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box" id="job_card_reports">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools">
                            <form method="post" target="_blank" action="<?= base_url('hse/job_card_reports') ?>">

                                <input name="print" type="hidden" value="true">

                                <div class="form-group col-md-3">
                                    <label for="" class="control-label">Job Card Type</label>
                                    <?= form_dropdown('job_card_type',$job_card_options,'',' class="form-control searchable"') ?>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="" class="control-label">From</label>
                                    <input class="form-control datepicker" name="from" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="" class="control-label">To</label>
                                    <input class="form-control datepicker" name="to" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="form-group col-md-2 pull-right">
                                    <br/>
                                    <button type="button" id="generate_job_card_report" class="btn btn-default btn-xs">
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
                        <div id="job_card_report_container" class="col-xs-12 table-responsive">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $this->load->view('includes/footer'); ?>
