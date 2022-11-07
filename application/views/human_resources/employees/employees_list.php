<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Employees List
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('human_resources')?>"><i class="fa fa-users"></i>Human Resources</a></li>
        <li class="active">Employees List</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools  pull-right">
                            <button data-toggle="modal" data-target="#employee_form" class="btn btn-default btn-xs">
                                <i class="ion-person-add"></i> New Employee
                            </button>
                            <div id="employee_form" class="modal fade" role="dialog">
                                <?php $this->load->view('human_resources/employees/employee_form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover table-striped" id="employees_list">
                            <thead>
                                <tr>
                                    <th>Full Name</th><th>Phone Number</th><th>Alternative Phone</th><th>Email</th><th>Address</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');