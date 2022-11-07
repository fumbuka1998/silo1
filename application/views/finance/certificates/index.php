<?php $this->load->view('includes/header');?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            All Project Certificates
            <!--<small>Sub-title</small>-->
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('finance')?>"><i class="fa fa-money"></i>Finance</a></li>
            <li class="active">All Project Certificates</li>
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

                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12 table-responsive">
                                <table id="all_project_certificate_list" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Cert. No.</th><th>Cert. Date</th><th>Certified Amount</th><th>Paid Amount</th><th></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /.content -->
<?php $this->load->view('includes/footer'); ?>