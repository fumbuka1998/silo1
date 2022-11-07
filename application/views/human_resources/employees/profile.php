<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?= $employee->first_name ?>'s Profile
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <?php if(check_permission('Human Resources')){ ?>
        <li><a href="<?= base_url('human_resources/employees_list')?>"><i class="fa fa-users"></i>Employees</a></li>
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
                        if(check_permission('Human Resources')) {
                            ?>
                            <button data-toggle="modal" data-target="#edit_form" class="btn btn-block btn-primary">
                                Edit
                            </button>
                            <div id="edit_form" class="modal fade" role="dialog">
                                <?php $this->load->view('human_resources/employees/employee_form', ['employee' => $employee]); ?>
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
                    <li class="active"><a href="#contracts" data-toggle="tab">Contracts</a></li>
                    <li><a href="#user_account_details" data-toggle="tab">User Account Details</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="contracts">
                        <?php $this->load->view('human_resources/employees/contracts_tab'); ?>
                    </div>
                    <!-- /.tab-pane -->
                    <div class=" tab-pane" id="user_account_details">
                        <?php $this->load->view('human_resources/employees/user_account_tab'); ?>
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