<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Budgets
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('projects')?>"><i class="fa fa-product-hunt"></i>Projects</a></li>
        <li class="active">Budgets</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                      <div class="col-xs-12">
                        <table id="budgets_list" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Project Name</th><th>Reference Number</th><th>Budgeted Figure</th><th>Actual Use</th><th>Project Status</th><th></th>
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