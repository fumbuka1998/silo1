<?php
$this->load->view('includes/header');
?>
    <section class="content-header">
        <h1>
            HSE
            <small>Site Diary Compliances</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li class="active">Site Diary Compliances</li>
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
                            <button data-toggle="modal" data-target="#site_diary_compliance_form" class="btn btn-default btn-xs">
                                <i class="fa fa-plus"></i> Diary Compliance
                            </button>
                            <div id="site_diary_compliance_form" class="modal fade site_diary_compliance_form" role="dialog">
                                <?php $this->load->view('hse/site_diary_compliances/site_diary_compliance_form');?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="site_diary_compliances_list" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th> Date </th><th>Project</th><th> Supervisor </th><th style="width: 40%">Remarks</th><th style="width: 15%"></th>
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