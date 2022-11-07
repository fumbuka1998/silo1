<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Departments
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('human_resources')?>"><i class="fa fa-users"></i>Human Resources</a></li>
        <li class="active">Departments</li>
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
                            <button data-toggle="modal" data-target="#new_department" class="btn btn-default btn-xs">
                                <i class="fa fa-plus"></i> New Department
                            </button>
                            <div id="new_department" class="modal fade" tabindex="-1" role="dialog">
                                <?php $this->load->view('human_resources/departments/department_form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="departments_list" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Department Name</th><th>Description</th><th>No. of Employees</th><th></th>
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