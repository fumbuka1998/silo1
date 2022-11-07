<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 21/03/2018
 * Time: 15:24
 */
$this->load->view('includes/header'); ?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Students
            <small>Dashboard</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href=""></a></li>
            <li class="active"></li>
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
                                <button data-toggle="modal" data-target="#new_student_form"
                                        class="btn btn-default btn-xs">
                                    New Student
                                </button>
                                <div id="new_student_form" class="modal fade" tabindex="-1" role="dialog">
                                    <?php $this->load->view('students/student_form'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /.content -->
<?php $this->load->view('includes/footer'); ?>