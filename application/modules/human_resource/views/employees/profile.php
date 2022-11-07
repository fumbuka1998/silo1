<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?= $employee->first_name ?>'s Profile
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <?php if(check_permission('Human Resources') && check_privilege('Employees')){ ?>
        <li><a href="<?= base_url('human_resource/human_resources/employees_lists')?>"><i class="fa fa-users"></i>Employees</a></li>
        <?php } ?>
        <li class="active"><?= $employee->first_name ?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-3">

            <!-- Profile Image -->
            <div class="box box-primary">
                <div class="box-body box-profile">
                    <img class="profile-user-img img-responsive img-circle" src="<?= $employee->avatar_path() ?>" alt="User profile picture">

                    <h3 class="profile-username text-center"><?= $employee->full_name() ?></h3>

                    <p class="text-muted text-center">
                        <?php
                            $position = $employee->position();
                            $department = $employee->department();
                            echo $employee->position_id != '' ?  $position->position_name.' - '.$department->department_name : $department->department_name;
                        ?>
                    </p>

                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>Phone</b> <a class="pull-right"><?= $employee->phone ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Alt. Phone</b> <a class="pull-right"><?= $employee->alternative_phone ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Email</b> <a class="pull-right"><?= $employee->email ?></a>
                        </li>
                        <li class="list-group-item">
                            <strong><i class="fa fa-map-marker margin-r-5"></i> Address</strong>
                            <br/><br/>
                            <p class="text-muted">
                                <?= nl2br($employee->address) ?>
                            </p>
                        </li>
                    </ul>
                    <?php
                        if(check_privilege('Register Employee')) {
                            ?>
                            <button data-toggle="modal" data-target="#edit_form" class="btn btn-block btn-primary">
                                Edit
                            </button>
                            <div id="edit_form" class="modal fade" role="dialog">
                                <?php $this->load->view('employees/employee_form', ['employee' => $employee]); ?>
                            </div>
                            <?php
                        }
                    ?>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                <?php if(check_privilege('Register Employee')){ ?>
                    <li><a href="#contracts" data-toggle="tab">Contracts</a></li>
                    <li><a href="#ssf_details" data-toggle="tab">SSF Details</a></li>
                    <li><a href="#bank_details" data-toggle="tab">Bank Details</a></li>
                    <li><a href="#loans" data-toggle="tab">Loans</a></li>
                    <?php } ?>
                    <li class="active"><a href="#user_account_details" data-toggle="tab">User Account Details</a></li>
                </ul>
                <div class="tab-content">
                    <?php if(check_privilege('Register Employee')){ ?>

                    <div class="tab-pane" id="contracts">
                        <?php $this->load->view('employees/contracts_tab'); ?>
                    </div>
                    <!-- /.tab-pane -->
                        <div class=" tab-pane" id="ssf_details">
                        <?php $this->load->view('employees/employee_ssfs/employee_ssfs_tab'); ?>
                    </div>
                    <!-- /.tab-pane -->
                      <div class=" tab-pane" id="bank_details">
                        <?php $this->load->view('employees/employee_banks/employee_banks_tab'); ?>
                    </div
                    <!-- /.tab-pane -->
                      <div class=" tab-pane" id="loans">
                        <?php $this->load->view('employees/employee_loans/employee_loan_tab'); ?>
                    </div>

                    <?php } ?>
                    <!-- /.tab-pane -->
                    <div class="active  tab-pane" id="user_account_details">
                        <?php $this->load->view('employees/user_account_tab'); ?>
                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
<?php $this->load->view('includes/footer');